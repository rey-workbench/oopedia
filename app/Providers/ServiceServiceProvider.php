<?php

namespace App\Providers;

use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Contracts\Services\AdminDashboardServiceInterface;
use App\Contracts\Services\DashboardServiceInterface;
use App\Contracts\Services\FactGatheringServiceInterface;
use App\Contracts\Services\GamificationServiceInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\LeaderboardServiceInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Contracts\Services\MaterialViewServiceInterface;
use App\Contracts\Services\MslqServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\QuestionAnswerServiceInterface;
use App\Contracts\Services\QuestionListingServiceInterface;
use App\Contracts\Services\QuestionServiceInterface;
use App\Contracts\Services\StudentServiceInterface;
use App\Contracts\Services\SubMaterialServiceInterface;
use App\Contracts\Services\SusResultServiceInterface;
use App\Contracts\Services\UeqSurveyServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Services\Adaptive\AdaptiveEngineService;
use App\Services\Adaptive\FactGatheringService;
use App\Services\Analytics\AdminDashboardService;
use App\Services\Analytics\DashboardService;
use App\Services\Analytics\LeaderboardService;
use App\Services\Analytics\MslqService;
use App\Services\Analytics\SusResultService;
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
    public function register(): void
    {
        $this->app->bind(MaterialServiceInterface::class, MaterialService::class);
        $this->app->bind(SubMaterialServiceInterface::class, SubMaterialService::class);
        $this->app->bind(MaterialViewServiceInterface::class, MaterialViewService::class);
        $this->app->bind(QuestionServiceInterface::class, QuestionService::class);
        $this->app->bind(QuestionAnswerServiceInterface::class, QuestionAnswerService::class);
        $this->app->bind(QuestionListingServiceInterface::class, QuestionListingService::class);
        $this->app->bind(GuestProgressServiceInterface::class, GuestProgressService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(StudentServiceInterface::class, StudentService::class);
        $this->app->bind(PerformanceServiceInterface::class, PerformanceService::class);
        $this->app->bind(DashboardServiceInterface::class, DashboardService::class);
        $this->app->bind(LeaderboardServiceInterface::class, LeaderboardService::class);
        $this->app->bind(AdminDashboardServiceInterface::class, AdminDashboardService::class);
        $this->app->bind(UeqSurveyServiceInterface::class, UeqSurveyService::class);
        $this->app->bind(GamificationServiceInterface::class, GamificationService::class);
        $this->app->singleton(AdaptiveEngineServiceInterface::class, AdaptiveEngineService::class);
        $this->app->bind(FactGatheringServiceInterface::class, FactGatheringService::class);
        $this->app->bind(MslqServiceInterface::class, MslqService::class);
        $this->app->bind(SusResultServiceInterface::class, SusResultService::class);
    }
}
