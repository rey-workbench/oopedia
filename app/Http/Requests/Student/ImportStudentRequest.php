<?php

declare(strict_types=1);

namespace App\Http\Requests\Student;

use App\Http\Requests\BaseFormRequest;

final class ImportStudentRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:2048',
        ];
    }
}
