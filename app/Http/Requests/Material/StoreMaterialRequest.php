<?php

namespace App\Http\Requests\Material;

use App\Http\Requests\BaseFormRequest;

class StoreMaterialRequest extends BaseFormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'created_by' => 'required|exists:users,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
