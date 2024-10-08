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
    public function index(Request $request) {
        $query = Izin::with('user')
                ->where('status_izin', 1)
                ->orderByRaw('CASE WHEN status_process = 1 THEN 0 ELSE 1 END')
                ->where('status', 1)
                ->latest();
    
        if ($request->has('search') && !empty($request->input('search'))) {
            $search = $request->input('search');
            $query->where('kode', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
            });
        }

        $dateRange = $request->input('date_range');
        if (!empty($dateRange)) {
            [$dari, $sampai] = explode(' - ', $dateRange);
            $query->whereBetween('dari', [$dari, $sampai]);
        }

        if ($request->has('status') && !empty($request->input('status'))) {
            $status = $request->input('status');
            $query->where('status_process', $status);
        }

        $fileDate = Carbon::now()->format('Y-m-d');

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

        if ($request->has('export') && $request->export == 'print') {
            $fileName = 'izin-' . $fileDate . '.pdf';
            $izins = $query->get();
            $pdf = Pdf::loadView('admin.exports.izin', compact('izins'));
            return $pdf->stream($fileName);
        }
    
        $izins = $query->paginate(10);
    
        return view('admin.izin.izin', compact('izins'));
    }

    public function show($kode, Request $request) {
        $izin = Izin::where('kode', $kode)->first();
        $fileDate = Carbon::now()->format('Y-m-d');

        if ($request->has('export') && $request->export == 'pdf') {
            $fileName = 'izin-' . $fileDate . '.pdf';
            $pdf = Pdf::loadView('admin.izin.pdf', compact('izin'));
            return $pdf->stream($fileName);
        }

        return view('admin.izin.show', compact('izin'));
    }

    public function destroy($id) {
        try {
            $izin = Izin::find($id);

            $izin->update([
                'status' => 2,
            ]);

            return redirect()->back()->with('success', 'Success.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function approve($id) {
        try {
            $izin = Izin::find($id);

            $izin->update([
                'status_process' => 2,
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
                'status_process' => 3,
            ]);

            return redirect()->back()->with('success', 'Success.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
