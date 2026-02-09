<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            // Refresh user data dari database
            $user = User::find($user->id);
            
            // Cek role dan status approval
            if ($user->role_id == 1) {
                // Superadmin
                return redirect()->intended('admin/dashboard');
            } else if ($user->role_id == 2) {
                // Admin
                if ($user->is_approved) {
                    return redirect()->intended('admin/dashboard');
                } else {
                    return redirect()->route('admin.pending-approval');
                }
            } else if ($user->role_id == 3) {
                // Mahasiswa
                return redirect()->intended('mahasiswa/dashboard');
            } else {
                // Tamu (role_id = 4)
                return redirect()->intended('mahasiswa/materials');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ])->onlyInput('email');
    }

    public function show()
    {
        request()->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            request()->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    public function update()
    {
        request()->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]); 
        
        $status = Password::reset(
            request()->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => ($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->role_id <= 2) {
                return redirect()->route('admin.dashboard');
            } else if ($user->role_id == 3) {
                return redirect()->route('mahasiswa.dashboard');
            }
        }
        
        // Unauthenticated users are treated as guests
        return redirect()->route('mahasiswa.materials.index');
    }

    public function fallback()
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->role_id <= 2) {
                return redirect()->route('admin.dashboard');
            } else if ($user->role_id == 3) {
                return redirect()->route('mahasiswa.dashboard');
            }
        }
        
        // Guest users or unauthenticated users fall back to materials
        return redirect()->route('mahasiswa.materials.index');
    }
} 