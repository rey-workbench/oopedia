<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Services\Lms\Material\MaterialService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MaterialController extends Controller
{
    protected $materialService;

    public function __construct(MaterialService $materialService)
    {
        $this->materialService = $materialService;
    }

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
        return Inertia::render('Admin/Materials/Create');
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

    public function edit(Material $material)
    {
        $material->load('media'); // Ensure media is loaded
        return Inertia::render('Admin/Materials/Edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $this->materialService->updateMaterial(
                $material,
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

    public function destroy(Material $material)
    {
        try {
            $this->materialService->deleteMaterial($material);

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