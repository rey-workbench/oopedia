<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class UpdateAdminRequest extends BaseFormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        $userId = $this->route('userId') ?? $this->route('user');

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:8|confirmed',
        ];
    }
}
