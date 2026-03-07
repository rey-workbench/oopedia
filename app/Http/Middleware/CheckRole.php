<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param string $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (! auth()->check()) {
            return redirect('login');
        }

        // Convert role parameter to array for multiple role support
        $roles = explode('|', $role);

        if (! in_array(auth()->user()->role->role_name, $roles)) {
            return Inertia::render('Error', [
                'status'  => 403,
                'message' => 'Anda tidak memiliki akses untuk halaman ini',
            ])->toResponse($request)->setStatusCode(403);
        }

        return $next($request);
    }
}
