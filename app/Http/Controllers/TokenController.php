<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\Token;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function check(Request $request)
    {
        $token = $request->input('token');
        $userId = auth()->id();
        $today = \Carbon\Carbon::now()->setTime(4, 0);

        $tokenExists = Token::where('token', $token)->exists();

        $hasCheckedInToday = Absen::where('user_id', $userId)
            ->whereDate('tanggal', $today)
            ->whereHas('token', function ($query) {
                $query->where('status', 1);
            })
            ->exists();

        $hasCheckedOutToday = Absen::where('user_id', $userId)
            ->whereDate('tanggal', $today)
            ->whereHas('token', function ($query) {
                $query->where('status', 2);
            })
            ->exists();

        $disableCheckIn = $hasCheckedInToday;
        $disableCheckOut = !$hasCheckedInToday || $hasCheckedOutToday;

        if ($tokenExists) {
            do {
                $newToken = mt_rand(10000, 99999);
                $exists = Token::where('token', $newToken)->exists();
            } while ($exists);

            return response()->json([
                'exists' => true,
                'newToken' => $newToken,
                'disableCheckIn' => $disableCheckIn,
                'disableCheckOut' => $disableCheckOut
            ]);
        } else {
            return response()->json([
                'exists' => false,
                'disableCheckIn' => $disableCheckIn,
                'disableCheckOut' => $disableCheckOut
            ]);
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
