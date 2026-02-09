<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Services\Lms\Material\MaterialViewService;
use Inertia\Inertia;

class MaterialController extends Controller
{
    protected $materialViewService;

    public function __construct(MaterialViewService $materialViewService)
    {
        $this->materialViewService = $materialViewService;
    }

    public function showSubMaterial($materialId, $subMaterialId)
    {
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);
        
        $data = $this->materialViewService->getSubMaterialDetail($materialId, $subMaterialId, $isGuest);
        
        return Inertia::render('Mahasiswa/SubMaterials/Show', $data);
    }

    public function show($id)
    {
        $userId = auth()->id();
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);

        $data = $this->materialViewService->getMaterialDetail($id, $userId, $isGuest);

        return Inertia::render('Mahasiswa/Materials/Show', $data);
    }

    public function index()
    {
        $userId = auth()->id();
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);

        $materials = $this->materialViewService->getMaterialsList($userId, $isGuest);

        return Inertia::render('Mahasiswa/Materials/Index', [
            'materials' => $materials,
            'isGuest' => $isGuest
        ]);
    }

    public function reset($id)
    {
        $this->materialViewService->resetMaterialProgress(auth()->id(), $id);

        return redirect()->route('mahasiswa.materials.questions.show', ['material' => $id])
            ->with('success', 'Progress direset. Anda dapat mengerjakan soal kembali.');
    }
}