<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Absen;
use App\Models\Lokasi;
use App\Exports\AbsenExport;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class AbsenController extends Controller
{
    public function absen(Request $request) {
        $query = Absen::with('token', 'token.lokasi', 'user')->latest();
        
        // Search filters
        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
            });
        }
    
        // Month filter
        if ($request->has('bulan') && !empty($request->input('bulan'))) {
            $bulan = $request->input('bulan');
            $query->whereMonth('tanggal', Carbon::parse($bulan)->month)
                  ->whereYear('tanggal', Carbon::parse($bulan)->year);
            $daysInMonth = Carbon::parse($bulan)->daysInMonth;
            $monthYear = Carbon::parse($bulan)->format('F Y');
        } else {
            $tanggal = Carbon::now()->format('Y-m-d');
            $daysInMonth = Carbon::now()->daysInMonth;
            $monthYear = Carbon::now()->format('F Y');
        }
        
        // Date filter
        if ($request->has('tanggal') && !empty($request->input('tanggal'))) {
            $tanggal = $request->input('tanggal');
            $daysInMonth = Carbon::parse($tanggal)->daysInMonth;
            $monthYear = Carbon::parse($tanggal)->format('F Y');
            $query->whereDate('tanggal', $tanggal);
        } else {
            $tanggal = Carbon::now()->format('Y-m-d');
            $daysInMonth = Carbon::now()->daysInMonth;
            $monthYear = Carbon::now()->format('F Y');
        }
        
        // Location filter
        if ($request->has('lokasi') && !empty($request->input('lokasi'))) {
            $lokasiId = $request->input('lokasi');
            $query->whereHas('token.lokasi', function ($q) use ($lokasiId) {
                $q->where('id', $lokasiId);
            });
        }
        
        // Status filters
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
        
        // Function to gather user lateness and overtime by month/year
        $this->collectUserStats($query, $request);
    
        if ($request->has('export')) {
            $absens = $query->get();
            if ($absens->isEmpty()) {
                return redirect()->back()->with('error', 'No data available to export.');
            }
    
            // Prepare user statistics
            $userStats = $this->getUserStats($absens);
    
            switch ($request->export) {
                case 'excel':
                    return $this->exportToExcel($absens, $userStats, $fileDate, $daysInMonth);
                case 'pdf':
                    return $this->exportToPDF($absens, $userStats, $fileDate, $daysInMonth);
                case 'print':
                    return $this->printReport($absens, $userStats, $fileDate, $daysInMonth);
            }
        }
        
        $absens = $query->paginate(10);
        $lokasis = Lokasi::where('status', 1)->get();
    
        return view('admin.absen', [
            'absens' => $absens,
            'lokasis' => $lokasis,
            'daysInMonth' => $daysInMonth,
            'monthYear' => $monthYear
        ]);
    }
    
    private function collectUserStats($query, Request $request) {
        // You can move this code to gather user lateness and overtime here for efficiency.
        // Consider grouping by month and year in this method if needed.
    }
    
    private function getUserStats($absens) {
        $userLateness = [];
        $userOvertime = [];
    
        foreach ($absens as $absen) {
            $userId = $absen->user_id;
    
            // Count lateness and overtime
            if ($absen->status == 3) {
                if ($absen->token->status == 1) {
                    $userLateness[$userId] = ($userLateness[$userId] ?? 0) + 1;
                } elseif ($absen->token->status == 2) {
                    $userOvertime[$userId] = ($userOvertime[$userId] ?? 0) + 1;
                }
            }
        }
    
        return ['lateness' => $userLateness, 'overtime' => $userOvertime];
    }
    
    private function exportToExcel($absens, $userStats, $fileDate, $daysInMonth) {
        // Implement your Excel export logic here, passing $userStats for userLateness and userOvertime
        return Excel::download(
            new AbsenExport($absens, $daysInMonth, $userStats['lateness'], $userStats['overtime']),
            'absen-' . $fileDate . '.xlsx'
        );
    }
    
    private function exportToPDF($absens, $userStats, $fileDate, $daysInMonth) {
        // Implement your PDF export logic here, passing $userStats for userLateness and userOvertime
        $pdf = Pdf::loadView('admin.exports.absen', [
            'absens' => $absens,
            'userLateness' => $userStats['lateness'],
            'userOvertime' => $userStats['overtime'],
            'daysInMonth' => $daysInMonth,
        ]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('absen-' . $fileDate . '.pdf');
    }
    
    private function printReport($absens, $userStats, $fileDate, $daysInMonth) {
        // Implement your print logic here, passing $userStats for userLateness and userOvertime
        $pdf = Pdf::loadView('admin.exports.absen', [
            'absens' => $absens,
            'userLateness' => $userStats['lateness'],
            'userOvertime' => $userStats['overtime'],
            'daysInMonth' => $daysInMonth,
        ]);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('absen-' . $fileDate . '.pdf');
    }
    
}
