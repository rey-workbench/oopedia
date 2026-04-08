<?php

namespace App\DTOs\Survey;

use Illuminate\Http\Request;

readonly class UeqSurveyCreateDTO
{
    public function __construct(
        public string $user_id,
        public ?string $nim = null,
        public ?string $class = null,
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
    ) {}

    public static function fromRequest(Request $request, string $userId): self
    {
        return new self(
            user_id: $userId,
            nim: $request->input('nim'),
            class: $request->input('class'),
            annoying_enjoyable: $request->input('annoying_enjoyable'),
            not_understandable_understandable: $request->input('not_understandable_understandable'),
            creative_dull: $request->input('creative_dull'),
            easy_difficult: $request->input('easy_difficult'),
            valuable_inferior: $request->input('valuable_inferior'),
            boring_exciting: $request->input('boring_exciting'),
            not_interesting_interesting: $request->input('not_interesting_interesting'),
            unpredictable_predictable: $request->input('unpredictable_predictable'),
            fast_slow: $request->input('fast_slow'),
            inventive_conventional: $request->input('inventive_conventional'),
            obstructive_supportive: $request->input('obstructive_supportive'),
            good_bad: $request->input('good_bad'),
            complicated_easy: $request->input('complicated_easy'),
            unlikable_pleasing: $request->input('unlikable_pleasing'),
            usual_leading_edge: $request->input('usual_leading_edge'),
            unpleasant_pleasant: $request->input('unpleasant_pleasant'),
            secure_not_secure: $request->input('secure_not_secure'),
            motivating_demotivating: $request->input('motivating_demotivating'),
            meets_expectations_does_not_meet: $request->input('meets_expectations_does_not_meet'),
            inefficient_efficient: $request->input('inefficient_efficient'),
            clear_confusing: $request->input('clear_confusing'),
            impractical_practical: $request->input('impractical_practical'),
            organized_cluttered: $request->input('organized_cluttered'),
            attractive_unattractive: $request->input('attractive_unattractive'),
            friendly_unfriendly: $request->input('friendly_unfriendly'),
            conservative_innovative: $request->input('conservative_innovative'),
            comments: $request->input('comments'),
            suggestions: $request->input('suggestions'),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'user_id'                           => $this->user_id,
            'nim'                               => $this->nim,
            'class'                             => $this->class,
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
        ], fn ($value) => $value !== null);
    }
}
