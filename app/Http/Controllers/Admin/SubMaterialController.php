<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubMaterial\StoreSubMaterialRequest;
use App\Http\Requests\SubMaterial\UpdateSubMaterialRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

final class SubMaterialController extends Controller
{
    public function __construct(
        protected MaterialServiceInterface $materialService,
        protected MaterialRepositoryInterface $materialRepo,
    ) {}

    public function index(string $materialId): Response|RedirectResponse
    {
        $material = $this->materialRepo->find($materialId);
        if (! $material) {
            return redirect()->route('admin.materials.index')
                ->with('error', 'Material tidak ditemukan');
        }

        $subMaterials = $this->materialService->getSubMaterialsByMaterial($materialId);

        return $this->render(
            'Admin/Materials/Submaterials/Index',
            compact('material', 'subMaterials'),
        );
    }

    public function create(string $materialId): Response|RedirectResponse
    {
        $material = $this->materialRepo->find($materialId);
        if (! $material) {
            return redirect()->route('admin.materials.index')
                ->with('error', 'Material tidak ditemukan');
        }

        return $this->render('Admin/Materials/Submaterials/Create/Index', compact('material'));
    }

    public function store(StoreSubMaterialRequest $request, string $materialId): RedirectResponse
    {
        $this->materialService->createSubMaterial($materialId, $request->validated());

        return redirect()->route('admin.materials.submaterials.index', $materialId)
            ->with('success', 'Sub-materi berhasil ditambahkan.');
    }

    public function edit(string $materialId, string $submaterialId): Response|RedirectResponse
    {
        $material    = $this->materialRepo->find($materialId);
        $submaterial = $this->materialService->getSubMaterialById($submaterialId);

        if (! $material || ! $submaterial) {
            return redirect()->route('admin.materials.index')
                ->with('error', 'Material atau sub-material tidak ditemukan');
        }

        return $this->render(
            'Admin/Materials/Submaterials/Edit/Index',
            compact('material', 'submaterial'),
        );
    }

    public function update(
        UpdateSubMaterialRequest $request,
        string $materialId,
        string $submaterialId,
    ): RedirectResponse {
        $this->materialService->updateSubMaterial($submaterialId, $request->validated());

        return redirect()->route('admin.materials.submaterials.index', $materialId)
            ->with('success', 'Sub-materi berhasil diperbarui.');
    }

    public function destroy(string $materialId, string $submaterialId): RedirectResponse
    {
        $this->materialService->deleteSubMaterial($submaterialId);

        return redirect()->route('admin.materials.submaterials.index', $materialId)
            ->with('success', 'Sub-materi berhasil dihapus.');
    }

    public function getJson(string $materialId): JsonResponse
    {
        $data = $this->materialService->getSubMaterialsSimple($materialId);

        return response()->json($data);
    }
}
