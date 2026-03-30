<?php

namespace App\Http\Requests\Student;

use App\Http\Requests\BaseFormRequest;

class ImportStudentRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:2048',
        ];
    }
}
