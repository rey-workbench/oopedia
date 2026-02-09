<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Services\Analytics\DashboardService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $userId = auth()->id();
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);

        $data = $this->dashboardService->getDashboardIndexData($userId, $isGuest);

        return Inertia::render('Mahasiswa/Dashboard/Index', $data);
    }

    public function inProgress()
    {
        $userId = auth()->id();
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);

        $materialsWithStats = $this->dashboardService->getInProgressData($userId, $isGuest);

        return Inertia::render('Mahasiswa/Dashboard/InProgress', [
            'materialsWithStats' => $materialsWithStats
        ]);
    }

    public function complete()
    {
        $userId = auth()->id();
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);

        $materialsWithStats = $this->dashboardService->getCompletedData($userId, $isGuest);

        return Inertia::render('Mahasiswa/Dashboard/Completed', [
            'materialsWithStats' => $materialsWithStats
        ]);
    }

    public function completed()
    {
        $materials = $this->dashboardService->getAllMaterials();
        return Inertia::render('Mahasiswa/Dashboard/Completed', compact('materials'));
    }
}
