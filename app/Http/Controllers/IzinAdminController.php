<?php

namespace App\Http\Controllers;

use App\Exports\IzinExport;
use Carbon\Carbon;
use App\Models\Izin;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class IzinAdminController extends Controller
{
    public function izin(Request $request) {
        $query = Izin::with('user')
                ->orderByRaw('CASE WHEN status_izin = 1 THEN 0 ELSE 1 END')
                ->latest();
    
        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where('kode', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('tanggal') && !empty($request->input('tanggal'))) {
            $tanggal = $request->input('tanggal');
            $query->whereDate('tanggal', $tanggal);
        }

        if ($request->has('status') && !empty($request->input('status'))) {
            $status = $request->input('status');
            $query->where('status_izin', $status);
        }

        $fileDate = $request->has('tanggal') && !empty($request->input('tanggal'))
                ? $request->input('tanggal')
                : Carbon::now()->format('Y-m-d');

        if ($request->has('export') && $request->export == 'excel') {
            $fileName = 'izin-' . $fileDate . '.xlsx';
            return Excel::download(new IzinExport($query->get()), $fileName);
        }
    
        if ($request->has('export') && $request->export == 'pdf') {
            $fileName = 'izin-' . $fileDate . '.pdf';
            $izins = $query->get();
            $pdf = Pdf::loadView('admin.exports.izin', compact('izins'));
            return $pdf->download($fileName);
        }
    
        $izins = $query->paginate(10);
    
        return view('admin.izin', compact('izins'));
    }

    public function approve($id) {
        try {
            $izin = Izin::find($id);

            $izin->update([
                'status_izin' => 2,
            ]);

            return redirect()->back()->with('success', 'Success.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function reject($id) {
        try {
            $izin = Izin::find($id);

            $izin->update([
                'status_izin' => 3,
            ]);

            return redirect()->back()->with('success', 'Success.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
