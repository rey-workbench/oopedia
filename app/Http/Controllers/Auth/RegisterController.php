<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Contracts\Services\UserServiceInterface;
use App\DTOs\User\UserRegistrationDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

final class RegisterController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $userService,
    ) {}

    public function create(): Response
    {
        return $this->render('Auth/Register/Index');
    }

    public function store(RegisterRequest $registerRequest): RedirectResponse
    {
        $userRegistrationDTO = UserRegistrationDTO::fromRequest($registerRequest);

        $user = $this->userService->registerUser($userRegistrationDTO->toArray());

        event(new Registered($user));

        Auth::login($user);

        if ($userRegistrationDTO->isDosen && ! $userRegistrationDTO->is_approved) {
            return Redirect::route('admin.pending-approval');
        }

        if ($userRegistrationDTO->isDosen) {
            return Redirect::route('admin.dashboard');
        }

        return Redirect::route('mahasiswa.dashboard');
    }
}
