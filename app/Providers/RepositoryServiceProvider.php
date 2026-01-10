<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\MaterialRepository;
use App\Repositories\ProgressRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\AnswerRepository;
use App\Repositories\UserRepository;
use App\Repositories\UeqSurveyRepository;
use App\Repositories\QuestionBankRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register repositories as singletons
        $this->app->singleton(MaterialRepository::class);
        $this->app->singleton(ProgressRepository::class);
        $this->app->singleton(QuestionRepository::class);
        $this->app->singleton(AnswerRepository::class);
        $this->app->singleton(UserRepository::class);
        $this->app->singleton(UeqSurveyRepository::class);
        $this->app->singleton(QuestionBankRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
