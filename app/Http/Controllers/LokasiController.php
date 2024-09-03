<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function index(Request $request) {
        $query = Lokasi::where('status', 1);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        }

        $lokasis = $query->latest()->paginate(10);

        return view('admin.lokasi.lokasi', compact(
            'lokasis',
        ));
    }

    public function create() {}

    public function store(Request $request) {
        $request->validate([
            'nama' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'radius' => 'required',
            'jam_masuk_siang' => 'required',
            'jam_pulang_siang' => 'required',
            'jam_masuk_malam' => 'required',
            'jam_pulang_malam' => 'required',
        ]);

        try {
            $slug = $this->generateSlug($request->input('nama'));

            $array = [
                'nama' => $request['nama'],
                'deskripsi' => $request['deskripsi'],
                'lat' => $request['lat'],
                'long' => $request['long'],
                'radius' => $request['radius'],
                'jam_masuk_siang' => $request['jam_masuk_siang'],
                'jam_pulang_siang' => $request['jam_pulang_siang'],
                'jam_masuk_malam' => $request['jam_masuk_malam'],
                'jam_pulang_malam' => $request['jam_pulang_malam'],
                'slug' => $slug,
            ];

            Lokasi::create($array);
    
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
            'lat' => 'required',
            'long' => 'required',
            'radius' => 'required',
            'jam_masuk_siang' => 'required',
            'jam_pulang_siang' => 'required',
            'jam_masuk_malam' => 'required',
            'jam_pulang_malam' => 'required',
        ]);

        try {
            $lokasi = Lokasi::find($id);
    
            $array = [
                'nama' => $request['nama'],
                'deskripsi' => $request['deskripsi'],
                'lat' => $request['lat'],
                'long' => $request['long'],
                'radius' => $request['radius'],
                'jam_masuk_siang' => $request['jam_masuk_siang'],
                'jam_pulang_siang' => $request['jam_pulang_siang'],
                'jam_masuk_malam' => $request['jam_masuk_malam'],
                'jam_pulang_malam' => $request['jam_pulang_malam'],
            ];

            $lokasi->update($array);
    
            return redirect()->back()->with('success', 'Success.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy($id) {
        try {
            $lokasi = Lokasi::find($id);

            $lokasi->update([
                'status' => 2,
            ]);

            return redirect()->back()->with('success', 'Success.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function preview($slug) {
        $lokasi = Lokasi::where('slug', $slug)->first();

        return view('admin.lokasi.preview', compact(
            'lokasi',
        ));
    }

    private function generateSlug($title) {
        $slug = Str::slug($title);
        $count = Lokasi::where('slug', 'like', "$slug%")->count();
    
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }
    
        return $slug;
    }
}
