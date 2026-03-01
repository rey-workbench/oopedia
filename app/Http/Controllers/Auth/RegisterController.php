<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Services\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService,
    ) {}

    public function create(): Response
    {
        return Inertia::render('Auth/Register/Index');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $role_id = $request->has('register_as_admin') ? 2 : 3;
        $is_approved = ($role_id === 3);

        $user = $this->userService->registerUser([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => $role_id,
            'is_approved' => $is_approved,
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($role_id === 2 && ! $is_approved) {
            return Redirect::route('admin.pending-approval');
        }

        if ($role_id === 2) {
            return Redirect::route('admin.dashboard');
        }

        return Redirect::route('mahasiswa.dashboard');
    }
}
