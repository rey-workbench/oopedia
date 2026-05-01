<?php

declare(strict_types=1);

namespace App\Http\Requests\SubMaterial;

use App\Http\Requests\BaseFormRequest;

final class StoreSubMaterialRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'content'      => ['required', 'string'],
            'jenis_konten' => ['nullable', 'in:teori,sintaks,mixed'],
            'order'        => ['nullable', 'integer', 'min:0'],
        ];
    }
}
