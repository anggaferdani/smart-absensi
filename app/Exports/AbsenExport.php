<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AbsenExport implements FromView, WithMultipleSheets
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

        return view('admin.exports.excel', [
            'months' => $months,
            'daysInMonth' => $this->daysInMonth,
            'userLateness' => $this->userLateness,
            'userOvertime' => $this->userOvertime,
        ]);
    }

    public function sheets(): array
    {
        $months = $this->absens->groupBy(function($date) {
            return Carbon::parse($date->tanggal)->format('F Y');
        });

        $sheets = [];

        foreach ($months as $month => $absens) {
            $userLateness = [];
            $userOvertime = [];

            foreach ($absens as $absen) {
                $userLateness[$absen->user_id][$month] = $absen->status == 3 ? 1 : 0;
                $userOvertime[$absen->user_id][$month] = $absen->token->status == 2 ? 1 : 0;
            }

            $sheets[] = new MonthSheet($month, $absens, $this->daysInMonth, $userLateness, $userOvertime);
        }

        return $sheets;
    }
}

class MonthSheet implements FromView, WithTitle
{
    protected $month;
    protected $absens;
    protected $daysInMonth;
    protected $userLateness;
    protected $userOvertime;

    public function __construct($month, $absens, $daysInMonth, $userLateness, $userOvertime)
    {
        $this->month = $month;
        $this->absens = $absens;
        $this->daysInMonth = $daysInMonth;
        $this->userLateness = $userLateness;
        $this->userOvertime = $userOvertime;
    }

    public function view(): View
    {
        return view('admin.exports.excel', [
            'month' => $this->month,
            'absens' => $this->absens,
            'daysInMonth' => $this->daysInMonth,
            'userLateness' => $this->userLateness,
            'userOvertime' => $this->userOvertime,
        ]);
    }

    public function title(): string
    {
        return $this->month;
    }
}
