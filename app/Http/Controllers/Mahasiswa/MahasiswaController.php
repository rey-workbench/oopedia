<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Services\LeaderboardServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MahasiswaController extends Controller
{
    public function __construct(
        protected MaterialRepositoryInterface $materialRepo,
        protected LeaderboardServiceInterface $leaderboardService,
    ) {}

    public function materi(?string $slug = null): RedirectResponse
    {
        if ($slug) {
            $material = $this->materialRepo->findBySlug($slug);

            return redirect()->route('mahasiswa.materials.show', $material->id);
        }

        return redirect()->route('mahasiswa.materials.index');
    }

    public function leaderboard(): Response
    {
        $currentUserId = Auth::id();
        $data = $this->leaderboardService->getLeaderboardData($currentUserId);

        return Inertia::render('Mahasiswa/Leaderboard/Index', $data);
    }
}
