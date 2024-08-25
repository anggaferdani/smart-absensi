<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'day');
        $lokasiFilter = $request->input('lokasi', '');

        $absensMasuk = Absen::query();
        $absensPulang = Absen::query();

        if ($filter === 'month') {
            $absensMasuk = $absensMasuk->whereYear('tanggal', date('Y'))->whereMonth('tanggal', date('m'));
            $absensPulang = $absensPulang->whereYear('tanggal', date('Y'))->whereMonth('tanggal', date('m'));
        } elseif ($filter === 'year') {
            $absensMasuk = $absensMasuk->whereYear('tanggal', date('Y'));
            $absensPulang = $absensPulang->whereYear('tanggal', date('Y'));
        } else {
            $absensMasuk = $absensMasuk->whereDate('tanggal', date('Y-m-d'));
            $absensPulang = $absensPulang->whereDate('tanggal', date('Y-m-d'));
        }

        if ($lokasiFilter) {
            $absensMasuk = $absensMasuk->whereHas('token', function ($query) use ($lokasiFilter) {
                $query->where('lokasi_id', $lokasiFilter);
            });
            $absensPulang = $absensPulang->whereHas('token', function ($query) use ($lokasiFilter) {
                $query->where('lokasi_id', $lokasiFilter);
            });
        }

        $statusesMasuk = $absensMasuk->whereHas('token', function ($query) {
            $query->where('status', 1);
        })->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $statusesPulang = $absensPulang->whereHas('token', function ($query) {
            $query->where('status', 2);
        })->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $totalMasuk = $statusesMasuk->sum();
        $totalPulang = $statusesPulang->sum();

        $statusLabels = [
            1 => 'Lebih Awal',
            2 => 'Tepat Waktu',
            3 => 'Terlambat'
        ];

        $labeledStatusesMasuk = $statusesMasuk->mapWithKeys(function ($count, $status) use ($statusLabels) {
            return [$statusLabels[$status] => $count];
        });

        $labeledStatusesPulang = $statusesPulang->mapWithKeys(function ($count, $status) use ($statusLabels) {
            return [$statusLabels[$status] => $count];
        });

        $lokasis = Lokasi::where('status', 1)->get();

        return view('admin.dashboard', [
            'filter' => $filter,
            'statusesMasuk' => $labeledStatusesMasuk,
            'statusesPulang' => $labeledStatusesPulang,
            'totalMasuk' => $totalMasuk,
            'totalPulang' => $totalPulang,
            'lokasis' => $lokasis,
        ]);
    }
}
