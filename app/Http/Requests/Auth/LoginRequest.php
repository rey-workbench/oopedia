<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

class LoginRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email'    => 'required_without:is_guest|nullable|string|email',
            'password' => 'required_without:is_guest|nullable|string',
            'is_guest' => 'sometimes|boolean',
        ];
    }
}
