<?php

namespace App\Http\Controllers;

use App\Models\Izin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SakitController extends Controller
{
    public function index() {
        $izins = Izin::with('user')->where('user_id', Auth::id())->where('status_izin', 2)->where('status', 1)->latest()->paginate(5);
        return view('user.sakit', compact(
            'izins',
        ));
    }

    public function create() {}

    public function store(Request $request) {
        $request->validate([
            'keterangan' => 'required',
            'dari' => 'required',
            'sampai' => 'required',
            'lampiran' => 'required',
        ]);

        try {
            $kode = $this->generateKodeIzin();

            $arrayIzin = [
                'user_id' => Auth::id(),
                'kode' => $kode,
                'keterangan' => $request['keterangan'],
                'dari' => $request['dari'],
                'sampai' => $request['sampai'],
                'lampiran' => $this->handleFileUpload($request->file('lampiran'), 'sakit/'),
                'status_izin' => 2,
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
            'dari' => 'required',
            'sampai' => 'required',
        ]);

        try {
            $izin = Izin::find($id);
    
            $array = [
                'keterangan' => $request['keterangan'],
                'dari' => $request['dari'],
                'sampai' => $request['sampai'],
                'status_process' => 1,
            ];

            if ($request->hasFile('lampiran')) {
                $array['lampiran'] = $this->handleFileUpload($request->file('lampiran'), 'sakit/');
            }

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

    private function handleFileUpload($file, $path)
    {
        if ($file) {
            $fileName = date('YmdHis') . rand(999999999, 9999999999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($path), $fileName);
            return $fileName;
        }
        return null;
    }
}
