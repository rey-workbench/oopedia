<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Services\MaterialViewServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MaterialController extends Controller
{
    public function __construct(
        protected MaterialViewServiceInterface $materialViewService,
    ) {}

    public function showSubMaterial(int|string $materialId, int|string $subMaterialId): Response
    {
        $data = $this->materialViewService->getSubMaterialDetail((int) $materialId, (int) $subMaterialId, $this->isGuest());

        return Inertia::render('Mahasiswa/SubMaterials/Show/Index', $data);
    }

    public function show(int|string $id): Response
    {
        $data = $this->materialViewService->getMaterialDetail((int) $id, Auth::id(), $this->isGuest());

        return Inertia::render('Mahasiswa/Materials/Show/Index', $data);
    }

    public function index(): Response
    {
        $isGuest = $this->isGuest();
        $materials = $this->materialViewService->getMaterialsList(Auth::id(), $isGuest);

        return Inertia::render('Mahasiswa/Materials/Index', [
            'materials' => $materials,
            'isGuest' => $isGuest,
        ]);
    }

    public function reset(int|string $id, Request $request): RedirectResponse
    {
        $intId = (int) $id;
        if ($this->isGuest()) {
            $allProgress = session('guest_progress', []);
            $filtered = collect($allProgress)
                ->filter(fn ($v, $k) => ! str_starts_with((string) $k, $intId . '_'))
                ->all();
            session(['guest_progress' => $filtered]);
            session()->forget('guest_progress.' . $intId);
        } else {
            $this->materialViewService->resetMaterialProgress(Auth::id(), $intId);
        }

        return redirect()->route('mahasiswa.materials.questions.show', ['material' => $intId])
            ->with('success', 'Progress direset. Anda dapat mengerjakan soal kembali.');
    }
}
