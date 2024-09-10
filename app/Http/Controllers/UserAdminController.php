<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lokasi;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function index(Request $request) {
        $query = User::where('role', 2)->where('status', 1);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
                $q->where('phone', 'like', '%' . $search . '%');
                $q->where('email', 'like', '%' . $search . '%');
                $q->where('jabatan', 'like', '%' . $search . '%');
            });
        }

        $users = $query->latest()->paginate(10);

        $lokasis = Lokasi::where('status', 1)->get();

        return view('admin.user', compact(
            'users',
            'lokasis',
        ));
    }

    public function create() {}

    public function store(Request $request) {
        $request->validate([
            'profile_picture' => 'image|mimes:jpeg,png,jpg|dimensions:ratio=1/1',
            'name' => 'required',
            'phone' => 'required|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'lokasi_id' => 'required',
        ]);

        try {
            $profilePicturePath = $request->hasFile('profile_picture')
            ? $this->handleFileUpload($request->file('profile_picture'), 'profile-picture/')
            : 'default.png';

            $array = [
                'profile_picture' => $profilePicturePath,
                'name' => $request['name'],
                'phone' => $request['phone'],
                'email' => $request['email'],
                'password' => bcrypt($request['password']),
                'jabatan' => $request['jabatan'],
                'lokasi_id' => $request['lokasi_id'],
                'role' => 2,
            ];

            User::create($array);
    
            return redirect()->back()->with('success', 'Success.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function show($id) {}

    public function edit($id) {}

    public function update(Request $request, $id) {
        $user = User::find($id);

        $request->validate([
            'profile_picture' => 'dimensions:ratio=1/1',
            'name' => 'required',
            'phone' => 'required|unique:users,phone,'.$user->id.",id",
            'email' => 'required|email|unique:users,email,'.$user->id.",id",
            'lokasi_id' => 'required',
        ]);

        try {
            $array = [
                'name' => $request['name'],
                'phone' => $request['phone'],
                'email' => $request['email'],
                'jabatan' => $request['jabatan'],
                'lokasi_id' => $request['lokasi_id'],
            ];

            if ($request['password']) {
                $array['password'] = bcrypt($request['password']);
            }

            if ($request->hasFile('profile_picture')) {
                $array['profile_picture'] = $this->handleFileUpload($request->file('profile_picture'), 'profile-picture/');
            }

            $user->update($array);
    
            return redirect()->back()->with('success', 'Success.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy($id) {
        try {
            $user = User::find($id);

            $user->update([
                'status' => 2,
            ]);

            return redirect()->back()->with('success', 'Success.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
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
