<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Services\DashboardServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardServiceInterface $dashboardService,
    ) {}

    public function index(): Response
    {
        $userId = Auth::id();
        $isGuest = ! Auth::check() || Auth::user()->role_id === 4;

        $data = $this->dashboardService->getDashboardIndexData($userId, $isGuest);

        return Inertia::render('Mahasiswa/Dashboard/Index', $data);
    }

    public function inProgress(): Response
    {
        $userId = Auth::id();
        $isGuest = ! Auth::check() || Auth::user()->role_id === 4;

        $materialsWithStats = $this->dashboardService->getInProgressData($userId, $isGuest);

        return Inertia::render('Mahasiswa/Dashboard/InProgress/Index', [
            'materialsWithStats' => $materialsWithStats,
        ]);
    }

    public function complete(): Response
    {
        $userId = Auth::id();
        $isGuest = ! Auth::check() || Auth::user()->role_id === 4;

        $materialsWithStats = $this->dashboardService->getCompletedData($userId, $isGuest);

        return Inertia::render('Mahasiswa/Dashboard/Completed/Index', [
            'materialsWithStats' => $materialsWithStats,
        ]);
    }

    public function completed(): Response
    {
        $materials = $this->dashboardService->getAllMaterials();

        return Inertia::render('Mahasiswa/Dashboard/Completed/Index', compact('materials'));
    }
}
