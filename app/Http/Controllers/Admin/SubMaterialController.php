<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Services\SubMaterialServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubMaterial\StoreSubMaterialRequest;
use App\Http\Requests\SubMaterial\UpdateSubMaterialRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SubMaterialController extends Controller
{
    public function __construct(
        protected SubMaterialServiceInterface $subMaterialService,
        protected MaterialRepositoryInterface $materialRepo,
    ) {}

    public function index(int $materialId): Response|RedirectResponse
    {
        $material = $this->materialRepo->find($materialId);
        if (! $material) {
            return redirect()->route('admin.materials.index')
                ->with('error', 'Material tidak ditemukan');
        }

        $subMaterials = $this->subMaterialService->getSubMaterialsByMaterial($materialId);

        return Inertia::render('Admin/Materials/Submaterials/Index', compact('material', 'subMaterials'));
    }

    public function create(int $materialId): Response|RedirectResponse
    {
        $material = $this->materialRepo->find($materialId);
        if (! $material) {
            return redirect()->route('admin.materials.index')
                ->with('error', 'Material tidak ditemukan');
        }

        return Inertia::render('Admin/Materials/Submaterials/Create/Index', compact('material'));
    }

    public function store(StoreSubMaterialRequest $request, int $materialId): RedirectResponse
    {
        $this->subMaterialService->createSubMaterial($materialId, $request->validated());

        return redirect()->route('admin.materials.submaterials.index', $materialId)
            ->with('success', 'Sub-materi berhasil ditambahkan.');
    }

    public function edit(int $materialId, int $submaterialId): Response|RedirectResponse
    {
        $material = $this->materialRepo->find($materialId);
        $submaterial = $this->subMaterialService->getSubMaterialById($submaterialId);

        if (! $material || ! $submaterial) {
            return redirect()->route('admin.materials.index')
                ->with('error', 'Material atau sub-material tidak ditemukan');
        }

        return Inertia::render('Admin/Materials/Submaterials/Edit/Index', compact('material', 'submaterial'));
    }

    public function update(UpdateSubMaterialRequest $request, int $materialId, int $submaterialId): RedirectResponse
    {
        $this->subMaterialService->updateSubMaterial($submaterialId, $request->validated());

        return redirect()->route('admin.materials.submaterials.index', $materialId)
            ->with('success', 'Sub-materi berhasil diperbarui.');
    }

    public function destroy(int $materialId, int $submaterialId): RedirectResponse
    {
        $this->subMaterialService->deleteSubMaterial($submaterialId);

        return redirect()->route('admin.materials.submaterials.index', $materialId)
            ->with('success', 'Sub-materi berhasil dihapus.');
    }

    public function getJson(int $materialId): JsonResponse
    {
        $data = $this->subMaterialService->getSubMaterialsSimple($materialId);

        return response()->json($data);
    }
}
