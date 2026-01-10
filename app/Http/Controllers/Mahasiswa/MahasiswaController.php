<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Repositories\MaterialRepository;
use App\Services\LeaderboardService;

class MahasiswaController extends Controller
{
    protected $materialRepo;
    protected $leaderboardService;

    public function __construct(
        MaterialRepository $materialRepo,
        LeaderboardService $leaderboardService
    ) {
        $this->materialRepo = $materialRepo;
        $this->leaderboardService = $leaderboardService;
    }

    public function materi($slug = null)
    {
        if ($slug) {
            $material = $this->materialRepo->findBySlug($slug);
            return redirect()->route('mahasiswa.materials.show', $material->id);
        }
        return redirect()->route('mahasiswa.materials.index');
    }

    public function leaderboard()
    {
        $currentUserId = auth()->id();
        $data = $this->leaderboardService->getLeaderboardData($currentUserId);

        return view('mahasiswa.leaderboard', $data);
    }
}