<?php

namespace App\Http\Middleware;

use App\Enums\User\RoleName;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class AccessControl
{
    public function handle(
        Request $request,
        Closure $next,
        ?string $role = null,
        bool $requireApproval = false,
    ): Response {
        $user            = $request->user();
        $isAuthenticated = $user !== null;
        $userRole        = $user?->role?->role_name;
        $userRoleValue   = $userRole instanceof \BackedEnum ? $userRole->value : $userRole;

        if (! $isAuthenticated) {
            if ($role !== null && $role !== RoleName::GUEST->value) {
                return redirect('login');
            }

            return $next($request);
        }

        if ($role !== null && $role !== RoleName::GUEST->value) {
            $requiredRoles = explode('|', $role);
            if (! in_array($userRoleValue, $requiredRoles)) {
                return Inertia::render('Error/Index', [
                    'status'  => 403,
                    'message' => 'Anda tidak memiliki akses untuk halaman ini',
                ])->toResponse($request)->setStatusCode(403);
            }
        }

        if ($requireApproval && $user->isDosen() && ! $user->is_approved) {
            return to_route('admin.pending-approval');
        }

        return $next($request);
    }
}
