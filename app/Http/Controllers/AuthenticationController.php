<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    public function login() {
        return view('login');
    }

    public function postLogin(Request $request) {
        $loginType = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $this->validate($request, [
            'login' => $loginType == 'email' ? 'email|exists:users,email' : 'required|numeric|exists:users,phone',
            'password' => 'required',
        ]);

        $credentials = [
            $loginType => $request->input('login'),
            'password' => $request->input('password'),
        ];

        if (Auth::guard('web')->attempt($credentials)) {
            if (auth()->user()->status == 1) {
                if (auth()->user()->role == 1) {
                    return redirect()->route('admin.dashboard');
                } elseif (auth()->user()->role == 2) {
                    return redirect()->route('user.dashboard');
                } else {
                    return redirect()->route('login')->with('error', 'The account level you entered does not match');
                }
            } else {
                Auth::guard('web')->logout();
                return redirect()->route('login')->with('error', 'Your account has been disabled');
            }
        } else {
            return redirect()->route('login')->with('error', 'The email or password you entered is incorrect. Please try again');
        }
    }

    public function logout() {
        Auth::guard('web')->logout();
        return redirect()->route('login');
    }
}
