<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Services\MaterialServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

final class MaterialController extends Controller
{
    public function __construct(
        protected MaterialServiceInterface $materialService,
    ) {}

    public function index(): Response
    {
        $userId  = $this->getUserId();
        $isGuest = $this->isGuest();

        $materials = $this->materialService->getMaterialsList((string) $userId, $isGuest);

        return $this->render('Mahasiswa/Materials/Index', [
            'materials' => $materials,
        ]);
    }

    public function show(string $materialId): Response
    {
        $userId        = $this->getUserId();
        $isGuest       = $this->isGuest();
        $guestProgress = $this->getGuestProgress();

        $data = $this->materialService->getMaterialDetail($materialId, (string) $userId, $isGuest, $guestProgress);

        return $this->render('Mahasiswa/Materials/Show/Index', $data);
    }

    public function reset(string $materialId): RedirectResponse
    {
        $userId = $this->getUserId();
        if ($userId) {
            $this->materialService->resetMaterialProgress((string) $userId, $materialId);
        }

        return redirect()->route('mahasiswa.materials.show', $materialId);
    }
}
