<?php

use App\Exceptions\Domain\DomainException;
use App\Http\Middleware\AccessControl;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'access' => AccessControl::class,
        ]);

        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        // Trust Proxies
        $middleware->trustProxies(at: [
            '*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle domain exceptions (MaterialNotFoundException, etc.) with Inertia-aware redirect
        $exceptions->render(function (DomainException $e, Request $request) {
            if ($request->inertia()) {
                return back()->with('error', $e->getMessage());
            }

            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 422);
        });

        // Handle HTTP exceptions with Inertia error page
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->inertia()) {
                return Inertia::render('Error/Index', [
                    'status'  => $e->getStatusCode(),
                    'message' => $e->getMessage(),
                ])->toResponse($request)->setStatusCode($e->getStatusCode());
            }
        });
    })->create();
