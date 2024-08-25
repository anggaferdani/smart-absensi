<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function index(Request $request) {
        $query = Experience::where('status', 1);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('experience', 'like', '%' . $search . '%');
            });
        }

        $experiences = $query->paginate(10);

        return view('backend.pages.experiences.index', compact(
            'experiences',
        ));
    }

    public function create() {}

    public function store(Request $request) {
        try {
            $request->validate([
                'experience' => 'required',
            ]);
    
            $array = [
                'experience' => $request['experience'],
            ];

            Experience::create($array);
    
            return redirect()->route('admin.experience.index')->with('success', 'Success');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function show($id) {}

    public function edit($id) {}

    public function update(Request $request, $id) {
        try {
            $experience = Experience::find($id);
    
            $request->validate([
                'experience' => 'required',
            ]);
    
            $array = [
                'experience' => $request['experience'],
            ];
    
            $experience->update($array);
    
            return redirect()->route('admin.experience.index')->with('success', 'Success');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy($id) {
        try {
            $user = Experience::find($id);

            $user->update([
                'status' => 0,
            ]);

            return redirect()->route('admin.experience.index')->with('success', 'Success');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
