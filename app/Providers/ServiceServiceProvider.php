<?php

namespace App\Providers;

// Service Interfaces
use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Contracts\Services\AdminDashboardServiceInterface;
use App\Contracts\Services\DashboardServiceInterface;
use App\Contracts\Services\FactGatheringServiceInterface;
use App\Contracts\Services\GamificationServiceInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\LeaderboardServiceInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Contracts\Services\MaterialViewServiceInterface;
use App\Contracts\Services\NextActionResolverServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\QuestionAnswerServiceInterface;
use App\Contracts\Services\QuestionListingServiceInterface;
use App\Contracts\Services\QuestionServiceInterface;
use App\Contracts\Services\StudentServiceInterface;
use App\Contracts\Services\SubMaterialServiceInterface;
use App\Contracts\Services\UeqSurveyServiceInterface;
use App\Contracts\Services\UserServiceInterface;

// Service Implementations
use App\Services\Adaptive\AdaptiveEngineService;
use App\Services\Adaptive\FactGatheringService;
use App\Services\Adaptive\NextActionResolverService;
use App\Services\Analytics\AdminDashboardService;
use App\Services\Analytics\DashboardService;
use App\Services\Analytics\LeaderboardService;
use App\Services\Analytics\UeqSurveyService;
use App\Services\Gamification\GamificationService;
use App\Services\Lms\GuestProgressService;
use App\Services\Lms\MaterialService;
use App\Services\Lms\MaterialViewService;
use App\Services\Lms\QuestionAnswerService;
use App\Services\Lms\QuestionListingService;
use App\Services\Lms\QuestionService;
use App\Services\Lms\SubMaterialService;
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
        // Lms Services
        $this->app->bind(MaterialServiceInterface::class , MaterialService::class);
        $this->app->bind(SubMaterialServiceInterface::class , SubMaterialService::class);
        $this->app->bind(MaterialViewServiceInterface::class , MaterialViewService::class);
        $this->app->bind(QuestionServiceInterface::class , QuestionService::class);
        $this->app->bind(QuestionAnswerServiceInterface::class , QuestionAnswerService::class);
        $this->app->bind(QuestionListingServiceInterface::class , QuestionListingService::class);
        $this->app->bind(GuestProgressServiceInterface::class , GuestProgressService::class);

        // User Services
        $this->app->bind(UserServiceInterface::class , UserService::class);
        $this->app->bind(StudentServiceInterface::class , StudentService::class);
        $this->app->bind(PerformanceServiceInterface::class , PerformanceService::class);

        // Analytics Services
        $this->app->bind(DashboardServiceInterface::class , DashboardService::class);
        $this->app->bind(LeaderboardServiceInterface::class , LeaderboardService::class);
        $this->app->bind(AdminDashboardServiceInterface::class , AdminDashboardService::class);
        $this->app->bind(UeqSurveyServiceInterface::class , UeqSurveyService::class);

        // Gamification (unified)
        $this->app->bind(GamificationServiceInterface::class , GamificationService::class);

        // Adaptive Services
        $this->app->bind(AdaptiveEngineServiceInterface::class , AdaptiveEngineService::class);
        $this->app->bind(FactGatheringServiceInterface::class , FactGatheringService::class);
        $this->app->bind(NextActionResolverServiceInterface::class , NextActionResolverService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    //
    }
}
