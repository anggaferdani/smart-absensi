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
        
        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where('kode', 'like', '%' . $search . '%');
        }
        
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
            $fileName = 'absen-' . $fileDate . '.xlsx';
            return Excel::download(new AbsenExport($query->get()), $fileName);
        }
        
        if ($request->has('export') && $request->export == 'pdf') {
            $fileName = 'absen-' . $fileDate . '.pdf';
            $absens = $query->get();
    
            $absens = $absens->map(function($absen) {
                $absen->tanggal = Carbon::parse($absen->tanggal);
                return $absen;
            });
    
            $users = User::with('absens')->get();
            
            $userLateness = $users->mapWithKeys(function($user) {
                $lateCount = $user->absens->filter(function($absen) {
                    return $absen->status == 3 && $absen->token->status == 1;
                })->count();
                return [$user->id => $lateCount];
            });
    
            $userOvertime = $users->mapWithKeys(function($user) {
                $overtime = $user->absens->filter(function($absen) {
                    return $absen->status == 3 && $absen->token->status == 2;
                })->count();
                return [$user->id => $overtime];
            });
    
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
