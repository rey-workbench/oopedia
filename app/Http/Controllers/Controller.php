<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\Services\GuestProgressServiceInterface;
use App\Enums\User\RoleName;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    protected function isGuest(): bool
    {
        if (Auth::guest()) {
            return true;
        }

        return (Auth::user()->role?->role_name ?? RoleName::GUEST->value) === RoleName::GUEST->value;
    }

    protected function render(string $page, array $data = []): Response
    {
        return Inertia::render($page, $data);
    }

    protected function getUserId(): int|string
    {
        return $this->isGuest() ? RoleName::GUEST->value : Auth::id();
    }

    protected function getGuestProgress(): array
    {
        if (! $this->isGuest()) {
            return [];
        }

        return resolve(GuestProgressServiceInterface::class)->getProgress();
    }

    protected function json(mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json($data, $status);
    }
}
