<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $user_id
 * @property string|null $nim
 * @property string|null $class
 * @property int $annoying_enjoyable
 * @property int $not_understandable_understandable
 * @property int $creative_dull
 * @property int $easy_difficult
 * @property int $valuable_inferior
 * @property int $boring_exciting
 * @property int $not_interesting_interesting
 * @property int $unpredictable_predictable
 * @property int $fast_slow
 * @property int $inventive_conventional
 * @property int $obstructive_supportive
 * @property int $good_bad
 * @property int $complicated_easy
 * @property int $unlikable_pleasing
 * @property int $usual_leading_edge
 * @property int $unpleasant_pleasant
 * @property int $secure_not_secure
 * @property int $motivating_demotivating
 * @property int $meets_expectations_does_not_meet
 * @property int $inefficient_efficient
 * @property int $clear_confusing
 * @property int $impractical_practical
 * @property int $organized_cluttered
 * @property int $attractive_unattractive
 * @property int $friendly_unfriendly
 * @property int $conservative_innovative
 * @property string|null $comments
 * @property string|null $suggestions
 */
final class UeqSurvey extends Model
{
    use HasFactory;
    use HasUlids;

    #[\Override]
    protected $fillable = [
        'user_id',
        'nim',
        'class',
        'annoying_enjoyable',
        'not_understandable_understandable',
        'creative_dull',
        'easy_difficult',
        'valuable_inferior',
        'boring_exciting',
        'not_interesting_interesting',
        'unpredictable_predictable',
        'fast_slow',
        'inventive_conventional',
        'obstructive_supportive',
        'good_bad',
        'complicated_easy',
        'unlikable_pleasing',
        'usual_leading_edge',
        'unpleasant_pleasant',
        'secure_not_secure',
        'motivating_demotivating',
        'meets_expectations_does_not_meet',
        'inefficient_efficient',
        'clear_confusing',
        'impractical_practical',
        'organized_cluttered',
        'attractive_unattractive',
        'friendly_unfriendly',
        'conservative_innovative',
        'comments',
        'suggestions',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
