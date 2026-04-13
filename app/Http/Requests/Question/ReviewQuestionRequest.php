<?php

declare(strict_types=1);

namespace App\Http\Requests\Question;

use App\Http\Requests\BaseFormRequest;

final class ReviewQuestionRequest extends BaseFormRequest
{
    protected function prepareForValidation(): void
    {
        $difficulty = $this->route('difficulty');

        if ($difficulty === null) {
            $difficulty = $this->query('difficulty');
        }

        $this->merge([
            'difficulty' => $difficulty,
        ]);
    }

    public function rules(): array
    {
        return [
            'difficulty' => 'nullable|in:all,beginner,medium,hard,final',
        ];
    }
}
