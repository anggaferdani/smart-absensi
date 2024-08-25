<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Izin;
use App\Models\Absen;
use App\Models\Token;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index() {
        $lokasis = Lokasi::where('status', 1)->get();
        return view('user.index', compact(
            'lokasis',
        ));
    }

    public function absen(Request $request) {
        $request->validate([
            'lokasi_id' => 'required',
            'token' => 'required',
            'status' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'jam_masuk' => 'required',
            'jam_pulang' => 'required',
        ]);

        try {
            $arrayToken = [
                'lokasi_id' => $request['lokasi_id'],
                'token' => $request['token'],
                'tanggal' => now(),
                'status' => $request['status'],
            ];

            $token = Token::create($arrayToken);

            if ($token) {
                $time = $token->tanggal->format('H:i');
                $jamMasukTime = Carbon::parse($request['jam_masuk'])->format('H:i');
                $jamPulangTime = Carbon::parse($request['jam_pulang'])->format('H:i');

                if ($token->status == 1) {
                    if ($time < $jamMasukTime) {
                        $absenStatus = 1;
                    } elseif ($time == $jamMasukTime) {
                        $absenStatus = 2;
                    } else {
                        $absenStatus = 3;
                    }
                } elseif ($token->status == 2) {
                    if ($time < $jamPulangTime) {
                        $absenStatus = 1;
                    } elseif ($time == $jamPulangTime) {
                        $absenStatus = 2;
                    } else {
                        $absenStatus = 3;
                    }
                }

                $kode = $this->generateKodeAbsen();

                $arrayAbsen = [
                    'token_id' => $token->id,
                    'user_id' => Auth::id(),
                    'kode' => $kode,
                    'lat' => $request['lat'],
                    'long' => $request['long'],
                    'tanggal' => now(),
                    'status' => $absenStatus,
                ];
    
                $absen = Absen::create($arrayAbsen);

                if ($absen) {
                    $tokenMessage = Token::with('lokasi')->find($token->id);
                    $absenMessage = Absen::with('token', 'user')->find($absen->id);

                    return redirect()->back()
                        ->with('success', 'Success.')
                        ->with('tokenMessage', $tokenMessage)
                        ->with('absenMessage', $absenMessage);
                }
            }
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function history() {
        $absens = Absen::with('token', 'token.lokasi', 'user')->where('user_id', Auth::id())->latest()->paginate(5);
        return view('user.history', compact(
            'absens',
        ));
    }

    private function generateKodeAbsen() {
        do {
            $kode = mt_rand(100000000000, 999999999999);
            
            $exists = Absen::where('kode', $kode)->exists();
        } while ($exists);
    
        return $kode;
    }
}
