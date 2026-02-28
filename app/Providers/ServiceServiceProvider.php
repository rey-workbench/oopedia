<?php

namespace App\Providers;

use App\Contracts\Services\AdaptiveEngineServiceInterface;
// Service Interfaces
use App\Contracts\Services\AdminDashboardServiceInterface;
use App\Contracts\Services\DashboardServiceInterface;
use App\Contracts\Services\FactGatheringServiceInterface;
use App\Contracts\Services\LeaderboardServiceInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Contracts\Services\MaterialViewServiceInterface;
use App\Contracts\Services\NextActionResolverServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\ProgressServiceInterface;
use App\Contracts\Services\QuestionAnswerServiceInterface;
use App\Contracts\Services\QuestionListingServiceInterface;
use App\Contracts\Services\QuestionServiceInterface;
use App\Contracts\Services\QuizRewardServiceInterface;
use App\Contracts\Services\StreakServiceInterface;
use App\Contracts\Services\StudentServiceInterface;
use App\Contracts\Services\SubMaterialServiceInterface;
use App\Contracts\Services\UeqSurveyServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Services\Adaptive\AdaptiveEngineService;
// Service Implementations
use App\Services\Adaptive\FactGatheringService;
use App\Services\Adaptive\NextActionResolverService;
use App\Services\Analytics\AdminDashboardService;
use App\Services\Analytics\DashboardService;
use App\Services\Analytics\LeaderboardService;
use App\Services\Analytics\UeqSurveyService;
use App\Services\Gamification\QuizRewardService;
use App\Services\Gamification\StreakService;
use App\Services\Lms\Material\MaterialService;
use App\Services\Lms\Material\MaterialViewService;
use App\Services\Lms\Material\SubMaterialService;
use App\Services\Lms\ProgressService;
use App\Services\Lms\Question\QuestionAnswerService;
use App\Services\Lms\Question\QuestionListingService;
use App\Services\Lms\Question\QuestionService;
use App\Services\User\PerformanceService;
use App\Services\User\StudentService;
use App\Services\User\UserService;
use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind service interfaces to implementations
        $this->app->bind(MaterialServiceInterface::class, MaterialService::class);
        $this->app->bind(SubMaterialServiceInterface::class, SubMaterialService::class);
        $this->app->bind(QuestionServiceInterface::class, QuestionService::class);
        $this->app->bind(QuestionAnswerServiceInterface::class, QuestionAnswerService::class);
        $this->app->bind(QuestionListingServiceInterface::class, QuestionListingService::class);
        $this->app->bind(ProgressServiceInterface::class, ProgressService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(StudentServiceInterface::class, StudentService::class);
        $this->app->bind(DashboardServiceInterface::class, DashboardService::class);
        $this->app->bind(LeaderboardServiceInterface::class, LeaderboardService::class);
        $this->app->bind(AdaptiveEngineServiceInterface::class, AdaptiveEngineService::class);
        $this->app->bind(UeqSurveyServiceInterface::class, UeqSurveyService::class);
        $this->app->bind(MaterialViewServiceInterface::class, MaterialViewService::class);
        $this->app->bind(QuizRewardServiceInterface::class, QuizRewardService::class);
        $this->app->bind(StreakServiceInterface::class, StreakService::class);
        $this->app->bind(PerformanceServiceInterface::class, PerformanceService::class);
        $this->app->bind(FactGatheringServiceInterface::class, FactGatheringService::class);
        $this->app->bind(NextActionResolverServiceInterface::class, NextActionResolverService::class);
        $this->app->bind(AdminDashboardServiceInterface::class, AdminDashboardService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
