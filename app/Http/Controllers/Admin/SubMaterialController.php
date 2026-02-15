<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\Services\SubMaterialServiceInterface;
use App\Contracts\Repositories\MaterialRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubMaterialController extends Controller
{
    public function __construct(
        protected SubMaterialServiceInterface $subMaterialService,
        protected MaterialRepositoryInterface $materialRepo
    ) {}

    public function index(int $materialId)
    {
        $material = $this->materialRepo->find($materialId);
        if (!$material) {
            return redirect()->route('admin.materials.index')
                ->with('error', 'Material tidak ditemukan');
        }
        
        $subMaterials = $this->subMaterialService->getSubMaterialsByMaterial($materialId);
        return Inertia::render('Admin/Materials/Submaterials/Index', compact('material', 'subMaterials'));
    }

    public function create(int $materialId)
    {
        $material = $this->materialRepo->find($materialId);
        if (!$material) {
            return redirect()->route('admin.materials.index')
                ->with('error', 'Material tidak ditemukan');
        }
        
        return Inertia::render('Admin/Materials/Submaterials/Create/Index', compact('material'));
    }

    public function store(Request $request, int $materialId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'jenis_konten' => 'required|in:teori,sintaks,mixed',
            'order' => 'required|integer',
        ]);

        try {
            $this->subMaterialService->createSubMaterial($materialId, $request->all());

            return redirect()->route('admin.materials.submaterials.index', $materialId)
                ->with('success', 'Sub-materi berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(int $materialId, int $submaterialId)
    {
        $material = $this->materialRepo->find($materialId);
        $submaterial = $this->subMaterialService->getSubMaterialById($submaterialId);
        
        if (!$material || !$submaterial) {
            return redirect()->route('admin.materials.index')
                ->with('error', 'Material atau sub-material tidak ditemukan');
        }
        
        return Inertia::render('Admin/Materials/Submaterials/Edit/Index', compact('material', 'submaterial'));
    }

    public function update(Request $request, int $materialId, int $submaterialId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'jenis_konten' => 'required|in:teori,sintaks,mixed',
            'order' => 'required|integer',
        ]);

        try {
            $this->subMaterialService->updateSubMaterial($submaterialId, $request->all());

            return redirect()->route('admin.materials.submaterials.index', $materialId)
                ->with('success', 'Sub-materi berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(int $materialId, int $submaterialId)
    {
        try {
            $this->subMaterialService->deleteSubMaterial($submaterialId);
            
            return redirect()->route('admin.materials.submaterials.index', $materialId)
                ->with('success', 'Sub-materi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.materials.submaterials.index', $materialId)
                ->with('error', $e->getMessage());
        }
    }

    public function getJson(int $materialId)
    {
        $data = $this->subMaterialService->getSubMaterialsSimple($materialId);
        return response()->json($data);
    }
}
