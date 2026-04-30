<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\AdaptiveAction;

use App\Http\Requests\BaseFormRequest;

final class StoreAdaptiveActionRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'id'           => 'required|string|unique:adaptive_actions,id',
            'name'         => 'required|string|max:255',
            'description'  => 'required|string',
            'variant'      => 'nullable|string',
            'instructions' => 'required|array',
        ];
    }
}
