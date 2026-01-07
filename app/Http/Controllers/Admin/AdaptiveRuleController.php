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
        
        // Get attributes grouped by type
        $allAttributes = \App\Models\AttributeDefinition::active()->orderBy('sort_order')->get();
        
        // Separate regular and computed attributes
        $regularAttributes = $allAttributes->where('is_computed', false)->values();
        $computedAttributes = $allAttributes->where('is_computed', true)->values();
        
        return view('admin.adaptive-rules.create', compact('materials', 'regularAttributes', 'computedAttributes'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'material_id' => 'nullable|exists:materials,id',
                'conditions' => 'required|array|min:1',
                'conditions.*.key' => 'required|string',
                'conditions.*.operator' => 'required|string',
                'conditions.*.value' => 'required',
                'actions' => 'required|array|min:1',
                'actions.*.type' => 'required|string',
                'actions.*.key' => 'required_if:actions.*.type,update_attribute',
                'actions.*.operator' => 'required_if:actions.*.type,update_attribute',
                'actions.*.value' => 'required',
                'priority' => 'required|integer|min:0'
            ]);

            // Transform conditions from form format to storage format
            $conditions = collect($request->conditions)->map(function($cond) {
                return [
                    'type' => $cond['key'], // Use 'type' for backward compatibility
                    'operator' => $cond['operator'],
                    'value' => $cond['value']
                ];
            })->toArray();

            $validated['conditions'] = $conditions;
            $validated['created_by'] = auth()->id();
            $validated['is_active'] = $request->has('is_active') ? 1 : 0;
            
            // Jika material_id kosong, set ke null
            if (empty($validated['material_id'])) {
                $validated['material_id'] = null;
            }
            
            // Fill legacy columns for backward compatibility
            $validated['condition_type'] = 'composite';
            $validated['condition_operator'] = 'composite';
            $validated['condition_value'] = 'composite';
            $validated['action_type'] = 'update_attribute';
            $validated['action_value'] = 'composite';

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
        // Get attributes grouped by type
        $allAttributes = \App\Models\AttributeDefinition::active()->orderBy('sort_order')->get();
        
        // Separate regular and computed attributes
        $regularAttributes = $allAttributes->where('is_computed', false)->values();
        $computedAttributes = $allAttributes->where('is_computed', true)->values();
        
        // Pass complete $allAttributes as well if needed, or just these two
        $attributes = $allAttributes;

        return view('admin.adaptive-rules.edit', compact('adaptiveRule', 'materials', 'attributes', 'regularAttributes', 'computedAttributes'));
    }

    public function update(Request $request, AdaptiveRule $adaptiveRule)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'material_id' => 'nullable|exists:materials,id',
                'conditions' => 'required|array|min:1',
                'conditions.*.key' => 'required|string',
                'conditions.*.operator' => 'required|string',
                'conditions.*.value' => 'required',
                'actions' => 'required|array|min:1',
                'actions.*.type' => 'required|string',
                'actions.*.key' => 'required_if:actions.*.type,update_attribute',
                'actions.*.operator' => 'required_if:actions.*.type,update_attribute',
                'actions.*.value' => 'required',
                'priority' => 'required|integer|min:0'
            ]);

            // Transform conditions from form format to storage format
            $conditions = collect($request->conditions)->map(function($cond) {
                return [
                    'type' => $cond['key'],
                    'operator' => $cond['operator'],
                    'value' => $cond['value']
                ];
            })->toArray();

            $validated['conditions'] = $conditions;
            $validated['is_active'] = $request->has('is_active') ? 1 : 0;
            
            // Jika material_id kosong, set ke null
            if (empty($validated['material_id'])) {
                $validated['material_id'] = null;
            }
            
            // Update legacy columns for backward compatibility
            $validated['condition_type'] = 'composite';
            $validated['condition_operator'] = 'composite';
            $validated['condition_value'] = 'composite';
            $validated['action_type'] = 'update_attribute';
            $validated['action_value'] = 'composite';

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
