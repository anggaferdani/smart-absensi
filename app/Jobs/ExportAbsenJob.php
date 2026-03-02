<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Absen;
use App\Exports\AbsenExport;
use Illuminate\Bus\Queueable;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportAbsenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah percobaan ulang jika job gagal.
     */
    public int $tries = 1;

    /**
     * Timeout job dalam detik.
     */
    public int $timeout = 300;

    protected string $type;
    protected array  $filters;
    protected string $jobKey;

    /**
     * @param string $type    'excel' | 'pdf' | 'print'
     * @param array  $filters Query filters dari request (search, date_range, lokasi, status_absen, status)
     * @param string $jobKey  Cache key unik untuk tracking status
     */
    public function __construct(string $type, array $filters, string $jobKey)
    {
        $this->type    = $type;
        $this->filters = $filters;
        $this->jobKey  = $jobKey;
    }

    /* ------------------------------------------------------------------ */
    /*  HANDLE                                                              */
    /* ------------------------------------------------------------------ */

    public function handle(): void
    {
        try {
            // Tandai sedang diproses
            Cache::put($this->jobKey, ['status' => 'processing'], now()->addHours(2));

            /* -------- Build query (sama persis dengan AbsenController) -------- */
            $query = Absen::with('token', 'token.lokasi', 'user')->latest();

            if (!empty($this->filters['search'])) {
                $search = $this->filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('kode', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      });
                });
            }

            $daysInMonth = Carbon::now()->daysInMonth;

            if (!empty($this->filters['date_range'])) {
                $dateRange = explode(' - ', $this->filters['date_range']);
                if (count($dateRange) === 2) {
                    $startDate   = Carbon::parse(trim($dateRange[0]))->startOfDay();
                    $endDate     = Carbon::parse(trim($dateRange[1]))->endOfDay();
                    $daysInMonth = $startDate->daysInMonth;
                    $query->whereBetween('tanggal', [$startDate, $endDate]);
                }
            }

            if (!empty($this->filters['lokasi'])) {
                $lokasiId = $this->filters['lokasi'];
                $query->whereHas('token.lokasi', function ($q) use ($lokasiId) {
                    $q->where('id', $lokasiId);
                });
            }

            if (!empty($this->filters['status_absen'])) {
                $statusAbsen = $this->filters['status_absen'];
                $query->whereHas('token', function ($q) use ($statusAbsen) {
                    $q->where('status', $statusAbsen);
                });
            }

            if (!empty($this->filters['status'])) {
                $query->where('status', $this->filters['status']);
            }

            $absens = $query->get();

            if ($absens->isEmpty()) {
                Cache::put($this->jobKey, [
                    'status'  => 'failed',
                    'message' => 'Tidak ada data untuk diekspor.',
                ], now()->addHours(2));
                return;
            }

            /* -------- Hitung lateness & overtime (sama persis) -------- */
            $months      = $absens->groupBy(fn ($a) => Carbon::parse($a->tanggal)->format('F Y'));
            $userLateness = [];
            $userOvertime = [];

            foreach ($months as $month => $monthAbsens) {
                foreach (User::all() as $user) {
                    $userLateness[$user->id][$month] = $monthAbsens->filter(
                        fn ($a) => $a->user_id == $user->id && $a->status == 3 && $a->token->status == 1
                    )->count();

                    $userOvertime[$user->id][$month] = $monthAbsens->filter(
                        fn ($a) => $a->user_id == $user->id && $a->status == 3 && $a->token->status == 2
                    )->count();
                }
            }

            /* -------- Buat direktori exports jika belum ada -------- */
            if (!Storage::exists('exports')) {
                Storage::makeDirectory('exports');
            }

            $fileDate = Carbon::now()->format('Y-m-d-His');

            /* ==================== EXCEL ==================== */
            if ($this->type === 'excel') {
                $storagePath = 'exports/absen-' . $fileDate . '.xlsx';

                Excel::store(
                    new AbsenExport($absens, $daysInMonth, $userLateness, $userOvertime),
                    $storagePath
                );

                Cache::put($this->jobKey, [
                    'status'   => 'completed',
                    'file'     => $storagePath,
                    'filename' => 'absen-' . $fileDate . '.xlsx',
                    'type'     => 'excel',
                ], now()->addHours(2));

            /* ==================== PDF / PRINT ==================== */
            } elseif (in_array($this->type, ['pdf', 'print'])) {
                $storagePath = 'exports/absen-' . $fileDate . '.pdf';

                $absensForPdf = $absens->map(function ($absen) {
                    $absen->tanggal = Carbon::parse($absen->tanggal);
                    return $absen;
                });

                $monthsPdf = $absensForPdf->groupBy(fn ($a) => Carbon::parse($a->tanggal)->format('F Y'));

                $pdf = Pdf::loadView('admin.exports.absen', [
                    'months'       => $monthsPdf,
                    'daysInMonth'  => $daysInMonth,
                    'userLateness' => $userLateness,
                    'userOvertime' => $userOvertime,
                ]);
                $pdf->setPaper('A4', 'landscape');

                Storage::put($storagePath, $pdf->output());

                Cache::put($this->jobKey, [
                    'status'   => 'completed',
                    'file'     => $storagePath,
                    'filename' => 'absen-' . $fileDate . '.pdf',
                    'type'     => $this->type,
                ], now()->addHours(2));
            }

        } catch (\Throwable $e) {
            Cache::put($this->jobKey, [
                'status'  => 'failed',
                'message' => 'Export gagal: ' . $e->getMessage(),
            ], now()->addHours(2));
        }
    }

    /* ------------------------------------------------------------------ */
    /*  GAGAL                                                               */
    /* ------------------------------------------------------------------ */

    public function failed(\Throwable $exception): void
    {
        Cache::put($this->jobKey, [
            'status'  => 'failed',
            'message' => 'Job gagal: ' . $exception->getMessage(),
        ], now()->addHours(2));
    }
}