<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Services\MaterialViewServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MaterialController extends Controller
{
    public function __construct(
        protected MaterialViewServiceInterface $materialViewService,
    ) {}

    public function showSubMaterial(int $materialId, int $subMaterialId): Response
    {
        $isGuest = ! Auth::check() || Auth::user()->role_id === 4;

        $data = $this->materialViewService->getSubMaterialDetail($materialId, $subMaterialId, $isGuest);

        return Inertia::render('Mahasiswa/SubMaterials/Show/Index', $data);
    }

    public function show(int $id): Response
    {
        $userId = Auth::id();
        $isGuest = ! Auth::check() || Auth::user()->role_id === 4;

        $data = $this->materialViewService->getMaterialDetail($id, $userId, $isGuest);

        return Inertia::render('Mahasiswa/Materials/Show/Index', $data);
    }

    public function index(): Response
    {
        $userId = Auth::id();
        $isGuest = ! Auth::check() || Auth::user()->role_id === 4;

        $materials = $this->materialViewService->getMaterialsList($userId, $isGuest);

        return Inertia::render('Mahasiswa/Materials/Index', [
            'materials' => $materials,
            'isGuest' => $isGuest,
        ]);
    }

    public function reset(int $id): RedirectResponse
    {
        $this->materialViewService->resetMaterialProgress(Auth::id(), $id);

        return redirect()->route('mahasiswa.materials.questions.show', ['material' => $id])
            ->with('success', 'Progress direset. Anda dapat mengerjakan soal kembali.');
    }
}
