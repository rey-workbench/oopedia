<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        
        $cookieGuest = Cookie::forget('is_guest');
        $cookieProgress = Cookie::forget('guest_progress');

        return redirect($request->input('redirect', '/login'))
            ->withCookie($cookieGuest)
            ->withCookie($cookieProgress);
    }
} 