<?php

declare(strict_types=1);

namespace App\Http\Requests\Survey;

use App\Http\Requests\BaseFormRequest;

final class StoreUeqSurveyRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'assessment_type' => ['required', 'string', 'in:pre,post'],
            'nim'             => ['nullable', 'string', 'max:20'],
            'class'           => ['nullable', 'string', 'max:50'],

            'answers'               => ['required', 'array', 'size:26'],
            'answers.*.question_id' => ['required', 'string'],
            'answers.*.value'       => ['required', 'integer', 'between:1,7'],

            'comments'    => ['required', 'max:1000'],
            'suggestions' => ['required', 'max:1000'],
        ];
    }

    #[\Override]
    public function messages(): array
    {
        $between = 'Skala penilaian harus bernilai antara 1 sampai 7';

        return array_merge(parent::messages(), [
            'answers.*.value.required' => 'Seluruh pertanyaan instrumen penilaian UEQ wajib diisi',
            'answers.*.value.between'  => $between,

            'comments.required'         => 'Komentar wajib diisi',
            'suggestions.required'      => 'Saran wajib diisi',
            'comments.max'              => 'Komentar tidak boleh lebih dari 1000 karakter',
            'suggestions.max'           => 'Saran tidak boleh lebih dari 1000 karakter',
            'assessment_type.required'  => 'Tipe asesmen wajib diisi',
            'assessment_type.in'        => 'Tipe asesmen tidak valid',
        ]);
    }
}
