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
        $userId = auth()->id(); // Get the ID of the authenticated user
        $today = \Carbon\Carbon::today(); // Get today's date

        // Check if the token exists
        $tokenExists = Token::where('token', $token)->exists();

        // Check if the user has already checked in today
        $hasCheckedInToday = Absen::where('user_id', $userId)
            ->whereDate('tanggal', $today)
            ->whereHas('token', function ($query) {
                $query->where('status', 1); // Check if token status is 1 (Check In)
            })
            ->exists();

        // Check if the user has checked out today based on the token status
        $hasCheckedOutToday = Absen::where('user_id', $userId)
            ->whereDate('tanggal', $today)
            ->whereHas('token', function ($query) {
                $query->where('status', 2); // Check if token status is 2 (Check Out)
            })
            ->exists();

        // Determine whether to disable buttons
        $disableCheckIn = $hasCheckedInToday;
        $disableCheckOut = $hasCheckedOutToday;

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
