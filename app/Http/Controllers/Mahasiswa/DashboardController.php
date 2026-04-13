<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Services\DashboardServiceInterface;
use App\Contracts\Services\LeaderboardServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class DashboardController extends Controller
{
    public function __construct(
        protected DashboardServiceInterface $dashboardService,
        protected LeaderboardServiceInterface $leaderboardService,
    ) {}

    public function index(): Response
    {
        $data                    = $this->dashboardService->getDashboardIndexData(Auth::id(), $this->isGuest());
        $leaderboard             = $this->leaderboardService->getLeaderboardData(Auth::id());
        $data['currentUserRank'] = $leaderboard['currentUserRank'];

        return $this->render('Mahasiswa/Dashboard/Index', $data);
    }

    public function inProgress(): Response
    {
        $materialsWithStats = $this->dashboardService->getInProgressData(Auth::id(), $this->isGuest());

        return $this->render('Mahasiswa/Dashboard/InProgress/Index', [
            'materialsWithStats' => $materialsWithStats,
        ]);
    }

    public function complete(): Response
    {
        $materialsWithStats = $this->dashboardService->getCompletedData(Auth::id(), $this->isGuest());

        return $this->render('Mahasiswa/Dashboard/Completed/Index', [
            'materialsWithStats' => $materialsWithStats,
        ]);
    }

    public function leaderboard(): Response
    {
        $data = $this->leaderboardService->getLeaderboardData(Auth::id());

        return $this->render('Mahasiswa/Leaderboard/Index', $data);
    }
}
