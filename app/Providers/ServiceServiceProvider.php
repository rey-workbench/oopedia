<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Contracts\Services\AdaptiveManagementServiceInterface;
use App\Contracts\Services\AdminDashboardServiceInterface;
use App\Contracts\Services\DashboardServiceInterface;
use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\LeaderboardServiceInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Contracts\Services\MslqServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Contracts\Services\QuizServiceInterface;
use App\Contracts\Services\StudentServiceInterface;
use App\Contracts\Services\SusResultServiceInterface;
use App\Contracts\Services\UeqSurveyServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Services\Adaptive\AdaptiveEngineService;
use App\Services\Adaptive\AdaptiveManagementService;
use App\Services\Analytics\AdminDashboardService;
use App\Services\Analytics\DashboardService;
use App\Services\Analytics\LeaderboardService;
use App\Services\Analytics\MslqService;
use App\Services\Analytics\SusResultService;
use App\Services\Analytics\UeqSurveyService;
use App\Services\Lms\GuestProgressService;
use App\Services\Lms\MaterialService;
use App\Services\Lms\QuizService;
use App\Services\User\PerformanceService;
use App\Services\User\StudentService;
use App\Services\User\UserService;
use Illuminate\Support\ServiceProvider;

final class ServiceServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        // LMS Domain
        $this->app->bind(MaterialServiceInterface::class, MaterialService::class);
        $this->app->bind(QuizServiceInterface::class, QuizService::class);
        $this->app->bind(GuestProgressServiceInterface::class, GuestProgressService::class);

        // User & Performance Domain
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(StudentServiceInterface::class, StudentService::class);
        $this->app->bind(PerformanceServiceInterface::class, PerformanceService::class);

        // Leaderboard
        $this->app->bind(LeaderboardServiceInterface::class, LeaderboardService::class);

        // Surveys, Analytics & Dashboards
        $this->app->bind(UeqSurveyServiceInterface::class, UeqSurveyService::class);
        $this->app->bind(MslqServiceInterface::class, MslqService::class);
        $this->app->bind(SusResultServiceInterface::class, SusResultService::class);
        $this->app->bind(DashboardServiceInterface::class, DashboardService::class);
        $this->app->bind(AdminDashboardServiceInterface::class, AdminDashboardService::class);

        // Adaptive Engine Core
        $this->app->bind(AdaptiveEngineServiceInterface::class, AdaptiveEngineService::class);
        $this->app->bind(AdaptiveManagementServiceInterface::class, AdaptiveManagementService::class);
    }
}
