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
        
        // Handle search filters
        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
            });
        }
    
        // Handle month filter
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
        
        // Handle date filter
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
        
        // Handle location filter
        if ($request->has('lokasi') && !empty($request->input('lokasi'))) {
            $lokasiId = $request->input('lokasi');
            $query->whereHas('token.lokasi', function ($q) use ($lokasiId) {
                $q->where('id', $lokasiId);
            });
        }
        
        // Handle attendance status filter
        if ($request->has('status_absen') && !empty($request->input('status_absen'))) {
            $statusAbsen = $request->input('status_absen');
            $query->whereHas('token', function ($q) use ($statusAbsen) {
                $q->where('status', $statusAbsen);
            });
        }
        
        // Handle arrival status filter
        if ($request->has('status') && !empty($request->input('status'))) {
            $statusKedatangan = $request->input('status');
            $query->where('status', $statusKedatangan);
        }
        
        // Set file date for exports
        $fileDate = $request->has('tanggal') && !empty($request->input('tanggal'))
                ? $request->input('tanggal')
                : Carbon::now()->format('Y-m-d');
        
        // Handle export to Excel
        if ($request->has('export') && $request->export == 'excel') {
            $absens = $query->get();
            if ($absens->isEmpty()) {
                return redirect()->back()->with('error', 'No data available to export.');
            }
    
            $userLateness = [];
            $userOvertime = [];
            // Calculate lateness and overtime per user for the specified month and year
            $users = User::with('absens')->get();
            foreach ($users as $user) {
                $userAbsens = $user->absens()->whereMonth('tanggal', Carbon::parse($fileDate)->month)
                                                  ->whereYear('tanggal', Carbon::parse($fileDate)->year)
                                                  ->get();
                $lateCount = $userAbsens->filter(function($absen) {
                    return $absen->status == 3 && $absen->token->status == 1;
                })->count();
                $overtimeCount = $userAbsens->filter(function($absen) {
                    return $absen->status == 3 && $absen->token->status == 2;
                })->count();
                $userLateness[$user->id] = $lateCount;
                $userOvertime[$user->id] = $overtimeCount;
            }
    
            $fileName = 'absen-' . $fileDate . '.xlsx';
            return Excel::download(
                new AbsenExport($query->get(), $daysInMonth, $userLateness, $userOvertime),
                $fileName
            );
        }
    
        // Handle export to PDF
        if ($request->has('export') && $request->export == 'pdf') {
            $absens = $query->get();
            if ($absens->isEmpty()) {
                return redirect()->back()->with('error', 'No data available to export.');
            }
            
            $fileName = 'absen-' . $fileDate . '.pdf';
            $absens = $absens->map(function($absen) {
                $absen->tanggal = Carbon::parse($absen->tanggal);
                return $absen;
            });
            
            $userLateness = [];
            $userOvertime = [];
            $users = User::with('absens')->get();
            foreach ($users as $user) {
                $userAbsens = $user->absens()->whereMonth('tanggal', Carbon::parse($fileDate)->month)
                                                  ->whereYear('tanggal', Carbon::parse($fileDate)->year)
                                                  ->get();
                $lateCount = $userAbsens->filter(function($absen) {
                    return $absen->status == 3 && $absen->token->status == 1;
                })->count();
                $overtimeCount = $userAbsens->filter(function($absen) {
                    return $absen->status == 3 && $absen->token->status == 2;
                })->count();
                $userLateness[$user->id] = $lateCount;
                $userOvertime[$user->id] = $overtimeCount;
            }
            
            $months = $absens->groupBy(function($date) {
                return Carbon::parse($date->tanggal)->format('F Y');
            });
            
            $pdf = Pdf::loadView('admin.exports.absen', [
                'months' => $months,
                'daysInMonth' => $daysInMonth,
                'userLateness' => $userLateness,
                'userOvertime' => $userOvertime,
            ]);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download($fileName);
        }
    
        // Handle export for printing
        if ($request->has('export') && $request->export == 'print') {
            $absens = $query->get();
            if ($absens->isEmpty()) {
                return redirect()->back()->with('error', 'No data available to export.');
            }
    
            $fileName = 'absen-' . $fileDate . '.pdf';
            $absens = $absens->map(function($absen) {
                $absen->tanggal = Carbon::parse($absen->tanggal);
                return $absen;
            });
            
            $userLateness = [];
            $userOvertime = [];
            $users = User::with('absens')->get();
            foreach ($users as $user) {
                $userAbsens = $user->absens()->whereMonth('tanggal', Carbon::parse($fileDate)->month)
                                                  ->whereYear('tanggal', Carbon::parse($fileDate)->year)
                                                  ->get();
                $lateCount = $userAbsens->filter(function($absen) {
                    return $absen->status == 3 && $absen->token->status == 1;
                })->count();
                $overtimeCount = $userAbsens->filter(function($absen) {
                    return $absen->status == 3 && $absen->token->status == 2;
                })->count();
                $userLateness[$user->id] = $lateCount;
                $userOvertime[$user->id] = $overtimeCount;
            }
            
            $months = $absens->groupBy(function($date) {
                return Carbon::parse($date->tanggal)->format('F Y');
            });
            
            $pdf = Pdf::loadView('admin.exports.absen', [
                'months' => $months,
                'daysInMonth' => $daysInMonth,
                'userLateness' => $userLateness,
                'userOvertime' => $userOvertime,
            ]);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream($fileName);
        }
        
        // Pagination for the regular view
        $absens = $query->paginate(10);
        $lokasis = Lokasi::all();
        return view('admin.absen', compact('absens', 'lokasis', 'monthYear'));
    }
}
