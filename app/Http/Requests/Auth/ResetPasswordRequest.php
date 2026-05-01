<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rules\Password;

final class ResetPasswordRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'token'    => ['required'],
            'email'    => ['required', 'email:rfc,dns,spoof'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
