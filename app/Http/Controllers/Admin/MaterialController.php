<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\MaterialServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Material\StoreMaterialRequest;
use App\Http\Requests\Material\UpdateMaterialRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MaterialController extends Controller
{
    public function __construct(protected
        MaterialServiceInterface $materialService,
        )
    {
    }

    public function index(Request $request): Response
    {
        $search = $request->search;
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'asc');

        $materials = $this->materialService->getAllMaterials($search, $sort, $direction);

        return Inertia::render('Admin/Materials/Index', compact('materials'));
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Materials/Create/Index');
    }

    public function store(StoreMaterialRequest $request): RedirectResponse
    {
        $this->materialService->createMaterial(
            $request->except('cover_image'),
            $request->file('cover_image'),
        );

        return redirect()->route('admin.materials.index')
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    public function edit(string $materialId): Response|RedirectResponse
    {
        $material = $this->materialService->getMaterialById($materialId);

        if (!$material) {
            return redirect()->route('admin.materials.index')
                ->with('error', 'Material tidak ditemukan');
        }

        return Inertia::render('Admin/Materials/Edit/Index', compact('material'));
    }

    public function update(UpdateMaterialRequest $request, string $materialId): RedirectResponse
    {
        $this->materialService->updateMaterial(
            $materialId,
            $request->except('cover_image'),
            $request->file('cover_image'),
        );

        return redirect()->route('admin.materials.index')
            ->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(string $materialId): RedirectResponse
    {
        $this->materialService->deleteMaterial($materialId);

        return redirect()->route('admin.materials.index')
            ->with('success', 'Materi berhasil dihapus.');
    }

    public function deleteMedia(string $id): RedirectResponse
    {
        $materialId = $this->materialService->deleteMedia($id);

        return redirect()->route('admin.materials.edit', $materialId)
            ->with('success', 'Media berhasil dihapus.');
    }
}
