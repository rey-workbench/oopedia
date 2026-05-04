<?php

declare(strict_types=1);

namespace App\Http\Requests\Survey;

use App\Http\Requests\BaseFormRequest;

final class StoreSusResultRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'assessment_type' => ['required', 'string', 'in:pre,post'],

            'q1'  => ['required', 'integer', 'between:1,5'],
            'q2'  => ['required', 'integer', 'between:1,5'],
            'q3'  => ['required', 'integer', 'between:1,5'],
            'q4'  => ['required', 'integer', 'between:1,5'],
            'q5'  => ['required', 'integer', 'between:1,5'],
            'q6'  => ['required', 'integer', 'between:1,5'],
            'q7'  => ['required', 'integer', 'between:1,5'],
            'q8'  => ['required', 'integer', 'between:1,5'],
            'q9'  => ['required', 'integer', 'between:1,5'],
            'q10' => ['required', 'integer', 'between:1,5'],

            'comments'    => ['required', 'max:1000'],
            'suggestions' => ['required', 'max:1000'],
        ];
    }

    #[\Override]
    public function messages(): array
    {
        $between = 'Skala penilaian harus bernilai antara 1 sampai 5';

        return array_merge(parent::messages(), [
            'q1.required'  => 'Pertanyaan 1 wajib diisi',
            'q2.required'  => 'Pertanyaan 2 wajib diisi',
            'q3.required'  => 'Pertanyaan 3 wajib diisi',
            'q4.required'  => 'Pertanyaan 4 wajib diisi',
            'q5.required'  => 'Pertanyaan 5 wajib diisi',
            'q6.required'  => 'Pertanyaan 6 wajib diisi',
            'q7.required'  => 'Pertanyaan 7 wajib diisi',
            'q8.required'  => 'Pertanyaan 8 wajib diisi',
            'q9.required'  => 'Pertanyaan 9 wajib diisi',
            'q10.required' => 'Pertanyaan 10 wajib diisi',

            'q1.between'  => $between,
            'q2.between'  => $between,
            'q3.between'  => $between,
            'q4.between'  => $between,
            'q5.between'  => $between,
            'q6.between'  => $between,
            'q7.between'  => $between,
            'q8.between'  => $between,
            'q9.between'  => $between,
            'q10.between' => $between,

            'comments.required'    => 'Komentar wajib diisi',
            'suggestions.required' => 'Saran wajib diisi',
            'assessment_type.required' => 'Tipe asessmen wajib diisi',
        ]);
    }
}
