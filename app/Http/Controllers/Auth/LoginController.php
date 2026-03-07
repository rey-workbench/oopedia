<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Services\UserServiceInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function __construct(protected
        UserServiceInterface $userService, protected
        GuestProgressServiceInterface $guestProgressService,
        )
    {
    }

    public function create(): Response
    {
        return Inertia::render('Auth/Login/Index');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        // Handle Guest Login
        if ($request->has('is_guest')) {
            if (Auth::check()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            $this->guestProgressService->clearAllProgress();

            return Redirect::route('mahasiswa.materials.index');
        }

        // Handle Standard Login
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Email atau password salah',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        $this->guestProgressService->clearAllProgress();
        $user = $this->userService->getUserById(Auth::id());

        $clearGuest = Cookie::forget('is_guest');
        $clearProgress = Cookie::forget('guest_progress');

        return match (true) {
                $user->role_id == 1 => redirect()->intended('admin/dashboard')->withCookie($clearGuest)->withCookie($clearProgress),
                $user->role_id == 2 && $user->is_approved => redirect()->intended('admin/dashboard')->withCookie($clearGuest)->withCookie($clearProgress),
                $user->role_id == 2 => redirect()->route('admin.pending-approval')->withCookie($clearGuest)->withCookie($clearProgress),
                $user->role_id == 3 => redirect()->intended('mahasiswa/dashboard')->withCookie($clearGuest)->withCookie($clearProgress),
                default => redirect()->intended('mahasiswa/materials')->withCookie($clearGuest)->withCookie($clearProgress),
            };
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function home(): RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role_id <= 2) {
                return Redirect::route('admin.dashboard');
            }

            if ($user->role_id == 3) {
                return Redirect::route('mahasiswa.dashboard');
            }
        }

        return Redirect::route('mahasiswa.materials.index');
    }
}
