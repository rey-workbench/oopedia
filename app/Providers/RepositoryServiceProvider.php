<?php

namespace App\Providers;

use App\Contracts\Repositories\AnswerRepositoryInterface;
use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\MediaRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Repositories\QuizAttemptRepositoryInterface;
use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Repositories\StudentStateRepositoryInterface;
use App\Contracts\Repositories\SubMaterialRepositoryInterface;
use App\Contracts\Repositories\UeqSurveyRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Repositories\AnswerRepository;
use App\Repositories\MaterialRepository;
use App\Repositories\MediaRepository;
use App\Repositories\ProgressRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\QuizAttemptRepository;
use App\Repositories\RoleRepository;
use App\Repositories\StudentStateRepository;
use App\Repositories\SubMaterialRepository;
use App\Repositories\UeqSurveyRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
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
}
