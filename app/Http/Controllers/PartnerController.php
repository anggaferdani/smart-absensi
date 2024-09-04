<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(Request $request) {
        $query = Partner::where('status', 1);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('partner', 'like', '%' . $search . '%');
            });
        }

        $partners = $query->paginate(10);

        return view('backend.pages.partners.index', compact(
            'partners',
        ));
    }

    public function create() {}

    public function store(Request $request) {
        try {
            $request->validate([
                'partner' => 'required',
                'logo' => 'required',
            ]);
    
            $array = [
                'partner' => $request['partner'],
                'logo' => $this->handleFileUpload($request->file('logo'), 'partners/logo/'),
            ];

            Partner::create($array);
    
            return redirect()->route('admin.partner.index')->with('success', 'Success');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function show($id) {}

    public function edit($id) {}

    public function update(Request $request, $id) {
        try {
            $partner = Partner::find($id);
    
            $request->validate([
                'partner' => 'required',
            ]);
    
            $array = [
                'partner' => $request['partner'],
            ];

            if ($request->hasFile('logo')) {
                $array['logo'] = $this->handleFileUpload($request->file('logo'), 'partners/logo/');
            }
    
            $partner->update($array);
    
            return redirect()->route('admin.partner.index')->with('success', 'Success');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy($id) {
        try {
            $user = Partner::find($id);

            $user->update([
                'status' => 0,
            ]);

            return redirect()->route('admin.partner.index')->with('success', 'Success');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    private function handleFileUpload($file, $path)
    {
        if ($file) {
            $fileName = date('YmdHis') . rand(999999999, 9999999999) . '.' . $file->getClientOriginalName();
            $file->move(public_path($path), $fileName);
            return $fileName;
        }
        return null;
    }
}
