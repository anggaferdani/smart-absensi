<?php

namespace App\Http\Controllers;

use App\Models\Token;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function check(Request $request)
    {
        $token = $request->input('token');
        $exists = Token::where('token', $token)->exists();

        if ($exists) {
            do {
                $newToken = mt_rand(10000, 99999);
                $exists = Token::where('token', $newToken)->exists();
            } while ($exists);

            return response()->json(['exists' => true, 'newToken' => $newToken]);
        } else {
            return response()->json(['exists' => false]);
        }
    }

    public function token(Request $request) {
        $query = Token::with('lokasi');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('token', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('tanggal')) {
            $tanggal = $request->input('tanggal');
            $query->whereDate('tanggal', $tanggal);
        }

        $tokens = $query->latest()->paginate(10);

        return view('admin.token', compact(
            'tokens',
        ));
    }
}
