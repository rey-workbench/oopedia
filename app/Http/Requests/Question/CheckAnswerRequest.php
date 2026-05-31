<?php

declare(strict_types=1);

namespace App\Http\Requests\Question;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Validator;

final class CheckAnswerRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'used_hint'                => ['required', 'boolean'],
            'time_spent'               => ['required', 'integer', 'min:0'],
            'score'                    => ['nullable', 'integer', 'min:0'],
            'difficulty'               => ['nullable', 'in:all,beginner,medium,hard,final'],
            'answer'                   => ['nullable', 'string', 'max:5000'],
            'fill_in_the_blank_answer' => ['nullable', 'string', 'max:1000'],
            'drag_and_drop_answers'    => ['nullable', 'array', 'max:50'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $hasAnswer = $this->filled('answer')
                    || $this->filled('fill_in_the_blank_answer')
                    || $this->filled('drag_and_drop_answers');

                if (! $hasAnswer) {
                    $validator->errors()->add('answer', 'Jawaban wajib diisi.');
                }
            },
        ];
    }
}
