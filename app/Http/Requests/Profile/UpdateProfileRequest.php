<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends BaseFormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        $userId = Auth::id() ?? 0;

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => 'nullable|min:6|confirmed',
        ];
    }
}
