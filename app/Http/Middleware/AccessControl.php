<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class AccessControl
{
    /**
     * Handle an incoming request.
     *
     * @param string|null $role - Required role (superadmin|dosen|mahasiswa|guest)
     * @param bool $requireApproval - Whether approval is required for this route
     */
    public function handle(
        Request $request,
        Closure $next,
        ?string $role = null,
        bool $requireApproval = false,
    ): Response {
        $user            = $request->user();
        $isAuthenticated = $user !== null;
        $userRole        = $user?->role?->role_name ?? null;

        // Handle unauthenticated users
        if (! $isAuthenticated) {
            // If role is required, redirect to login
            if ($role !== null && $role !== 'guest') {
                return redirect('login');
            }

            // Allow guest access for unauthenticated users
            return $next($request);
        }

        // Handle authenticated users

        // Check if role is required
        if ($role !== null && $role !== 'guest') {
            $requiredRoles = explode('|', $role);
            if (! in_array($userRole, $requiredRoles)) {
                return Inertia::render('Error', [
                    'status'  => 403,
                    'message' => 'Anda tidak memiliki akses untuk halaman ini',
                ])->toResponse($request)->setStatusCode(403);
            }
        }

        // Check admin approval requirement
        if ($requireApproval && $user->isDosen() && ! $user->is_approved) {
            return redirect()->route('admin.pending-approval');
        }

        // Handle guest role restrictions
        if ($userRole === 'guest') {
            $allowedRoutes = [
                'mahasiswa.materials.index',
                'mahasiswa.materials.show',
                'mahasiswa.submaterials.show',
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
