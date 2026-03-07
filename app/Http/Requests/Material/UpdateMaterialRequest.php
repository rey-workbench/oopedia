<?php

namespace App\Http\Requests\Material;

use App\Http\Requests\BaseFormRequest;

class UpdateMaterialRequest extends BaseFormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'module_id' => 'nullable|exists:modules,id',
            'is_final_project' => 'nullable|boolean',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
