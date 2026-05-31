<?php

namespace App\Providers;

use App\Enums\User\RoleName;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureHttps();
        $this->configureRateLimiting();
    }

    protected function configureHttps(): void
    {
        if (app()->environment('production') || str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('anti_scrape', fn(Request $request) => Limit::perMinute(60)->by($request->ip())
            ->response(function () {
                if (request()->inertia()) {
                    return inertia('Error/Index', [
                        'status' => 429,
                        'message' => 'Too many requests. Please slow down.',
                    ])->toResponse(request())->setStatusCode(429);
                }

                return response()->json([
                    'message' => 'Too many requests. Please slow down.',
                ], 429);
            }));

        RateLimiter::for('api', function (Request $request) {
            $config = config('rate_limiting.api');

            return Limit::perMinute($config['limit'])
                ->by($request->user()?->id ?: $request->ip())
                ->response(fn() => response()->json([
                    'message' => 'Too many requests. Please slow down.',
                ], 429));
        });

        RateLimiter::for(RoleName::GUEST->value, function (Request $request) {
            $config = config('rate_limiting.' . RoleName::GUEST->value);

            return Limit::perMinute($config['limit'])
                ->by($request->ip())
                ->response(fn() => response()->json([
                    'message' => 'Too many requests. Please slow down.',
                ], 429));
        });
    }
}
