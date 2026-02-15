<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GuestAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Allow unauthenticated users (actual guests)
        if (!auth()->check()) {
            return $next($request);
        }
        
        $user = auth()->user();
        
        // Only restrict role 4 (Authenticated Guest)
        if ($user->role_id === 4) {
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
            
            $routeName = $request->route() ? $request->route()->getName() : null;
            
            if ($routeName && !in_array($routeName, $allowedRoutes)) {
                return redirect()->route('mahasiswa.materials.index')
                    ->with('info', 'Fitur ini hanya tersedia untuk mahasiswa terdaftar.');
            }
        }

        return $next($request);
    }
}