<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class GuestLoginController extends Controller
{
    public function login(): RedirectResponse
    {
        $cookie = cookie('is_guest', 'true', 43200);

        return redirect()->route('mahasiswa.materials.index')
            ->withCookie($cookie)
            ->with('info', 'Anda masuk sebagai tamu. Beberapa fitur dan konten materi akan terbatas.');
    }
}
