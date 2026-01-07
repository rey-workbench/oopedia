<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formula;
use App\Models\AttributeDefinition;
use Illuminate\Http\Request;
use App\Models\AdaptiveRule;

class FormulaController extends Controller
{
    public function index(Request $request)
    {
        $query = Formula::orderBy('sort_order')->orderBy('created_at', 'desc');
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('key', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('scope') && $request->scope != '') {
            $query->where('scope', $request->scope);
        }
        
        $formulas = $query->paginate(15);
        return view('admin.formulas.index', compact('formulas'));
    }
    
    public function create()
    {
        // Get all attributes grouped by category
        $attributes = AttributeDefinition::orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');
        
        return view('admin.formulas.create', compact('attributes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:formulas,key|regex:/^[a-z_]+$/',
            'description' => 'nullable|string',
            'expression' => 'required|string',
            'return_type' => 'required|in:' . implode(',', array_keys(Formula::RETURN_TYPES)),
            'scope' => 'required|in:' . implode(',', array_keys(Formula::SCOPES)),
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $formula = Formula::create($validated);

        // AUTO-SYNC: Update or Create AttributeDefinition associated with this formula
        $attribute = AttributeDefinition::where('key', $formula->key)->first();

        if ($attribute) {
            // If attribute exists, link it to this formula and make it computed
            $attribute->update([
                'is_computed' => true,
                'formula_id' => $formula->id,
                'category' => 'computed', // Move to computed category or keep existing? Let's move to underscore clarity
                'menu_label' => $attribute->label . ' (Formula)' // Optional detail
            ]);
        } else {
            // Create new computed attribute
            AttributeDefinition::create([
                'key' => $formula->key,
                'label' => $formula->name,
                'type' => $formula->return_type,
                'default_value' => '0',
                'category' => 'computed',
                'description' => $formula->description ?? 'Generated from formula',
                'is_computed' => true,
                'formula_id' => $formula->id,
                'is_active' => true,
                'sort_order' => 99
            ]);
        }

        return redirect()->route('admin.formulas.index')
            ->with('success', 'Formula berhasil dibuat dan didaftarkan sebagai Atribut!');
    }
    
    public function show(Formula $formula)
    {
        return view('admin.formulas.show', compact('formula'));
    }
    
    public function edit(Formula $formula)
    {
        // Get all attributes grouped by category
        $attributes = AttributeDefinition::orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');
        
        return view('admin.formulas.edit', compact('formula', 'attributes'));
    }
    
    public function update(Request $request, Formula $formula)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'key' => 'required|string|regex:/^[a-z_]+$/|unique:formulas,key,' . $formula->id,
                'description' => 'nullable|string',
                'expression' => 'required|string',
                'dependencies' => 'nullable|array',
                'return_type' => 'required|in:integer,float,string,boolean',
                'scope' => 'required|in:material,global,session'
            ]);
            
            $validated['is_active'] = $request->has('is_active') ? 1 : 0;
            
            $formula->update($validated);
            
            return redirect()->route('admin.formulas.index')
                ->with('success', 'Formula berhasil diperbarui!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui formula: ' . $e->getMessage());
        }
    }
    
    public function destroy(Formula $formula)
    {
        // 1. Get associated attribute definitions
        $relatedAttributes = $formula->attributeDefinitions;

        // 2. Check if any of these attributes are used in Adaptive Rules
        foreach ($relatedAttributes as $attr) {
            // Search in conditions (JSON)
            $usedInConditions = AdaptiveRule::where('conditions', 'like', '%"key":"' . $attr->key . '"%')
                ->orWhere('conditions', 'like', '%"type":"' . $attr->key . '"%') // legacy
                ->exists();

            // Search in actions (JSON)
            $usedInActions = AdaptiveRule::where('actions', 'like', '%"key":"' . $attr->key . '"%')
                ->exists();

            if ($usedInConditions || $usedInActions) {
                return redirect()->back()
                    ->with('error', "Formula tidak dapat dihapus karena atribut '{$attr->label}' sedang digunakan dalam Adaptive Rules!");
            }
        }
        
        // 3. If safe, delete the associated attributes first
        foreach ($relatedAttributes as $attr) {
            $attr->delete();
        }

        // 4. Delete the formula
        $formula->delete();
        
        return redirect()->route('admin.formulas.index')
            ->with('success', 'Formula dan atribut terkait berhasil dihapus!');
    }
    
    public function toggleStatus(Formula $formula)
    {
        $formula->update(['is_active' => !$formula->is_active]);
        
        return redirect()->back()
            ->with('success', 'Status formula berhasil diubah!');
    }
}
