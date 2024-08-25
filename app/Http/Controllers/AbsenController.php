<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
            $query->whereDate('tanggal', $tanggal);
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
    
        if ($request->has('status_kedatangan') && !empty($request->input('status_kedatangan'))) {
            $statusKedatangan = $request->input('status_kedatangan');
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
            $pdf = Pdf::loadView('admin.exports.absen', compact('absens'));
            return $pdf->download($fileName);
        }
    
        $absens = $query->paginate(10);
    
        $lokasis = Lokasi::where('status', 1)->get();
    
        return view('admin.absen', compact('absens', 'lokasis'));
    }
}
