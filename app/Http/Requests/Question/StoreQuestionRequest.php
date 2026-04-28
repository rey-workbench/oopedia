<?php

declare(strict_types=1);

namespace App\Http\Requests\Question;

use App\Enums\Lms\QuestionDifficulty;
use App\Enums\Lms\QuestionType;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

final class StoreQuestionRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'question_text'         => 'required|string',
            'question_type'         => ['required', Rule::in(QuestionType::cases())],
            'difficulty'            => ['required', Rule::in(QuestionDifficulty::cases())],
            'material_id'           => 'required|exists:materials,id',
            'answers'               => $this->input('question_type') === QuestionType::FILL_IN_THE_BLANK->value
                ? 'required|array|min:1'
                : 'required|array|min:2',
            'answers.*.is_correct'  => 'required|boolean',
            'answers.*.explanation' => 'nullable|string',
            'answers.*.answer_text' => 'required|string',
            'answers.*.drag_source' => 'nullable|string',
            'answers.*.drag_target' => 'nullable|string',
        ];
    }
}
