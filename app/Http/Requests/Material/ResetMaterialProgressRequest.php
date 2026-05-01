<?php

declare(strict_types=1);

namespace App\Http\Requests\Material;

use App\Http\Requests\BaseFormRequest;

final class ResetMaterialProgressRequest extends BaseFormRequest
{
    #[\Override]
    protected function prepareForValidation(): void
    {
        $this->merge([
            'material' => $this->route('material'),
        ]);
    }

    public function rules(): array
    {
        return [
            'material' => ['required', 'exists:materials,id'],
        ];
    }
}
