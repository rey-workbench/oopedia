<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\AdaptiveAction;

use App\Http\Requests\BaseFormRequest;

final class UpdateAdaptiveActionRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['required', 'string'],
            'variant'      => ['nullable', 'string'],
            'instructions' => ['required', 'array'],
        ];
    }
}
