<?php

namespace App\Http\Middleware;

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
            if ($role !== null && $role !== 'guest') {
                return redirect('login');
            }

            return $next($request);
        }

        if ($role !== null && $role !== 'guest') {
            $requiredRoles = explode('|', $role);
            if (! in_array($userRoleValue, $requiredRoles)) {
                return Inertia::render('Error/Index', [
                    'status'  => 403,
                    'message' => 'Anda tidak memiliki akses untuk halaman ini',
                ])->toResponse($request)->setStatusCode(403);
            }
        }

        if ($requireApproval && $user->isDosen() && ! $user->is_approved) {
            return redirect()->route('admin.pending-approval');
        }

        if ($userRoleValue === 'guest') {
            $allowedRoutes = [
                'mahasiswa.materials.index',
                'mahasiswa.materials.show',
                'mahasiswa.materials.questions.index',
                'mahasiswa.materials.questions.show',
                'mahasiswa.materials.questions.review',
                'mahasiswa.materials.questions.levels',
                'mahasiswa.materials.questions.check',
                'mahasiswa.materials.questions.attempts',
                'mahasiswa.materials.reset',
                'logout',
                'login',
            ];

            $routeName = $request->route()?->getName();

            if ($routeName && ! in_array($routeName, $allowedRoutes)) {
                return redirect()->route('mahasiswa.materials.index')
                    ->with('info', 'Fitur ini hanya tersedia untuk mahasiswa terdaftar.');
            }
        }

        return $next($request);
    }
}
