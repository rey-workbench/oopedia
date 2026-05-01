<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

final class ForgotPasswordRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc,dns,spoof', 'indisposable'],
        ];
    }
}
