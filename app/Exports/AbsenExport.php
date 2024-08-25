<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AbsenExport implements FromView
{
    protected $absens;

    public function __construct($absens)
    {
        $this->absens = $absens;
    }

    public function view(): View
    {
        return view('admin.exports.absen', [
            'absens' => $this->absens
        ]);
    }
}