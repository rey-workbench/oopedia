<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Contracts\Services\PerformanceServiceInterface;
use App\Http\Controllers\Controller;
use App\Traits\HandlesAdaptiveState;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

final class MaterialController extends Controller
{
    use HandlesAdaptiveState;

    public function __construct(
        protected MaterialServiceInterface $materialService,
        protected PerformanceServiceInterface $performanceService,
        protected GuestProgressServiceInterface $guestProgressService,
    ) {}

    protected function getPerformanceService(): PerformanceServiceInterface
    {
        return $this->performanceService;
    }

    protected function getGuestProgressService(): GuestProgressServiceInterface
    {
        return $this->guestProgressService;
    }

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

    public function subMaterial(string $materialId, string $subMaterialId): Response
    {
        $isGuest = $this->isGuest();
        $data    = $this->materialService->getSubMaterialDetail($materialId, $subMaterialId, $isGuest);

        return $this->render('Mahasiswa/SubMaterials/Show/Index', $data);
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
