<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Repositories\MaterialRepository;
use App\Services\Analytics\LeaderboardService;
use Inertia\Inertia;

class MahasiswaController extends Controller
{
    public function __construct(
        protected \App\Contracts\Repositories\MaterialRepositoryInterface $materialRepo,
        protected \App\Contracts\Services\LeaderboardServiceInterface $leaderboardService
    ) {}

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

        return Inertia::render('Mahasiswa/Leaderboard/Index', $data);
    }
}