<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

final class LoginRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email'    => ['required_without:is_guest', 'nullable', 'string', 'email:rfc,dns,spoof', 'indisposable'],
            'password' => ['required_without:is_guest', 'nullable', 'string'],
            'is_guest' => ['sometimes', 'boolean'],
        ];
    }
}
