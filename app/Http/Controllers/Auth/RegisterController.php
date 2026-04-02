<?php

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

class RegisterController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService,
    ) {
    }

    public function create(): Response
    {
        return $this->render('Auth/Register/Index');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $dto = UserRegistrationDTO::fromRequest($request);

        $user = $this->userService->registerUser($dto->toArray());

        event(new Registered($user));

        Auth::login($user);

        if ($dto->isDosen && ! $dto->is_approved) {
            return Redirect::route('admin.pending-approval');
        }

        if ($dto->isDosen) {
            return Redirect::route('admin.dashboard');
        }

        return Redirect::route('mahasiswa.dashboard');
    }
}
