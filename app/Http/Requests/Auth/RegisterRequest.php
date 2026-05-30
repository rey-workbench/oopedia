<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rules\Password;

final class RegisterRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255', 'regex:/^[\p{L}\'\-\s]+$/u'],
            'email'    => ['required', 'string', 'email:rfc,dns,spoof', 'indisposable', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ];
    }
}
