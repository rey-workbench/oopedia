<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Interfaces\MaterialRepositoryInterface::class,
            \App\Repositories\Eloquent\MaterialRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\ProgressRepositoryInterface::class,
            \App\Repositories\Eloquent\ProgressRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
