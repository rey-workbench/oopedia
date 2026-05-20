<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Enums\Lms\QuestionType;
use App\Enums\User\RoleName;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Question
 */
final class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        $user    = $request->user();
        $isAdmin = $user?->hasRole(RoleName::SUPERADMIN) || $user?->hasRole(RoleName::DOSEN);

        // Only show correct answers if it's an admin or if the question is being reviewed (has user_attempt)
        $showAnswers = $isAdmin || $this->resource->user_attempt !== null;

        return [
            'id'            => $this->resource->id,
            'material_id'   => $this->material_id,
            'question_text' => $this->processHtml($this->question_text),
            'question_type' => $this->question_type->value,
            'difficulty'    => $this->difficulty->value,
            'hint'          => $this->hint,
            'answers'       => $this->whenLoaded('answers', fn () => $this->answers->map(function ($answer) use ($showAnswers): array {
                $data = [
                    'id'             => $answer->id,
                    'drag_source'    => $answer->drag_source,
                    'drag_target'    => $answer->drag_target,
                    'blank_position' => $answer->blank_position,
                ];

                if ($showAnswers || $this->question_type !== QuestionType::FILL_IN_THE_BLANK) {
                    $data['answer_text'] = $answer->answer_text;
                }

                if ($showAnswers) {
                    $data['is_correct']  = (bool) $answer->is_correct;
                    $data['explanation'] = $answer->explanation;
                }

                return $data;
            })),
            'answers_count' => $this->whenCounted('answers', $this->answers_count),
            'blank_length'  => $this->question_type === QuestionType::FILL_IN_THE_BLANK
                ? ($this->relationLoaded('answers')
                    ? mb_strlen(trim($this->answers->firstWhere('is_correct', true)?->answer_text ?? ''))
                    : 0)
                : null,
            'user_attempt'  => $this->when($this->resource->user_attempt !== null, $this->resource->user_attempt),
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }

    private function processHtml(?string $html): string
    {
        if (! $html) {
            return '';
        }

        // Make storage URLs absolute
        return preg_replace_callback('/src="([^"]+)"/', function ($matches): string {
            $url = $matches[1];
            if (str_starts_with($url, 'storage/') || str_starts_with($url, 'public/storage/')) {
                return 'src="' . url(str_replace('public/storage/', 'storage/', $url)) . '"';
            }

            return $matches[0];
        }, $html);
    }
}
