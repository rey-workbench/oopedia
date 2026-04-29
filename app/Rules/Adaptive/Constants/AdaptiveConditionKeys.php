<?php

declare(strict_types=1);

namespace App\Rules\Adaptive\Constants;

/**
 * Constants for keys and standard values used in Adaptive Fact JSON logic.
 */
final class AdaptiveConditionKeys
{
    // --- JSON Structure Keys ---
    public const string KEY = 'key';      // Field for attribute name
    public const string OP  = 'op';       // Field for operator
    public const string VAL = 'val';      // Field for threshold/value

    // --- Operators ---
    public const string OP_LT   = '<';
    public const string OP_GT   = '>';
    public const string OP_LTE  = '<=';
    public const string OP_GTE  = '>=';
    public const string OP_EQ   = '==';
    public const string OP_NEQ  = '!=';

    // --- State Keys (Student Attributes) ---
    public const string ACCURACY   = 'accuracy';
    public const string HINTS      = 'hints_used';
    public const string STREAK     = 'streak';
    public const string LEVEL      = 'level';
    
    // Nested Performance Metrics
    public const string TREND          = 'performance_metrics.trend';
    public const string SPEED          = 'performance_metrics.speed';
    public const string STAGNANT_COUNT = 'performance_metrics.stagnant_count';
    
    // Current Session Metrics
    public const string SESSION_HINTS = 'current_session.hints';
    public const string SESSION_TIME  = 'current_session.time_spent';

    // --- Standard Values (The "Standarized" part) ---
    
    // Trend Values
    public const string TREND_UP     = 'up';
    public const string TREND_DOWN   = 'down';
    public const string TREND_STABLE = 'stable';

    // Speed Values
    public const string SPEED_FAST   = 'fast';
    public const string SPEED_SLOW   = 'slow';
    public const string SPEED_NORMAL = 'normal';

    // Level Values (Case sensitive matching DB)
    public const string LEVEL_BEGINNER     = 'Beginner';
    public const string LEVEL_INTERMEDIATE = 'Intermediate';
    public const string LEVEL_EXPERT       = 'Expert';
    public const string LEVEL_AHLI         = 'Ahli';

    /**
     * Get allowed values for a specific key (for UI dropdowns).
     */
    public static function getAllowedValues(string $key): ?array
    {
        return match ($key) {
            self::TREND => [
                ['value' => self::TREND_UP, 'label' => 'Naik (Up)'],
                ['value' => self::TREND_STABLE, 'label' => 'Stabil (Stable)'],
                ['value' => self::TREND_DOWN, 'label' => 'Turun (Down)'],
            ],
            self::SPEED => [
                ['value' => self::SPEED_FAST, 'label' => 'Cepat (Fast)'],
                ['value' => self::SPEED_NORMAL, 'label' => 'Normal'],
                ['value' => self::SPEED_SLOW, 'label' => 'Lambat (Slow)'],
            ],
            self::LEVEL => [
                ['value' => self::LEVEL_BEGINNER, 'label' => 'Beginner'],
                ['value' => self::LEVEL_INTERMEDIATE, 'label' => 'Intermediate'],
                ['value' => self::LEVEL_EXPERT, 'label' => 'Expert'],
                ['value' => self::LEVEL_AHLI, 'label' => 'Ahli'],
            ],
            default => null,
        };
    }
}
