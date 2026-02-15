<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repository Interfaces
use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Repositories\AnswerRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Repositories\UeqSurveyRepositoryInterface;
use App\Contracts\Repositories\MediaRepositoryInterface;
use App\Contracts\Repositories\SubMaterialRepositoryInterface;
use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Repositories\QuizAttemptRepositoryInterface;
use App\Contracts\Repositories\StudentStateRepositoryInterface;

// Repository Implementations
use App\Repositories\MaterialRepository;
use App\Repositories\ProgressRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\AnswerRepository;
use App\Repositories\UserRepository;
use App\Repositories\UeqSurveyRepository;
use App\Repositories\MediaRepository;
use App\Repositories\SubMaterialRepository;
use App\Repositories\RoleRepository;
use App\Repositories\QuizAttemptRepository;
use App\Repositories\StudentStateRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind repository interfaces to implementations
        $this->app->bind(MaterialRepositoryInterface::class, MaterialRepository::class);
        $this->app->bind(ProgressRepositoryInterface::class, ProgressRepository::class);
        $this->app->bind(QuestionRepositoryInterface::class, QuestionRepository::class);
        $this->app->bind(AnswerRepositoryInterface::class, AnswerRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UeqSurveyRepositoryInterface::class, UeqSurveyRepository::class);
        $this->app->bind(MediaRepositoryInterface::class, MediaRepository::class);
        $this->app->bind(SubMaterialRepositoryInterface::class, SubMaterialRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(QuizAttemptRepositoryInterface::class, QuizAttemptRepository::class);
        $this->app->bind(StudentStateRepositoryInterface::class, StudentStateRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
