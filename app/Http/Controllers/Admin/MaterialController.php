<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Contracts\Services\MaterialServiceInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MaterialController extends Controller
{
    public function __construct(
        protected MaterialServiceInterface $materialService
    ) {}

    public function index(Request $request)
    {
        $search = $request->search;
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'asc');

        $materials = $this->materialService->getAllMaterials($search, $sort, $direction);

        return Inertia::render('Admin/Materials/Index', compact('materials'));
    }

    public function create()
    {
        return Inertia::render('Admin/Materials/Create/Index');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'created_by' => 'required|exists:users,id',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $this->materialService->createMaterial(
                $request->except('cover_image'), 
                $request->file('cover_image')
            );

            return redirect()->route('admin.materials.index')
                ->with('success', 'Materi berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(int $materialId)
    {
        $material = $this->materialService->getMaterialById($materialId);
        
        if (!$material) {
            return redirect()->route('admin.materials.index')
                ->with('error', 'Material tidak ditemukan');
        }
        
        return Inertia::render('Admin/Materials/Edit/Index', compact('material'));
    }

    public function update(Request $request, int $materialId)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $this->materialService->updateMaterial(
                $materialId,
                $request->except('cover_image'),
                $request->file('cover_image')
            );

            return redirect()->route('admin.materials.index')
                ->with('success', 'Materi berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(int $materialId)
    {
        try {
            $this->materialService->deleteMaterial($materialId);

            return redirect()->route('admin.materials.index')
                ->with('success', 'Materi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.materials.index')
                ->with('error', 'Gagal menghapus materi: ' . $e->getMessage());
        }
    }
    
    public function deleteMedia($id)
    {
        try {
            $materialId = $this->materialService->deleteMedia($id);
            
            return redirect()->route('admin.materials.edit', $materialId)
                ->with('success', 'Media berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus media: ' . $e->getMessage());
        }
    }
}