<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

final class LoginController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService,
        protected GuestProgressServiceInterface $guestProgressService,
    ) {}

    public function create(): Response
    {
        return $this->render('Auth/Login/Index');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        if ($request->has('is_guest')) {
            if (Auth::check()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            $this->guestProgressService->clearAllProgress();

            return Redirect::route('mahasiswa.materials.index');
        }

        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Email atau password salah',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        $this->guestProgressService->clearAllProgress();
        $user = $this->userService->getUserById(Auth::id());

        $redirect = match (true) {
            $user->isSuperAdmin()                  => redirect()->route('admin.dashboard'),
            $user->isDosen() && $user->is_approved => redirect()->route('admin.dashboard'),
            $user->isDosen()                       => redirect()->route('admin.pending-approval'),
            $user->isMahasiswa()                   => redirect()->route('mahasiswa.dashboard'),
            default                                => redirect()->route('mahasiswa.materials.index'),
        };

        return $redirect
            ->withCookie(Cookie::forget('is_guest'))
            ->withCookie(Cookie::forget('guest_progress'));
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
        if (! Auth::check()) {
            return Redirect::route('mahasiswa.materials.index');
        }

        $user = Auth::user();

        if ($user->isSuperAdmin() || $user->isDosen()) {
            return Redirect::route('admin.dashboard');
        }

        if ($user->isMahasiswa()) {
            return Redirect::route('mahasiswa.dashboard');
        }

        return Redirect::route('mahasiswa.materials.index');
    }

    public function landing(): Response
    {
        return $this->render('Landing/Index');
    }
}
