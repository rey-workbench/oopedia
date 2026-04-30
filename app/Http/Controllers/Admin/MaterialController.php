<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\MaterialServiceInterface;
use App\DTOs\Material\MaterialCreateDTO;
use App\DTOs\Material\MaterialUpdateDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Material\StoreMaterialRequest;
use App\Http\Requests\Material\UpdateMaterialRequest;
use App\Models\Material;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class MaterialController extends Controller
{
    public function __construct(
        private readonly MaterialServiceInterface $materialService,
    ) {}

    public function index(Request $request): Response
    {
        $search    = $request->search;
        $sort      = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'asc');

        $materials = $this->materialService->getAllMaterials($search, $sort, $direction);

        return $this->render('Admin/Materials/Index', ['materials' => $materials]);
    }

    public function create(): Response
    {
        return $this->render('Admin/Materials/Create/Index');
    }

    public function store(StoreMaterialRequest $storeMaterialRequest): RedirectResponse
    {
        $materialCreateDTO = MaterialCreateDTO::fromRequest($storeMaterialRequest, Auth::id());

        $this->materialService->createMaterial($materialCreateDTO);

        return redirect()->route('admin.materials.index')
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    public function edit(string $materialId): Response|RedirectResponse
    {
        $material = $this->materialService->getMaterialById($materialId);

        if (! $material instanceof Material) {
            return redirect()->route('admin.materials.index')
                ->with('error', 'Material tidak ditemukan');
        }

        return $this->render('Admin/Materials/Edit/Index', ['material' => $material]);
    }

    public function update(UpdateMaterialRequest $updateMaterialRequest, string $materialId): RedirectResponse
    {
        $materialUpdateDTO = MaterialUpdateDTO::fromRequest($updateMaterialRequest);

        $this->materialService->updateMaterial($materialId, $materialUpdateDTO);

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
