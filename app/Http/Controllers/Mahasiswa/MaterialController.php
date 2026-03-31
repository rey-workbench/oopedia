<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Services\GuestProgressServiceInterface;
use App\Contracts\Services\MaterialViewServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\StudentState;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class MaterialController extends Controller
{
    public function __construct(
        protected MaterialViewServiceInterface $materialViewService,
        protected GuestProgressServiceInterface $guestProgressService,
    ) {}

    public function index(): Response
    {
        $isGuest   = $this->isGuest();
        $materials = $this->materialViewService->getMaterialsList(Auth::id(), $isGuest);

        return $this->render('Mahasiswa/Materials/Index', [
            'materials' => $materials,
            'isGuest'   => $isGuest,
        ]);
    }

    public function show(string $id): RedirectResponse|Response
    {
        $isGuest = $this->isGuest();
        $userId  = Auth::id();

        if ($this->isMaterialLocked($id, $userId, $isGuest)) {
            return redirect()->route('mahasiswa.materials.index')
                ->with('error', 'Materi ini masih terkunci. Selesaikan materi sebelumnya!');
        }

        $data = $this->materialViewService->getMaterialDetail($id, $userId, $isGuest);

        return $this->render('Mahasiswa/Materials/Show/Index', $data);
    }

    public function showSubMaterial(string $materialId, string $subMaterialId): Response
    {
        $data = $this->materialViewService->getSubMaterialDetail($materialId, $subMaterialId, $this->isGuest());

        return $this->render('Mahasiswa/SubMaterials/Show/Index', $data);
    }

    public function reset(string $id, Request $request): RedirectResponse
    {
        if ($this->isGuest()) {
            $this->guestProgressService->resetMaterialProgress($id);
        } else {
            $this->materialViewService->resetMaterialProgress(Auth::id(), $id);
        }

        return redirect()->route('mahasiswa.materials.questions.show', ['material' => $id])
            ->with('success', 'Progress direset. Anda dapat mengerjakan soal kembali.');
    }

    private function isMaterialLocked(string $materialId, ?string $userId, bool $isGuest): bool
    {
        $material = Material::where('id', $materialId)->first();

        if (! $material) {
            return true;
        }

        if ($isGuest) {
            $allMaterials    = Material::orderBy('created_at', 'asc')->get();
            $totalMaterials  = $allMaterials->count();
            $materialIndex   = $allMaterials->search(fn ($m) => $m->id === $materialId);

            return $materialIndex >= ceil($totalMaterials / 2);
        }

        $studentState    = StudentState::where('user_id', $userId)->first();
        $unlockedModules = $studentState?->learning_profile['unlocked_modules'] ?? [];

        $allMaterials  = Material::orderBy('created_at', 'asc')->select('id', 'module_id')->get();
        $firstModuleId = $allMaterials->whereNotNull('module_id')->min('module_id');

        $moduleId      = $material->module_id;
        $isFirstModule = $moduleId !== null && $moduleId == $firstModuleId;
        $isUnlocked    = empty($moduleId) || $isFirstModule || in_array($moduleId, $unlockedModules);

        return ! $isUnlocked;
    }
}
