<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttributeDefinition;
use Illuminate\Http\Request;

class AttributeDefinitionController extends Controller
{
    public function index()
    {
        // Get all attributes sorted by category and sort order
        $attributes = AttributeDefinition::orderBy('sort_order')
            ->get()
            ->groupBy(function($item) {
                // Determine category based on key prefix or explicit category if we had one
                // Since we don't have explicit category column filled consistently yet, 
                // let's group by type (Regular vs Computed) and then maybe sub-group or just list
                return $item->is_computed ? 'Computed (Formula)' : 'Regular (Raw Data)';
            });

        return view('admin.attribute-definitions.index', compact('attributes'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'key' => 'required|string|unique:attribute_definitions,key|regex:/^[a-z_]+$/',
                'label' => 'required|string|max:255',
                'type' => 'required|in:integer,float,string,boolean',
                'default_value' => 'required|string',
                'category' => 'nullable|string',
                'description' => 'nullable|string'
            ]);

            $validated['is_active'] = true;
            $validated['sort_order'] = AttributeDefinition::max('sort_order') + 1;

            $attribute = AttributeDefinition::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Atribut berhasil ditambahkan',
                'data' => $attribute
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan atribut: ' . $e->getMessage()
            ], 500);
        }
    }
}
