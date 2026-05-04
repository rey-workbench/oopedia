<?php

declare(strict_types=1);

namespace App\Http\Requests\Survey;

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
        $count = \App\Models\MslqQuestion::count();
        
        return [
            'assessment_type'       => ['required', 'string', 'in:pre,post'],
            'answers'               => ['required', 'array', "size:{$count}"],
            'answers.*.question_id' => ['required', 'exists:mslq_questions,id'],
            'answers.*.value'       => ['required', 'integer', 'min:1', 'max:7'],
        ];
    }
}
