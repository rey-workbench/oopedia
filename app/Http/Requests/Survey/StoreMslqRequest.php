<?php

declare(strict_types=1);

namespace App\Http\Requests\Survey;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class StoreMslqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nim'                   => ['required', 'string', 'max:20'],
            'class'                 => ['required', 'string', 'max:10'],
            'answers'               => ['required', 'array', 'size:81'],
            'answers.*.question_id' => ['required', 'exists:mslq_questions,id'],
            'answers.*.value'       => ['required', 'integer', 'min:1', 'max:7'],
        ];
    }
}
