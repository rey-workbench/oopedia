<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

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

        return view('mahasiswa.dashboard.index', $data);
    }

    public function inProgress()
    {
        $userId = auth()->id();
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);

        $materialsWithStats = $this->dashboardService->getInProgressData($userId, $isGuest);

        return view('mahasiswa.dashboard.in-progress', [
            'materialsWithStats' => $materialsWithStats
        ]);
    }

    public function complete()
    {
        $userId = auth()->id();
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);

        $materialsWithStats = $this->dashboardService->getCompletedData($userId, $isGuest);

        return view('mahasiswa.dashboard.completed', [
            'materialsWithStats' => $materialsWithStats
        ]);
    }

    public function completed()
    {
        $materials = $this->dashboardService->getAllMaterials();
        return view('mahasiswa.dashboard.completed', compact('materials'));
    }
}
