<?php

declare(strict_types=1);

namespace App\Http\Requests\Profile;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Support\Facades\Auth;

final class UpdateProfileRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => 'required|email|unique:users,email,' . Auth::id(),
            'nim'      => ['nullable', 'string', 'max:20'],
            'class'    => ['nullable', 'string', 'max:50'],
            'password' => ['nullable', 'min:6', 'confirmed'],
        ];
    }
}
