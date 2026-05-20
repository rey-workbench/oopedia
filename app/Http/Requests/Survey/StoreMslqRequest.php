<?php

declare(strict_types=1);

namespace App\Http\Requests\Survey;

use App\Models\MslqQuestion;
use App\Http\Requests\BaseFormRequest;

final class StoreMslqRequest extends BaseFormRequest
{
    #[\Override]
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $count = MslqQuestion::count();

        return [
            'assessment_type' => ['required', 'string', 'in:pre,post'],
            'nim' => ['nullable', 'string', 'max:20'],
            'class' => ['nullable', 'string', 'max:50'],
            'answers' => ['required', 'array', 'size:' . $count],
            'answers.*.question_id' => ['required', 'exists:mslq_questions,id'],
            'answers.*.value' => ['required', 'integer', 'min:1', 'max:7'],
        ];
    }

    #[\Override]
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'answers.*.value.required' => 'Seluruh pertanyaan instrumen penilaian MSLQ wajib diisi',
            'answers.*.value.integer' => 'Nilai jawaban harus berupa angka',
            'answers.*.value.min' => 'Nilai minimal adalah 1',
            'answers.*.value.max' => 'Nilai maksimal adalah 7',
        ]);
    }
}
