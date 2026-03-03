<?php

namespace App\Jobs;

use App\Exports\IzinExport;
use App\Models\ExportHistory;
use App\Models\Izin;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExportSakitJob implements ShouldQueue
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

        $query = Izin::with('user')
            ->where('status_izin', 2)
            ->where('status', 1);

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', '%' . $search . '%')
                    ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
            });
        }

        if (!empty($this->filters['date_range'])) {
            [$dari, $sampai] = explode(' - ', $this->filters['date_range']);
            $query->whereBetween('dari', [trim($dari), trim($sampai)]);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status_process', $this->filters['status']);
        }

        $izins = collect();
        $query->chunk(500, function ($chunk) use (&$izins) {
            $izins = $izins->merge($chunk);
        });

        $filePath = 'exports/' . $this->exportHistory->file_name;

        if ($this->exportHistory->type == 'excel') {
            Excel::store(new IzinExport($izins), $filePath, 'public');
        } else {
            $pdf = Pdf::loadView('admin.exports.izin', compact('izins'));
            Storage::disk('public')->put($filePath, $pdf->output());
        }

        $this->exportHistory->update(['file_path' => $filePath, 'status' => 0]);
    }
}