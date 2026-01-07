<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Services\MaterialViewService;

class MaterialController extends Controller
{
    protected $materialViewService;

    public function __construct(MaterialViewService $materialViewService)
    {
        $this->materialViewService = $materialViewService;
    }

    public function show($id)
    {
        $userId = auth()->id();
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);

        $data = $this->materialViewService->getMaterialDetail($id, $userId, $isGuest);

        return view('mahasiswa.materials.show', $data);
    }

    public function index()
    {
        $userId = auth()->id();
        $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);

        $materials = $this->materialViewService->getMaterialsList($userId, $isGuest);

        return view('mahasiswa.materials.index', compact('materials'));
    }

    public function reset($id)
    {
        $this->materialViewService->resetMaterialProgress(auth()->id(), $id);

        return redirect()->route('mahasiswa.materials.questions.show', ['material' => $id])
            ->with('success', 'Progress direset. Anda dapat mengerjakan soal kembali.');
    }
}