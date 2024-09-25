<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Izin;
use App\Models\Absen;
use App\Models\Token;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function dashboard() {
        return view('user.dashboard');
    }

    public function shift() {
        return view('user.shift');
    }
    
    public function index(Request $request) {
        $lokasis = Lokasi::where('status', 1)->get();
        return view('user.index', compact(
            'lokasis',
        ));
    }

    public function response($kode) {
        $absen = Absen::with('token')->where('kode', $kode)->first();
        return view('user.response', compact(
            'absen',
        ));
    }

    public function absen(Request $request) {
        $request->validate([
            'shift' => 'required',
            'lokasi_id' => 'required',
            'token' => 'required',
            'status' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'jam_masuk_siang' => 'required',
            'jam_pulang_siang' => 'required',
            'jam_masuk_malam' => 'required',
            'jam_pulang_malam' => 'required',
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
                $jamMasukSiangTime = Carbon::parse($request['jam_masuk_siang'])->format('H:i');
                $jamPulangSiangTime = Carbon::parse($request['jam_pulang_siang'])->format('H:i');
                $jamMasukMalamTime = Carbon::parse($request['jam_masuk_malam'])->format('H:i');
                $jamPulangMalamTime = Carbon::parse($request['jam_pulang_malam'])->format('H:i');

                if ($request->shift == 'siang') {
                    if ($token->status == 1) {
                        if ($time < $jamMasukSiangTime) {
                            $absenStatus = 1;
                        } elseif ($time == $jamMasukSiangTime) {
                            $absenStatus = 2;
                        } else {
                            $absenStatus = 3;
                        }
                    } elseif ($token->status == 2) {
                        if ($time < $jamPulangSiangTime) {
                            $absenStatus = 1;
                        } elseif ($time == $jamPulangSiangTime) {
                            $absenStatus = 2;
                        } else {
                            $absenStatus = 3;
                        }
                    }
                } elseif ($request->shift == 'malam') {
                    if ($token->status == 1) {
                        if ($time < $jamMasukMalamTime) {
                            $absenStatus = 1;
                        } elseif ($time == $jamMasukMalamTime) {
                            $absenStatus = 2;
                        } else {
                            $absenStatus = 3;
                        }
                    } elseif ($token->status == 2) {
                        if ($time < $jamPulangMalamTime) {
                            $absenStatus = 1;
                        } elseif ($time == $jamPulangMalamTime) {
                            $absenStatus = 2;
                        } else {
                            $absenStatus = 3;
                        }
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
                    'shift' => $request['shift'],
                    'status' => $absenStatus,
                ];
    
                $absen = Absen::create($arrayAbsen);

                if ($absen) {
                    return redirect()->route('user.response', ['kode' => $absen->kode])
                        ->with('success', 'Success.');
                }
            }
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function history(Request $request) {
        $userId = Auth::id();
        $currentYear = now()->year;
        $currentMonth = now()->month;
    
        $selectedMonth = $request->get('bulan', $currentMonth);
    
        // Fetch attendance records for the user
        $absens = Absen::with('token', 'token.lokasi', 'user')
            ->where('user_id', $userId)
            ->whereYear('tanggal', $currentYear)
            ->whereMonth('tanggal', $selectedMonth)
            ->latest()
            ->paginate(5);
    
        // Count izin (leave) days
        $izinDays = Izin::where('user_id', $userId)
            ->where('status_izin', 1)
            ->where('status', 1)
            ->whereYear('dari', $currentYear)
            ->whereMonth('dari', $selectedMonth)
            ->sum(DB::raw("DATEDIFF(sampai, dari) + 1"));
    
        // Count sick days
        $sickDays = Izin::where('user_id', $userId)
            ->where('status_izin', 2)
            ->where('status', 1)
            ->whereYear('dari', $currentYear)
            ->whereMonth('dari', $selectedMonth)
            ->sum(DB::raw("DATEDIFF(sampai, dari) + 1"));
    
        // Count late days
        $lateDays = Absen::where('user_id', $userId)
            ->where('status', 3)
            ->whereHas('token', function($query) {
                $query->where('status', 1);
            })
            ->whereYear('tanggal', $currentYear)
            ->whereMonth('tanggal', $selectedMonth)
            ->count();
    
        // Count total working days in the selected month
        $totalDaysInMonth = $this->countBusinessDays($currentYear, $selectedMonth);
    
        // Count actual attended days (based on records)
        $actualAttendanceDays = $absens->count();
    
        // Total absent days are the sum of izin days and sick days
        $absentDays = $izinDays + $sickDays;
    
        // Calculate attendance percentage
        if ($totalDaysInMonth > 0) {
            $attendancePercentage = (($actualAttendanceDays) / $totalDaysInMonth) * 100;
        } else {
            $attendancePercentage = 0; // Avoid division by zero
        }
    
        return view('user.history', compact('absens', 'selectedMonth', 'izinDays', 'sickDays', 'lateDays', 'attendancePercentage'));
    }
    
    private function countBusinessDays($year, $month) {
        $totalDays = 0;
    
        $date = \Carbon\Carbon::create($year, $month, 1);
        while ($date->month == $month) {
            // Count only if it's not Sunday
            if ($date->dayOfWeek != \Carbon\Carbon::SUNDAY) {
                $totalDays++;
            }
            $date->addDay();
        }
    
        return $totalDays;
    }    

    private function generateKodeAbsen() {
        do {
            $kode = mt_rand(100000000000, 999999999999);
            
            $exists = Absen::where('kode', $kode)->exists();
        } while ($exists);
    
        return $kode;
    }
}
