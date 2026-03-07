<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockQuestionParameter
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Block any attempt to pass 'question', 'difficulty', or 'sub_material' query parameters
        if ($request->has('question') || $request->has('difficulty') || $request->has('sub_material')) {
            // Store difficulty in session if provided before blocking
            if ($request->has('difficulty')) {
                $request->session()->put('quiz_difficulty', $request->query('difficulty'));
            }

            return redirect()->to(url($request->path()));
        }

        return $next($request);
    }
}
