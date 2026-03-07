<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdminIsApproved
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->isDosen() && !auth()->user()->is_approved) {
            return redirect()->route('admin.pending-approval');
        }

        return $next($request);
    }
}
