<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class IzinExport implements FromView
{
    protected $izins;

    public function __construct($izins)
    {
        $this->izins = $izins;
    }

    public function view(): View
    {
        return view('admin.exports.izin', [
            'izins' => $this->izins
        ]);
    }
}