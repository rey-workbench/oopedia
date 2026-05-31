<?php

use App\Exceptions\Domain\DomainException;
use App\Http\Middleware\AccessControl;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Middleware\SecurityHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders()
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'access' => AccessControl::class,
        ]);

        $middleware->append(SecurityHeaders::class);

        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->trustProxies(at: [
            '*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Domain exceptions - Inertia-aware redirect
        $exceptions->render(function (DomainException $e, Request $request) {
            if ($request->inertia()) {
                return back()->with('error', $e->getMessage());
            }

            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 422);
        });

        // HTTP exceptions - Inertia error page
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->inertia()) {
                return Inertia::render('Error/Index', [
                    'status' => $e->getStatusCode(),
                    'message' => $e->getMessage(),
                ])->toResponse($request)->setStatusCode($e->getStatusCode());
            }
        });

        // Validation exceptions - JSON response for API
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        });

    })->create();
