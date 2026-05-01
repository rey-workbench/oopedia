<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\UserServiceInterface;
use App\DTOs\User\ProfileUpdateDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class ProfileController extends Controller
{
    public function __construct(
        private readonly UserServiceInterface $userService,
    ) {}

    public function show(): Response
    {
        $user = Auth::user();

        return $this->render('Admin/Profile/Index', [
            'user' => $user,
        ]);
    }

    public function update(UpdateProfileRequest $updateProfileRequest): RedirectResponse
    {
        $profileUpdateDTO = ProfileUpdateDTO::fromRequest($updateProfileRequest);

        $this->userService->updateProfile(Auth::id(), $profileUpdateDTO->toArray());

        return back()->with('success', 'Profil berhasil diperbarui');
    }
}
