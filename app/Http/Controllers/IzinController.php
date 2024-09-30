<?php

namespace App\Http\Controllers;

use App\Models\Izin;
use Illuminate\Http\Request;
use App\Models\ContactPerson;
use Illuminate\Support\Facades\Auth;

class IzinController extends Controller
{
    public function index() {
        $izins = Izin::with('user')->where('user_id', Auth::id())->where('status_izin', 1)->where('status', 1)->latest()->paginate(5);
        $contactPerson = ContactPerson::where('status', 1)->first();
        return view('user.izin.izin', compact(
            'izins',
            'contactPerson',
        ));
    }

    public function create() {
        return view('user.izin.create');
    }

    public function store(Request $request) {
        $request->validate([
            'keterangan' => 'required',
            'dari' => 'required',
            'sampai' => 'required|after_or_equal:dari',
            'lampiran' => 'nullable|max:2048|file|mimes:png,jpg,jpeg',
        ]);

        try {
            $kode = $this->generateKodeIzin();

            $arrayIzin = [
                'user_id' => Auth::id(),
                'kode' => $kode,
                'keterangan' => $request['keterangan'],
                'dari' => $request['dari'],
                'sampai' => $request['sampai'],
                'lampiran' => $this->handleFileUpload($request->file('lampiran'), 'izin/'),
                'status_izin' => 1,
            ];

            $izin = Izin::create($arrayIzin);

            return redirect()->route('user.izin.show', $izin->kode)->with('success', 'Success.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function show($kode) {
        $izin = Izin::where('kode', $kode)->first();
        $contactPerson = ContactPerson::where('status', 1)->first();
        return view('user.izin.show', compact(
            'izin',
            'contactPerson',
        ));
    }

    public function edit($id) {
        $izin = Izin::find($id);
        return view('user.izin.edit', compact(
            'izin',
        ));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'keterangan' => 'required',
            'dari' => 'required',
            'sampai' => 'required|after_or_equal:dari',
            'lampiran' => 'nullable|max:2048|file|mimes:png,jpg,jpeg',
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
                $array['lampiran'] = $this->handleFileUpload($request->file('lampiran'), 'izin/');
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
