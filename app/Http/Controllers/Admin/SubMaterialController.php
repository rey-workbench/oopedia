<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\SubMaterial;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubMaterialController extends Controller
{
    public function index(Material $material)
    {
        $subMaterials = $material->subMaterials()->orderBy('order')->get();
        return Inertia::render('Admin/Materials/Submaterials/Index', compact('material', 'subMaterials'));
    }

    public function create(Material $material)
    {
        return Inertia::render('Admin/Materials/Submaterials/Create', compact('material'));
    }

    public function store(Request $request, Material $material)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'jenis_konten' => 'required|in:teori,sintaks,mixed',
            'order' => 'required|integer',
        ]);

        $material->subMaterials()->create($request->all());

        return redirect()->route('admin.materials.submaterials.index', $material)
            ->with('success', 'Sub-materi berhasil ditambahkan.');
    }

    public function edit(Material $material, SubMaterial $submaterial)
    {
        return Inertia::render('Admin/Materials/Submaterials/Edit', compact('material', 'submaterial'));
    }

    public function update(Request $request, Material $material, SubMaterial $submaterial)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'jenis_konten' => 'required|in:teori,sintaks,mixed',
            'order' => 'required|integer',
        ]);

        $submaterial->update($request->all());

        return redirect()->route('admin.materials.submaterials.index', $material)
            ->with('success', 'Sub-materi berhasil diperbarui.');
    }

    public function destroy(Material $material, SubMaterial $submaterial)
    {
        $submaterial->delete();
        return redirect()->route('admin.materials.submaterials.index', $material)
            ->with('success', 'Sub-materi berhasil dihapus.');
    }

    public function getJson(Material $material)
    {
        return response()->json($material->subMaterials()->orderBy('order')->get(['id', 'title']));
    }
}
