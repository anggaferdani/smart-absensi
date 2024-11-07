<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BackupAbsenExport implements FromView
{
    protected $absens;
    protected $daysInMonth;
    protected $userLateness;
    protected $userOvertime;

    public function __construct($absens, $daysInMonth, $userLateness, $userOvertime)
    {
        $this->absens = $absens;
        $this->daysInMonth = $daysInMonth;
        $this->userLateness = $userLateness;
        $this->userOvertime = $userOvertime;
    }

    public function view(): View
    {
        $months = $this->absens->groupBy(function($date) {
            return Carbon::parse($date->tanggal)->format('F Y');
        });

        return view('admin.exports.absen', [
            'months' => $months,
            'daysInMonth' => $this->daysInMonth,
            'userLateness' => $this->userLateness,
            'userOvertime' => $this->userOvertime,
        ]);
    }
}