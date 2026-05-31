<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

final class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(Request):Response $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Generate nonce and attach to Vite
        $nonce = Str::random(32);
        Vite::useCspNonce($nonce);

        // 2. Process Request
        $response = $next($request);

        // 3. Apply Security Headers
        if ($response instanceof Response) {
            // Apply standard headers
            $headers = config('security.headers', []);
            foreach ($headers as $header => $value) {
                $response->headers->set($header, $value);
            }

            // Build CSP
            $csp = config('security.csp', []);
            // Automatically append nonce to script-src and style-src if not already present
            if (isset($csp['script-src'])) {
                $csp['script-src'] .= " 'nonce-{$nonce}'";
            }
            if (isset($csp['style-src'])) {
                $csp['style-src'] .= " 'nonce-{$nonce}'";
            }

            $cspDirectives = [];
            foreach ($csp as $directive => $value) {
                $cspDirectives[] = empty($value) ? $directive : "$directive $value";
            }
            $response->headers->set('Content-Security-Policy', implode('; ', $cspDirectives));

            // Build Permissions Policy
            $permissions = config('security.permissions', []);
            $permDirectives = [];
            foreach ($permissions as $feature => $value) {
                $permDirectives[] = "$feature$value";
            }
            $response->headers->set('Permissions-Policy', implode(', ', $permDirectives));

            // COOP, CORP, COEP - Temporarily disabled as it breaks Vite dev server and cross-origin assets
            // $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');
            // $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
            // $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

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
