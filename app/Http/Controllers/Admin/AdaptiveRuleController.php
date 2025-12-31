<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdaptiveRule;
use App\Models\Material;
use Illuminate\Http\Request;

class AdaptiveRuleController extends Controller
{
    public function index(Request $request)
    {
        $query = AdaptiveRule::with(['material', 'creator'])
            ->orderBy('priority', 'asc')
            ->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('material_id') && $request->material_id != '') {
            $query->where('material_id', $request->material_id);
        }

        $rules = $query->paginate(10);
        $materials = Material::orderBy('title')->get();

        return view('admin.adaptive-rules.index', compact('rules', 'materials'));
    }

    public function create()
    {
        $materials = Material::orderBy('title')->get();
        return view('admin.adaptive-rules.create', compact('materials'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'material_id' => 'nullable|exists:materials,id',
                'condition_type' => 'required|string',
                'condition_operator' => 'required|string',
                'condition_value' => 'required|string',
                'action_type' => 'required|string',
                'action_value' => 'required|string',
                'priority' => 'required|integer|min:0'
            ]);

            $validated['created_by'] = auth()->id();
            $validated['is_active'] = $request->has('is_active') ? 1 : 0;
            
            // Jika material_id kosong, set ke null
            if (empty($validated['material_id'])) {
                $validated['material_id'] = null;
            }

            AdaptiveRule::create($validated);

            return redirect()->route('admin.adaptive-rules.index')
                ->with('success', 'Rule berhasil ditambahkan!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan rule: ' . $e->getMessage());
        }
    }

    public function show(AdaptiveRule $adaptiveRule)
    {
        $adaptiveRule->load(['material', 'creator']);
        return view('admin.adaptive-rules.show', compact('adaptiveRule'));
    }

    public function edit(AdaptiveRule $adaptiveRule)
    {
        $materials = Material::orderBy('title')->get();
        return view('admin.adaptive-rules.edit', compact('adaptiveRule', 'materials'));
    }

    public function update(Request $request, AdaptiveRule $adaptiveRule)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'material_id' => 'nullable|exists:materials,id',
                'condition_type' => 'required|string',
                'condition_operator' => 'required|string',
                'condition_value' => 'required|string',
                'action_type' => 'required|string',
                'action_value' => 'required|string',
                'priority' => 'required|integer|min:0'
            ]);

            $validated['is_active'] = $request->has('is_active') ? 1 : 0;
            
            // Jika material_id kosong, set ke null
            if (empty($validated['material_id'])) {
                $validated['material_id'] = null;
            }

            $adaptiveRule->update($validated);

            return redirect()->route('admin.adaptive-rules.index')
                ->with('success', 'Rule berhasil diperbarui!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui rule: ' . $e->getMessage());
        }
    }

    public function destroy(AdaptiveRule $adaptiveRule)
    {
        $adaptiveRule->delete();

        return redirect()->route('admin.adaptive-rules.index')
            ->with('success', 'Rule berhasil dihapus!');
    }

    public function toggleStatus(AdaptiveRule $adaptiveRule)
    {
        $adaptiveRule->update(['is_active' => !$adaptiveRule->is_active]);

        return redirect()->back()
            ->with('success', 'Status rule berhasil diubah!');
    }
}
