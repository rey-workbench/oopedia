<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Contracts\Services\GuestProgressServiceInterface;
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
        private readonly GuestProgressServiceInterface $guestProgressService,
    ) {}

    public function create(): Response
    {
        return $this->render('Auth/Login/Index');
    }

    public function store(LoginRequest $loginRequest): RedirectResponse
    {
        if ($loginRequest->boolean('is_guest')) {
            if (Auth::check()) {
                Auth::logout();
                $loginRequest->session()->invalidate();
                $loginRequest->session()->regenerateToken();
            }

            $this->guestProgressService->clearAllProgress();

            return to_route('mahasiswa.materials.index');
        }

        $credentials = $loginRequest->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => __('auth.failed'),
            ])->onlyInput('email');
        }

        $loginRequest->session()->regenerate();
        $this->guestProgressService->clearAllProgress();
        $user = Auth::user();

        $redirect = match (true) {
            $user->isSuperAdmin()                  => to_route('admin.dashboard'),
            $user->isDosen() && $user->is_approved => to_route('admin.dashboard'),
            $user->isDosen()                       => to_route('admin.pending-approval'),
            $user->isMahasiswa()                   => to_route('mahasiswa.dashboard'),
            default                                => to_route('mahasiswa.materials.index'),
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
            return to_route('mahasiswa.materials.index');
        }

        $user = Auth::user();

        if ($user->isSuperAdmin() || $user->isDosen()) {
            return to_route('admin.dashboard');
        }

        if ($user->isMahasiswa()) {
            return to_route('mahasiswa.dashboard');
        }

        return to_route('mahasiswa.materials.index');
    }

    public function landing(): Response
    {
        return $this->render('Landing/Index');
    }
}
