<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GuestAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Allow paths for question exercises (for both guests and authenticated users)
        $allowedPaths = [
            'mahasiswa/materials/questions',
            'mahasiswa/materials/*/questions',
            'mahasiswa/materials/*/questions/*',
            'mahasiswa/materials/*/questions/levels',
            'mahasiswa/materials/*/questions/review'
        ];
        
        $currentPath = $request->path();
        
        foreach ($allowedPaths as $path) {
            if (fnmatch($path, $currentPath)) {
                return $next($request);
            }
        }
        
        // Allow all unauthenticated users to access routes
        if (!auth()->check()) {
            return $next($request);
        }
        
        if (auth()->check()) {
            $user = auth()->user();
            
            // Allow regular students (role 3) and admin/superadmin (roles 1-2) to access all routes
            if ($user->role_id <= 3) {
                return $next($request);
            }
            
            // Restrict guest access (role 4)
            if ($user->role_id === 4) {
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
                    'register',
                    'guest.logout'
                ];
                
                // Allow questions path for guests
                if (strpos($request->path(), 'materials/questions') !== false) {
                    return $next($request);
                }
                
                if (!in_array($request->route()->getName(), $allowedRoutes)) {
                    return redirect()->route('mahasiswa.materials.index')
                        ->with('info', 'Fitur ini hanya tersedia untuk mahasiswa terdaftar.');
                }
            }
        }

        return $next($request);
    }
}