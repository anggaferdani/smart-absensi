<?php

namespace App\Jobs;

use App\Exports\AbsenExport;
use App\Models\Absen;
use App\Models\ExportHistory;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExportAbsenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filters;
    protected $exportHistory;

    public function __construct($filters, ExportHistory $exportHistory)
    {
        $this->filters = $filters;
        $this->exportHistory = $exportHistory;
    }

    public function handle()
    {
        set_time_limit(0);

        $query = Absen::with('token', 'token.lokasi', 'user');

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', '%' . $search . '%')
                ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
            });
        }

        if (!empty($this->filters['date_range'])) {
            $dateRange = explode(' - ', $this->filters['date_range']);
            $startDate = Carbon::parse(trim($dateRange[0]))->startOfDay();
            $endDate   = Carbon::parse(trim($dateRange[1]))->endOfDay();
            $query->whereBetween('tanggal', [$startDate, $endDate]);
            $daysInMonth = $startDate->daysInMonth;
        } else {
            $daysInMonth = Carbon::now()->daysInMonth;
        }

        if (!empty($this->filters['lokasi'])) {
            $query->whereHas('token.lokasi', fn($q) => $q->where('id', $this->filters['lokasi']));
        }

        if (!empty($this->filters['status_absen'])) {
            $query->whereHas('token', fn($q) => $q->where('status', $this->filters['status_absen']));
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        $absens = collect();
        $query->chunk(500, function ($chunk) use (&$absens) {
            $absens = $absens->merge($chunk);
        });

        $fileName = $this->exportHistory->file_name;
        $filePath = 'exports/' . $fileName;

        $months = $absens->groupBy(fn($date) => Carbon::parse($date->tanggal)->format('F Y'));

        $userLateness = [];
        $userOvertime = [];
        foreach ($months as $month => $monthAbsens) {
            foreach ($monthAbsens->groupBy('user_id') as $userId => $userAbsens) {
                $userLateness[$userId][$month] = $userAbsens->filter(
                    fn($a) => $a->status == 3 && $a->token->status == 1
                )->count();
                $userOvertime[$userId][$month] = $userAbsens->filter(
                    fn($a) => $a->status == 3 && $a->token->status == 2
                )->count();
            }
        }

        if ($this->exportHistory->type == 'excel') {
            Excel::store(
                new AbsenExport($absens, $daysInMonth, $userLateness, $userOvertime),
                $filePath,
                'public'
            );
        } else {
            $pdf = Pdf::loadView('admin.exports.absen', compact('months', 'daysInMonth', 'userLateness', 'userOvertime'));
            $pdf->setPaper('A4', 'landscape');
            Storage::disk('public')->put($filePath, $pdf->output());
        }

        $this->exportHistory->update(['file_path' => $filePath, 'status' => 0]);
    }
}