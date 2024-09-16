<?php

namespace App\Http\Controllers;

use App\Models\UnitKerja;
use Illuminate\Http\Request;

class UnitKerjaAdminController extends Controller
{
    public function index(Request $request) {
        $query = UnitKerja::where('status', 1);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        }

        $unitKerjas = $query->latest()->paginate(10);

        return view('admin.unit-kerja.unit-kerja', compact(
            'unitKerjas',
        ));
    }

    public function create() {}

    public function store(Request $request) {
        $request->validate([
            'nama' => 'required',
        ]);

        try {
            $array = [
                'nama' => $request['nama'],
                'keterangan' => $request['keterangan'],
            ];

            UnitKerja::create($array);
    
            return redirect()->back()->with('success', 'Success.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function show($id) {}

    public function edit($id) {}

    public function update(Request $request, $id) {
        $request->validate([
            'nama' => 'required',
        ]);

        try {
            $unitKerja = UnitKerja::find($id);
    
            $array = [
                'nama' => $request['nama'],
                'keterangan' => $request['keterangan'],
            ];

            $unitKerja->update($array);
    
            return redirect()->back()->with('success', 'Success.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy($id) {
        try {
            $unitKerja = UnitKerja::find($id);

            $unitKerja->update([
                'status' => 2,
            ]);

            return redirect()->back()->with('success', 'Success.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
