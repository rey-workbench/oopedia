<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class GuestLoginController extends Controller
{
    public function login()
    {
        // Set guest cookie for 30 days
        $cookie = cookie('is_guest', 'true', 43200);
        
        return redirect()->route('mahasiswa.materials.index')
            ->withCookie($cookie)
            ->with('info', 'Anda masuk sebagai tamu. Beberapa fitur dan konten materi akan terbatas.');
    }
} 