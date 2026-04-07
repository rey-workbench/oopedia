<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->configureHttps();
        $this->configureRateLimiting();
    }

    protected function configureHttps(): void
    {
        if (app()->environment('production') || str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $config = config('rate_limiting.api');

            return Limit::perMinute($config['limit'])
                ->by(optional($request->user())->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many requests. Please slow down.',
                    ], 429);
                });
        });

        RateLimiter::for('guest', function (Request $request) {
            $config = config('rate_limiting.guest');

            return Limit::perMinute($config['limit'])
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many requests. Please slow down.',
                    ], 429);
                });
        });
    }
}
