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

        $this->app->bind(
            \App\Repositories\Interfaces\QuestionRepositoryInterface::class,
            \App\Repositories\Eloquent\QuestionRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\AnswerRepositoryInterface::class,
            \App\Repositories\Eloquent\AnswerRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\UeqSurveyRepositoryInterface::class,
            \App\Repositories\Eloquent\UeqSurveyRepository::class
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
