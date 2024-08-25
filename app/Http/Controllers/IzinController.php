<?php

namespace App\Http\Controllers;

use App\Models\Izin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IzinController extends Controller
{
    public function index() {
        $izins = Izin::with('user')->where('user_id', Auth::id())->where('status', 1)->latest()->paginate(5);
        return view('user.izin', compact(
            'izins',
        ));
    }

    public function create() {}

    public function store(Request $request) {
        $request->validate([
            'keterangan' => 'required',
            'tanggal' => 'required',
            'jangka_waktu' => 'required',
        ]);

        try {
            $kode = $this->generateKodeIzin();

            $arrayIzin = [
                'user_id' => Auth::id(),
                'kode' => $kode,
                'keterangan' => $request['keterangan'],
                'tanggal' => $request['tanggal'],
                'jangka_waktu' => $request['jangka_waktu'],
            ];

            Izin::create($arrayIzin);

            return redirect()->back()->with('success', 'Success.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function show($id) {}

    public function edit($id) {}

    public function update(Request $request, $id) {
        $request->validate([
            'keterangan' => 'required',
            'tanggal' => 'required',
            'jangka_waktu' => 'required',
        ]);

        try {
            $izin = Izin::find($id);
    
            $array = [
                'keterangan' => $request['keterangan'],
                'tanggal' => $request['tanggal'],
                'jangka_waktu' => $request['jangka_waktu'],
            ];

            $izin->update($array);
    
            return redirect()->back()->with('success', 'Success.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
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

    private function generateKodeIzin() {
        do {
            $kode = mt_rand(100000000000, 999999999999);
            
            $exists = Izin::where('kode', $kode)->exists();
        } while ($exists);
    
        return $kode;
    }
}
