<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class ImportAdminRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:2048',
        ];
    }
}
