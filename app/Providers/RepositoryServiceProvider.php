<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Repositories\AdaptiveActionRepositoryInterface;
use App\Contracts\Repositories\AdaptiveExecutionLogRepositoryInterface;
use App\Contracts\Repositories\AdaptiveFactRepositoryInterface;
use App\Contracts\Repositories\AdaptiveRuleRepositoryInterface;
use App\Contracts\Repositories\AnswerRepositoryInterface;
use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\MediaRepositoryInterface;
use App\Contracts\Repositories\MslqRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Repositories\StudentStateRepositoryInterface;
use App\Contracts\Repositories\SusResultRepositoryInterface;
use App\Contracts\Repositories\UeqSurveyRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Repositories\AdaptiveActionRepository;
use App\Repositories\AdaptiveExecutionLogRepository;
use App\Repositories\AdaptiveFactRepository;
use App\Repositories\AdaptiveRuleRepository;
use App\Repositories\AnswerRepository;
use App\Repositories\MaterialRepository;
use App\Repositories\MediaRepository;
use App\Repositories\MslqRepository;
use App\Repositories\ProgressRepository;
use App\Repositories\QuestionRepository;
use App\Repositories\RoleRepository;
use App\Repositories\StudentStateRepository;
use App\Repositories\SusResultRepository;
use App\Repositories\UeqSurveyRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        $this->app->bind(MaterialRepositoryInterface::class, MaterialRepository::class);
        $this->app->bind(ProgressRepositoryInterface::class, ProgressRepository::class);
        $this->app->bind(QuestionRepositoryInterface::class, QuestionRepository::class);
        $this->app->bind(AnswerRepositoryInterface::class, AnswerRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UeqSurveyRepositoryInterface::class, UeqSurveyRepository::class);
        $this->app->bind(MediaRepositoryInterface::class, MediaRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(StudentStateRepositoryInterface::class, StudentStateRepository::class);
        $this->app->bind(MslqRepositoryInterface::class, MslqRepository::class);
        $this->app->bind(SusResultRepositoryInterface::class, SusResultRepository::class);
        $this->app->bind(AdaptiveRuleRepositoryInterface::class, AdaptiveRuleRepository::class);
        $this->app->bind(AdaptiveExecutionLogRepositoryInterface::class, AdaptiveExecutionLogRepository::class);
        $this->app->bind(AdaptiveFactRepositoryInterface::class, AdaptiveFactRepository::class);
        $this->app->bind(AdaptiveActionRepositoryInterface::class, AdaptiveActionRepository::class);
    }
}
