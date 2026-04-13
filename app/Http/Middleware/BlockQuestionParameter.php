<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockQuestionParameter
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('question') || $request->has('difficulty') || $request->has('sub_material')) {
            if ($request->has('difficulty')) {
                $request->session()->put('quiz_difficulty', $request->query('difficulty'));
            }

            return redirect()->to(url($request->path()));
        }

        return $next($request);
    }
}
