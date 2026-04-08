<?php

namespace App\Http\Requests\Material;

use App\Http\Requests\BaseFormRequest;

class StoreMaterialRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'title'            => 'required|string|max:255',
            'content'          => 'required|string',
            'module_id'        => 'required|exists:modules,id',
            'is_final_project' => 'nullable|boolean',
            'created_by'       => 'required|exists:users,id',
            'cover_image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
