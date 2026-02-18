<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Services\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService,
    ) {}

    public function create(): Response
    {
        return Inertia::render('Auth/Login/Index');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Email atau password salah',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();
        $user = $this->userService->getUserById(Auth::id());

        return match (true) {
            $user->role_id == 1 => redirect()->intended('admin/dashboard'),
            $user->role_id == 2 && $user->is_approved => redirect()->intended('admin/dashboard'),
            $user->role_id == 2 => redirect()->route('admin.pending-approval'),
            $user->role_id == 3 => redirect()->intended('mahasiswa/dashboard'),
            default => redirect()->intended('mahasiswa/materials'),
        };
    }

    public function show(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => ($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            },
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function index(): RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role_id <= 2) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->role_id == 3) {
                return redirect()->route('mahasiswa.dashboard');
            }
        }

        return redirect()->route('mahasiswa.materials.index');
    }

    public function fallback(): RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role_id <= 2) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->role_id == 3) {
                return redirect()->route('mahasiswa.dashboard');
            }
        }

        return redirect()->route('mahasiswa.materials.index');
    }
}
