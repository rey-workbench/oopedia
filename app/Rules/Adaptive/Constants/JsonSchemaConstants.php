<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

/**
 * Standard JSON structures for database columns.
 */
final class JsonSchemaConstants
{
    /**
     * Standard structure for 'badges' column.
     * [ ['code' => 'B01', 'name' => 'First Win', 'earned_at' => '2024-01-01'] ]
     */
    public const array BADGE_TEMPLATE = [
        'code'      => '',
        'name'      => '',
        'earned_at' => '',
    ];

    /**
     * Standard structure for 'certifications' column.
     * [ ['type' => 'gold', 'material_id' => 'M1', 'issued_at' => '2024-01-01'] ]
     */
    public const array CERTIFICATION_TEMPLATE = [
        'type'        => '',
        'material_id' => '',
        'issued_at'   => '',
    ];

    /**
     * Standard structure for 'time_distribution' column.
     * [ 'beginner' => ['avg' => 10.5, 'total' => 5] ]
     */
    public const array TIME_DIST_TEMPLATE = [
        'avg'   => 0.0,
        'total' => 0,
    ];

    /**
     * Returns a full empty state for time distribution.
     */
    public static function emptyTimeDist(): array
    {
        return [
            'beginner' => self::TIME_DIST_TEMPLATE,
            'medium'   => self::TIME_DIST_TEMPLATE,
            'hard'     => self::TIME_DIST_TEMPLATE,
        ];
    }
}
