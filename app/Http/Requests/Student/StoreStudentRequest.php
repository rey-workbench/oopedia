<?php

namespace App\Http\Requests\Student;

use App\Http\Requests\BaseFormRequest;

class StoreStudentRequest extends BaseFormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
}
