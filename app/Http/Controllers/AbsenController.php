<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Absen;
use App\Models\Lokasi;
use App\Exports\AbsenExport;
use App\Jobs\ExportAbsenJob;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class AbsenController extends Controller
{
    /* ====================================================================
     *  HALAMAN UTAMA + EXPORT LAMA (tidak diubah, tetap berjalan)
     * ==================================================================== */

    public function absen(Request $request)
    {
        $query = Absen::with('token', 'token.lokasi', 'user')->latest();

        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->has('date_range') && !empty($request->input('date_range'))) {
            $dateRange = explode(' - ', $request->input('date_range'));

            if (count($dateRange) === 2) {
                $startDate = Carbon::parse(trim($dateRange[0]))->startOfDay();
                $endDate   = Carbon::parse(trim($dateRange[1]))->endOfDay();

                $query->whereBetween('tanggal', [$startDate, $endDate]);

                $daysInMonth = $startDate->daysInMonth;
                $monthYear   = $startDate->format('F Y');
            }
        } else {
            $tanggal     = Carbon::now()->format('Y-m-d');
            $daysInMonth = Carbon::now()->daysInMonth;
            $monthYear   = Carbon::now()->format('F Y');
        }

        if ($request->has('lokasi') && !empty($request->input('lokasi'))) {
            $lokasiId = $request->input('lokasi');
            $query->whereHas('token.lokasi', function ($q) use ($lokasiId) {
                $q->where('id', $lokasiId);
            });
        }

        if ($request->has('status_absen') && !empty($request->input('status_absen'))) {
            $statusAbsen = $request->input('status_absen');
            $query->whereHas('token', function ($q) use ($statusAbsen) {
                $q->where('status', $statusAbsen);
            });
        }

        if ($request->has('status') && !empty($request->input('status'))) {
            $statusKedatangan = $request->input('status');
            $query->where('status', $statusKedatangan);
        }

        $fileDate = $request->has('tanggal') && !empty($request->input('tanggal'))
            ? $request->input('tanggal')
            : Carbon::now()->format('Y-m-d');

        if ($request->has('export') && $request->export == 'excel') {
            $absens = $query->get();
            if ($absens->isEmpty()) {
                return redirect()->back()->with('error', 'No data available to export.');
            }

            $users  = User::with('absens')->get();
            $months = $absens->groupBy(fn ($date) => Carbon::parse($date->tanggal)->format('F Y'));

            $userLateness = [];
            $userOvertime = [];

            foreach ($months as $month => $absens) {
                $users = User::with('absens')->get();

                foreach ($users as $user) {
                    $lateCount = $absens->filter(fn ($absen) =>
                        $absen->user_id == $user->id && $absen->status == 3 && $absen->token->status == 1
                    )->count();

                    $overtimeCount = $absens->filter(fn ($absen) =>
                        $absen->user_id == $user->id && $absen->status == 3 && $absen->token->status == 2
                    )->count();

                    $userLateness[$user->id][$month] = $lateCount;
                    $userOvertime[$user->id][$month] = $overtimeCount;
                }
            }

            $fileName = 'absen-' . $fileDate . '.xlsx';
            return Excel::download(
                new AbsenExport($query->get(), $daysInMonth, $userLateness, $userOvertime),
                $fileName
            );
        }

        if ($request->has('export') && $request->export == 'pdf') {
            $absens = $query->get();
            if ($absens->isEmpty()) {
                return redirect()->back()->with('error', 'No data available to export.');
            }

            $fileName = 'absen-' . $fileDate . '.pdf';
            $absens   = $query->get();

            $absens = $absens->map(function ($absen) {
                $absen->tanggal = Carbon::parse($absen->tanggal);
                return $absen;
            });

            $users  = User::with('absens')->get();
            $months = $absens->groupBy(fn ($date) => Carbon::parse($date->tanggal)->format('F Y'));

            $userLateness = [];
            $userOvertime = [];

            foreach ($months as $month => $absens) {
                $users = User::with('absens')->get();

                foreach ($users as $user) {
                    $lateCount = $absens->filter(fn ($absen) =>
                        $absen->user_id == $user->id && $absen->status == 3 && $absen->token->status == 1
                    )->count();

                    $overtimeCount = $absens->filter(fn ($absen) =>
                        $absen->user_id == $user->id && $absen->status == 3 && $absen->token->status == 2
                    )->count();

                    $userLateness[$user->id][$month] = $lateCount;
                    $userOvertime[$user->id][$month] = $overtimeCount;
                }
            }

            $pdf = Pdf::loadView('admin.exports.absen', [
                'months'       => $months,
                'daysInMonth'  => $daysInMonth,
                'userLateness' => $userLateness,
                'userOvertime' => $userOvertime,
            ]);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download($fileName);
        }

        if ($request->has('export') && $request->export == 'print') {
            $absens = $query->get();
            if ($absens->isEmpty()) {
                return redirect()->back()->with('error', 'No data available to export.');
            }

            $fileName = 'absen-' . $fileDate . '.pdf';
            $absens   = $query->get();

            $absens = $absens->map(function ($absen) {
                $absen->tanggal = Carbon::parse($absen->tanggal);
                return $absen;
            });

            $users  = User::with('absens')->get();
            $months = $absens->groupBy(fn ($date) => Carbon::parse($date->tanggal)->format('F Y'));

            $userLateness = [];
            $userOvertime = [];

            foreach ($months as $month => $absens) {
                $users = User::with('absens')->get();

                foreach ($users as $user) {
                    $lateCount = $absens->filter(fn ($absen) =>
                        $absen->user_id == $user->id && $absen->status == 3 && $absen->token->status == 1
                    )->count();

                    $overtimeCount = $absens->filter(fn ($absen) =>
                        $absen->user_id == $user->id && $absen->status == 3 && $absen->token->status == 2
                    )->count();

                    $userLateness[$user->id][$month] = $lateCount;
                    $userOvertime[$user->id][$month] = $overtimeCount;
                }
            }

            $pdf = Pdf::loadView('admin.exports.absen', [
                'months'       => $months,
                'daysInMonth'  => $daysInMonth,
                'userLateness' => $userLateness,
                'userOvertime' => $userOvertime,
            ]);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream($fileName);
        }

        $absens  = $query->paginate(10);
        $lokasis = Lokasi::where('status', 1)->get();

        return view('admin.absen', [
            'absens'      => $absens,
            'lokasis'     => $lokasis,
            'daysInMonth' => $daysInMonth,
            'monthYear'   => $monthYear,
        ]);
    }

    /* ====================================================================
     *  EXPORT BACKGROUND JOB — METHOD BARU
     * ==================================================================== */

    /**
     * Terima request konfirmasi export, dispatch job ke queue, kembalikan jobKey.
     *
     * POST /admin/absen/export/dispatch
     */
    public function dispatchExport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:excel,pdf,print',
        ]);

        $filters = $request->only(['search', 'date_range', 'lokasi', 'status_absen', 'status']);

        // Buat unique key untuk tracking status job ini
        $jobKey = 'absen_export_' . Str::uuid();

        // Tandai sebagai pending
        Cache::put($jobKey, ['status' => 'pending'], now()->addHours(2));

        // Dispatch ke queue
        ExportAbsenJob::dispatch($request->type, $filters, $jobKey);

        return response()->json([
            'success' => true,
            'job_key' => $jobKey,
            'message' => 'Export sedang diproses di background.',
        ]);
    }

    /**
     * Cek status job berdasarkan jobKey.
     *
     * GET /admin/absen/export/status/{key}
     */
    public function exportStatus(string $key)
    {
        $status = Cache::get($key);

        if (!$status) {
            return response()->json(['status' => 'not_found'], 404);
        }

        // Jika sudah selesai, sertakan URL download
        if ($status['status'] === 'completed') {
            $status['download_url'] = route('admin.absen.export.download', ['key' => $key]);
        }

        return response()->json($status);
    }

    /**
     * Download file hasil export.
     *
     * GET /admin/absen/export/download/{key}
     */
    public function downloadExport(string $key)
    {
        $status = Cache::get($key);

        if (!$status || $status['status'] !== 'completed') {
            abort(404, 'File tidak ditemukan atau belum selesai.');
        }

        $filePath = $status['file'];

        if (!Storage::exists($filePath)) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        $headers = [
            'Content-Type' => $status['type'] === 'excel'
                ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                : 'application/pdf',
        ];

        // Untuk print: stream (inline), untuk excel/pdf: download (attachment)
        if ($status['type'] === 'print') {
            return response()->file(Storage::path($filePath), array_merge($headers, [
                'Content-Disposition' => 'inline; filename="' . $status['filename'] . '"',
            ]));
        }

        return Storage::download($filePath, $status['filename'], $headers);
    }

    /**
     * Hapus file hasil export dari storage (dipanggil saat user close toast).
     *
     * DELETE /admin/absen/export/destroy/{key}
     */
    public function destroyExport(string $key)
    {
        $status = Cache::get($key);

        if ($status && isset($status['file']) && Storage::exists($status['file'])) {
            Storage::delete($status['file']);
        }

        Cache::forget($key);

        return response()->json(['success' => true]);
    }
}