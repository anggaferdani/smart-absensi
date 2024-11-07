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

class BackupAbsenController extends Controller
{
    public function absen(Request $request) {
        $query = Absen::with('token', 'token.lokasi', 'user')->latest();
        
        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->has('date_range') && !empty($request->input('date_range'))) {
            $dateRange = explode(' - ', $request->input('date_range'));
            
            if (count($dateRange) === 2) {
                $startDate = Carbon::parse(trim($dateRange[0]))->startOfDay();
                $endDate = Carbon::parse(trim($dateRange[1]))->endOfDay();
                
                $query->whereBetween('tanggal', [$startDate, $endDate]);
                
                $daysInMonth = $startDate->daysInMonth;
                $monthYear = $startDate->format('F Y');
            }
        } else {
            $tanggal = Carbon::now()->format('Y-m-d');
            $daysInMonth = Carbon::now()->daysInMonth;
            $monthYear = Carbon::now()->format('F Y');
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

            $users = User::with('absens')->get();

            $months = $absens->groupBy(function($date) {
                return Carbon::parse($date->tanggal)->format('F Y');
            });
            
            $userLateness = [];
            $userOvertime = [];

            foreach ($months as $month => $absens) {
                $users = User::with('absens')->get();

                foreach ($users as $user) {
                    $lateCount = $absens->filter(function ($absen) use ($user) {
                        return $absen->user_id == $user->id && $absen->status == 3 && $absen->token->status == 1;
                    })->count();

                    $overtimeCount = $absens->filter(function ($absen) use ($user) {
                        return $absen->user_id == $user->id && $absen->status == 3 && $absen->token->status == 2;
                    })->count();

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
            $absens = $query->get();
    
            $absens = $absens->map(function($absen) {
                $absen->tanggal = Carbon::parse($absen->tanggal);
                return $absen;
            });
    
            $users = User::with('absens')->get();
    
            $months = $absens->groupBy(function($date) {
                return Carbon::parse($date->tanggal)->format('F Y');
            });

            $userLateness = [];
            $userOvertime = [];

            foreach ($months as $month => $absens) {
                $users = User::with('absens')->get();

                foreach ($users as $user) {
                    $lateCount = $absens->filter(function ($absen) use ($user) {
                        return $absen->user_id == $user->id && $absen->status == 3 && $absen->token->status == 1;
                    })->count();

                    $overtimeCount = $absens->filter(function ($absen) use ($user) {
                        return $absen->user_id == $user->id && $absen->status == 3 && $absen->token->status == 2;
                    })->count();

                    $userLateness[$user->id][$month] = $lateCount;
                    $userOvertime[$user->id][$month] = $overtimeCount;
                }
            }
            
            $pdf = Pdf::loadView('admin.exports.absen', [
                'months' => $months,
                'daysInMonth' => $daysInMonth,
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
            $absens = $query->get();
    
            $absens = $absens->map(function($absen) {
                $absen->tanggal = Carbon::parse($absen->tanggal);
                return $absen;
            });
    
            $users = User::with('absens')->get();
    
            $months = $absens->groupBy(function($date) {
                return Carbon::parse($date->tanggal)->format('F Y');
            });

            $userLateness = [];
                $userOvertime = [];

                foreach ($months as $month => $absens) {
                    $users = User::with('absens')->get();

                    foreach ($users as $user) {
                        $lateCount = $absens->filter(function ($absen) use ($user) {
                            return $absen->user_id == $user->id && $absen->status == 3 && $absen->token->status == 1;
                        })->count();

                        $overtimeCount = $absens->filter(function ($absen) use ($user) {
                            return $absen->user_id == $user->id && $absen->status == 3 && $absen->token->status == 2;
                        })->count();

                        $userLateness[$user->id][$month] = $lateCount;
                        $userOvertime[$user->id][$month] = $overtimeCount;
                    }
                }
            
            $pdf = Pdf::loadView('admin.exports.absen', [
                'months' => $months,
                'daysInMonth' => $daysInMonth,
                'userLateness' => $userLateness,
                'userOvertime' => $userOvertime,
            ]);
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream($fileName);
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
}
