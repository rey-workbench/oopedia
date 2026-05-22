<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\UeqSurvey;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin UeqSurvey
 */
final class UeqSurveyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'user'       => $this->whenLoaded('user', fn () => (new UserResource($this->user))->resolve()),
            'nim'        => $this->nim,
            'class'      => $this->class,
            'created_at' => $this->created_at?->toIso8601String(),

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
        ];
    }
}
