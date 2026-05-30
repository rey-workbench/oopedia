<?php

declare(strict_types=1);

namespace App\Http\Requests\Survey;

use App\Http\Requests\BaseFormRequest;

final class StoreSusResultRequest extends BaseFormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'comments'    => strip_tags((string) $this->input('comments')),
            'suggestions' => strip_tags((string) $this->input('suggestions')),
        ]);
    }

    public function rules(): array
    {
        return [
            'assessment_type' => ['required', 'string', 'in:pre,post'],
            'nim'             => ['nullable', 'string', 'max:20'],
            'class'           => ['nullable', 'string', 'max:50'],

            'answers'               => ['required', 'array', 'size:10'],
            'answers.*.question_id' => ['required', 'string', 'exists:sus_questions,id'],
            'answers.*.value'       => ['required', 'integer', 'between:1,5'],

            'comments'    => ['required', 'max:1000'],
            'suggestions' => ['required', 'max:1000'],
        ];
    }

    #[\Override]
    public function messages(): array
    {
        $between = 'Skala penilaian harus bernilai antara 1 sampai 5';

        return array_merge(parent::messages(), [
            'answers.*.value.required' => 'Seluruh pertanyaan instrumen penilaian SUS wajib diisi',
            'answers.*.value.between'  => $between,

            'comments.required'        => 'Komentar wajib diisi',
            'suggestions.required'     => 'Saran wajib diisi',
            'assessment_type.required' => 'Tipe asessmen wajib diisi',
        ]);
    }
}
