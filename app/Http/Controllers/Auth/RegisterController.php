<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Services\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

class RegisterController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService,
    ) {}

    public function create(): Response
    {
        return $this->render('Auth/Register/Index');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $roleName    = $request->has('register_as_admin') ? 'dosen' : 'mahasiswa';
        $role        = Role::where('role_name', $roleName)->first();
        $roleId      = $role?->id;
        $isApproved  = ($roleName === 'mahasiswa');

        $user = $this->userService->registerUser([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => $request->password,
            'role_id'     => $roleId,
            'is_approved' => $isApproved,
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($roleName === 'dosen' && ! $isApproved) {
            return Redirect::route('admin.pending-approval');
        }

        if ($roleName === 'dosen') {
            return Redirect::route('admin.dashboard');
        }

        return Redirect::route('mahasiswa.dashboard');
    }
}
