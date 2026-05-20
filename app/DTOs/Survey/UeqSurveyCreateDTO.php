<?php

declare(strict_types=1);

namespace App\DTOs\Survey;

use Illuminate\Http\Request;

final readonly class UeqSurveyCreateDTO
{
    public function __construct(
        public string $user_id,
        public string $assessment_type,
        public ?int $annoying_enjoyable = null,
        public ?int $not_understandable_understandable = null,
        public ?int $creative_dull = null,
        public ?int $easy_difficult = null,
        public ?int $valuable_inferior = null,
        public ?int $boring_exciting = null,
        public ?int $not_interesting_interesting = null,
        public ?int $unpredictable_predictable = null,
        public ?int $fast_slow = null,
        public ?int $inventive_conventional = null,
        public ?int $obstructive_supportive = null,
        public ?int $good_bad = null,
        public ?int $complicated_easy = null,
        public ?int $unlikable_pleasing = null,
        public ?int $usual_leading_edge = null,
        public ?int $unpleasant_pleasant = null,
        public ?int $secure_not_secure = null,
        public ?int $motivating_demotivating = null,
        public ?int $meets_expectations_does_not_meet = null,
        public ?int $inefficient_efficient = null,
        public ?int $clear_confusing = null,
        public ?int $impractical_practical = null,
        public ?int $organized_cluttered = null,
        public ?int $attractive_unattractive = null,
        public ?int $friendly_unfriendly = null,
        public ?int $conservative_innovative = null,
        public ?string $comments = null,
        public ?string $suggestions = null,
    ) {
    }

    public static function fromRequest(Request $request, string $userId): self
    {
        $answers       = $request->input('answers', []);
        $mappedAnswers = [];
        foreach ($answers as $answer) {
            $mappedAnswers[$answer['question_id']] = $answer['value'];
        }

        return new self(
            user_id: $userId,
            assessment_type: (string) $request->input('assessment_type', 'post'),
            annoying_enjoyable: $mappedAnswers['annoying_enjoyable']                               ?? null,
            not_understandable_understandable: $mappedAnswers['not_understandable_understandable'] ?? null,
            creative_dull: $mappedAnswers['creative_dull']                                         ?? null,
            easy_difficult: $mappedAnswers['easy_difficult']                                       ?? null,
            valuable_inferior: $mappedAnswers['valuable_inferior']                                 ?? null,
            boring_exciting: $mappedAnswers['boring_exciting']                                     ?? null,
            not_interesting_interesting: $mappedAnswers['not_interesting_interesting']             ?? null,
            unpredictable_predictable: $mappedAnswers['unpredictable_predictable']                 ?? null,
            fast_slow: $mappedAnswers['fast_slow']                                                 ?? null,
            inventive_conventional: $mappedAnswers['inventive_conventional']                       ?? null,
            obstructive_supportive: $mappedAnswers['obstructive_supportive']                       ?? null,
            good_bad: $mappedAnswers['good_bad']                                                   ?? null,
            complicated_easy: $mappedAnswers['complicated_easy']                                   ?? null,
            unlikable_pleasing: $mappedAnswers['unlikable_pleasing']                               ?? null,
            usual_leading_edge: $mappedAnswers['usual_leading_edge']                               ?? null,
            unpleasant_pleasant: $mappedAnswers['unpleasant_pleasant']                             ?? null,
            secure_not_secure: $mappedAnswers['secure_not_secure']                                 ?? null,
            motivating_demotivating: $mappedAnswers['motivating_demotivating']                     ?? null,
            meets_expectations_does_not_meet: $mappedAnswers['meets_expectations_does_not_meet']   ?? null,
            inefficient_efficient: $mappedAnswers['inefficient_efficient']                         ?? null,
            clear_confusing: $mappedAnswers['clear_confusing']                                     ?? null,
            impractical_practical: $mappedAnswers['impractical_practical']                         ?? null,
            organized_cluttered: $mappedAnswers['organized_cluttered']                             ?? null,
            attractive_unattractive: $mappedAnswers['attractive_unattractive']                     ?? null,
            friendly_unfriendly: $mappedAnswers['friendly_unfriendly']                             ?? null,
            conservative_innovative: $mappedAnswers['conservative_innovative']                     ?? null,
            comments: $request->input('comments'),
            suggestions: $request->input('suggestions'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'user_id'                           => $this->user_id,
            'assessment_type'                   => $this->assessment_type,
            'annoying_enjoyable'                => $this->annoying_enjoyable,
            'not_understandable_understandable' => $this->not_understandable_understandable,
            'creative_dull'                     => $this->creative_dull,
            'easy_difficult'                    => $this->easy_difficult,
            'valuable_inferior'                 => $this->valuable_inferior,
            'boring_exciting'                   => $this->boring_exciting,
            'not_interesting_interesting'       => $this->not_interesting_interesting,
            'unpredictable_predictable'         => $this->unpredictable_predictable,
            'fast_slow'                         => $this->fast_slow,
            'inventive_conventional'            => $this->inventive_conventional,
            'obstructive_supportive'            => $this->obstructive_supportive,
            'good_bad'                          => $this->good_bad,
            'complicated_easy'                  => $this->complicated_easy,
            'unlikable_pleasing'                => $this->unlikable_pleasing,
            'usual_leading_edge'                => $this->usual_leading_edge,
            'unpleasant_pleasant'               => $this->unpleasant_pleasant,
            'secure_not_secure'                 => $this->secure_not_secure,
            'motivating_demotivating'           => $this->motivating_demotivating,
            'meets_expectations_does_not_meet'  => $this->meets_expectations_does_not_meet,
            'inefficient_efficient'             => $this->inefficient_efficient,
            'clear_confusing'                   => $this->clear_confusing,
            'impractical_practical'             => $this->impractical_practical,
            'organized_cluttered'               => $this->organized_cluttered,
            'attractive_unattractive'           => $this->attractive_unattractive,
            'friendly_unfriendly'               => $this->friendly_unfriendly,
            'conservative_innovative'           => $this->conservative_innovative,
            'comments'                          => $this->comments,
            'suggestions'                       => $this->suggestions,
        ], fn (string|int|null $value): bool => $value !== null);
    }
}
