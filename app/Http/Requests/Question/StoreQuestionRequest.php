<?php

namespace App\Http\Requests\Question;

use App\Http\Requests\BaseFormRequest;

class StoreQuestionRequest extends BaseFormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        $answersMinRule = $this->input('question_type') === 'fill_in_the_blank'
            ? 'required|array|min:1'
            : 'required|array|min:2';

        $rules = [
            'question_text'         => 'required|string',
            'question_type'         => 'required|in:radio_button,drag_and_drop,fill_in_the_blank',
            'difficulty'            => 'required|in:beginner,medium,hard',
            'material_id'           => 'required|exists:materials,id',
            'sub_material_id'       => 'nullable|exists:sub_materials,id',
            'answers'               => $answersMinRule,
            'answers.*.is_correct'  => 'required|boolean',
            'answers.*.explanation' => 'nullable|string',
            'answers.*.answer_text' => 'required|string',
            'answers.*.drag_source' => 'nullable|string',
            'answers.*.drag_target' => 'nullable|string',
        ];

        return $rules;
    }
}
