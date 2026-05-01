<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

final class ImportAdminRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:2048'],
        ];
    }
}
