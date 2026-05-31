<?php

namespace App\Exceptions;

use App\Exceptions\Domain\DomainException;
use App\Services\Security\HoneypotService;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler
{
    /**
     * Konfigurasi penanganan error/exception untuk aplikasi.
     */
    public function __invoke(Exceptions $exceptions): void
    {
        // Domain exceptions - Inertia-aware redirect
        $exceptions->render(function (DomainException $e, Request $request) {
            if ($request->inertia()) {
                return back()->with('error', $e->getMessage());
            }

            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 422);
        });

        // HTTP exceptions - Inertia error page and Honeypot
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() === 404 && $trapResponse = HoneypotService::intercept($request)) {
                return $trapResponse;
            }

            // Fallback for API or JSON expected responses (let Laravel handle natively)
            if ($request->expectsJson() || $request->is('api/*')) {
                return null;
            }

            return Inertia::render('Error/Index', [
                'status'  => $e->getStatusCode(),
                'message' => $e->getMessage(),
            ])->toResponse($request)->setStatusCode($e->getStatusCode());
        });

        // Validation exceptions - JSON response for API
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors'  => $e->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        });
    }
}
