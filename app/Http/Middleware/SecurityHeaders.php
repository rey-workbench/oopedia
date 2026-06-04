<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request):Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof Response) {
            // Apply standard headers
            $headers = config('security.headers', []);
            foreach ($headers as $header => $value) {
                $response->headers->set($header, $value);
            }

            // Build CSP
            $csp           = config('security.csp', []);
            $cspDirectives = [];
            foreach ($csp as $directive => $value) {
                $cspDirectives[] = empty($value) ? $directive : sprintf('%s %s', $directive, $value);
            }

            $response->headers->set('Content-Security-Policy', implode('; ', $cspDirectives));

            // Build Permissions Policy
            $permissions    = config('security.permissions', []);
            $permDirectives = [];
            foreach ($permissions as $feature => $value) {
                $permDirectives[] = $feature . $value;
            }

            $response->headers->set('Permissions-Policy', implode(', ', $permDirectives));

            // Remove server headers
            $response->headers->remove('X-Powered-By');
            $response->headers->remove('Server');
        }

        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
            header_remove('Server');
        }

        return $response;
    }
}
