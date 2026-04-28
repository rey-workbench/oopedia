<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Enums\Lms\StudentLevel;
use App\Rules\Adaptive\Constants\ActionConstants;
use App\Rules\Adaptive\Constants\FactConstants;
use App\Rules\Adaptive\Constants\PedagogicalConstants;
use App\Rules\Adaptive\Constants\StudentStateSchema;

final class AdaptiveEngineService implements AdaptiveEngineServiceInterface
{
    public function evaluate(array $state): array
    {
        // 1. Extract Thresholds (Layer 2)
        $accuracy = (float) ($state[StudentStateSchema::ACCURACY] ?? 0);
        $metrics  = $state[StudentStateSchema::PERFORMANCE_METRICS] ?? [];
        $session  = $state[StudentStateSchema::CURRENT_SESSION]     ?? [];

        $trend         = $metrics['trend'] ?? 'stable';
        $speed         = $metrics['speed'] ?? 'normal';
        $hints         = (int) ($session['hints'] ?? 0);
        $level         = $state[StudentStateSchema::LEVEL] ?? StudentLevel::PEMULA->value;
        $streak        = (int) ($state[StudentStateSchema::STREAK] ?? 0);
        $stagnantCount = (int) ($metrics['stagnant_count'] ?? 0);
        $history       = $state[StudentStateSchema::SESSION_HISTORY] ?? [];

        // 2. Collect Facts for Debugging/Panel
        $facts = [];

        // Accuracy mapping
        if ($accuracy < 40) {
            $facts[] = FactConstants::ACCURACY_CRISIS;
        } elseif ($accuracy <= 60) {
            $facts[] = FactConstants::ACCURACY_STRUGGLE;
        } elseif ($accuracy <= 70) {
            $facts[] = FactConstants::ACCURACY_STABLE;
        } elseif ($accuracy > 85) {
            $facts[] = FactConstants::ACCURACY_EXCELLENT;
        } elseif ($accuracy > 80) {
            $facts[] = FactConstants::ACCURACY_OPTIMAL;
        }

        // Trend mapping
        if ($trend === 'down') {
            $facts[] = FactConstants::TREND_DOWN;
        } elseif ($trend === 'up') {
            $facts[] = FactConstants::TREND_UP;
        } else {
            $facts[] = FactConstants::TREND_STABLE;
        }

        // Speed mapping
        if ($speed === 'fast') {
            $facts[] = FactConstants::TIME_FAST;
        } elseif ($speed === 'slow') {
            $facts[] = FactConstants::TIME_SLOW;
        } else {
            $facts[] = FactConstants::TIME_NORMAL;
        }

        // Streak mapping
        if ($streak >= 7) {
            $facts[] = FactConstants::STREAK_7D;
        } elseif ($streak >= 5) {
            $facts[] = FactConstants::STREAK_5D;
        } elseif ($streak >= 3) {
            $facts[] = FactConstants::STREAK_3D;
        }

        // Level mapping
        if ($level === StudentLevel::AHLI->value) {
            $facts[] = FactConstants::LEVEL_AHLI;
        }

        // Help mapping
        if ($hints > 3) {
            $facts[] = FactConstants::HELP_HIGH;
        } elseif ($hints >= 2) {
            $facts[] = FactConstants::HELP_MED;
        } elseif ($hints === 0) {
            $facts[] = FactConstants::HELP_NONE;
        }

        // 3. Evaluate Rules R01-R15 in order

        // --- KRISIS PEMBELAJARAN ---
        // R01: Akurasi <40%, Tren turun 2 sesi, Bantuan >3x
        if ($accuracy < 40 && $trend === 'down' && $hints > 3) {
            return $this->result('R01', FactConstants::V_CRISIS, [ActionConstants::REMEDIAL, ActionConstants::REDUCE_DIFF], $facts);
        }
        // R02: Akurasi <40%, Tren turun 2 sesi, Bantuan <=3x
        if ($accuracy < 40 && $trend === 'down' && $hints <= 3) {
            return $this->result('R02', FactConstants::V_CRISIS, [ActionConstants::REMEDIAL], $facts);
        }
        // R03: Akurasi <40%, Tren stabil/naik, Bantuan >3x
        if ($accuracy < 40 && $hints > 3) {
            return $this->result('R03', FactConstants::V_CRISIS, [ActionConstants::REDUCE_DIFF, ActionConstants::SCAFFOLD_REDUCTION], $facts);
        }

        // --- SEDANG KESULITAN ---
        // R04: Akurasi 40-60%, Respons lambat, Bantuan <=3x
        if ($accuracy >= 40 && $accuracy <= 60 && $speed === 'slow' && $hints <= 3) {
            return $this->result('R04', FactConstants::V_STRUGGLING, [ActionConstants::REDUCE_DIFF], $facts);
        }
        // R05: Akurasi 40-60%, Respons normal, Bantuan 2-3x
        if ($accuracy >= 40 && $accuracy <= 60 && $hints >= 2) {
            return $this->result('R05', FactConstants::V_STRUGGLING, [ActionConstants::REMEDIAL], $facts);
        }
        // R06: Akurasi 60-70%, Tren stabil, Bantuan <=2x
        if ($accuracy >= 60 && $accuracy <= 70 && $trend === 'stable' && $hints <= 2) {
            return $this->result('R06', FactConstants::V_STRUGGLING, [ActionConstants::FEEDBACK], $facts);
        }

        // --- PERFORMA OPTIMAL ---
        // R07: Akurasi >80%, Tren naik, Level < Ahli
        if ($accuracy > PedagogicalConstants::ACCURACY_OPTIMAL_THRESHOLD && $trend === 'up' && $level !== StudentLevel::AHLI->value) {
            return $this->result('R07', FactConstants::V_OPTIMAL, [ActionConstants::INCREASE_DIFF], $facts);
        }
        // R08: Akurasi >80%, Tren naik, Level = Ahli
        if ($accuracy > PedagogicalConstants::ACCURACY_OPTIMAL_THRESHOLD && $trend === 'up' && $level === StudentLevel::AHLI->value) {
            return $this->result('R08', FactConstants::V_OPTIMAL, [ActionConstants::NEW_CHALLENGE], $facts);
        }
        // R09: Akurasi >80%, Respons cepat, Streak >=3
        if ($accuracy > PedagogicalConstants::ACCURACY_OPTIMAL_THRESHOLD && $speed === 'fast' && $streak >= 3) {
            return $this->result('R09', FactConstants::V_OPTIMAL, [ActionConstants::INCREASE_DIFF, ActionConstants::STREAK_BONUS], $facts);
        }

        // --- KETERGANTUNGAN BANTUAN ---
        // R10: Bantuan >3x, Akurasi <50% tanpa bantuan, Tren stabil
        if ($hints > 3 && $accuracy < 50 && $trend === 'stable') {
            return $this->result('R10', FactConstants::V_DEPENDENCY, [ActionConstants::SCAFFOLD_REDUCTION, ActionConstants::REMEDIAL], $facts);
        }
        // R11: Bantuan >3x, Akurasi >60% dengan bantuan, Tren naik
        if ($hints > 3 && $accuracy > 60 && $trend === 'up') {
            return $this->result('R11', FactConstants::V_DEPENDENCY, [ActionConstants::SCAFFOLD_REDUCTION], $facts);
        }

        // --- POTENSI KEBOSANAN ---
        // R12: Akurasi >80%, Skor stagnan >=3 sesi, Streak >=5
        if ($accuracy > PedagogicalConstants::ACCURACY_OPTIMAL_THRESHOLD && $stagnantCount >= 3 && $streak >= 5) {
            return $this->result('R12', FactConstants::V_BOREDOM, [ActionConstants::NEW_CHALLENGE, ActionConstants::STREAK_BONUS], $facts);
        }
        // R13: Akurasi >80%, Respons cepat, Skor stagnan >=3 sesi
        if ($accuracy > PedagogicalConstants::ACCURACY_OPTIMAL_THRESHOLD && $speed === 'fast' && $stagnantCount >= 3) {
            return $this->result('R13', FactConstants::V_BOREDOM, [ActionConstants::INCREASE_DIFF], $facts);
        }

        // --- SPECIAL: CERTIFICATION ---
        // R15 condition: Avg Accuracy > 85% over last 3 sessions
        $last3Accuracy = 0;
        if (count($history) >= 3) {
            $last3         = array_slice($history, -3);
            $last3Accuracy = array_sum($last3) / 3;
        }

        if ($level === StudentLevel::AHLI->value && $last3Accuracy > PedagogicalConstants::ACCURACY_CERTIFICATION_THRESHOLD && $streak >= 7 && $hints === 0) {
            return $this->result('R15', FactConstants::V_OPTIMAL, [ActionConstants::CERTIFICATION], $facts);
        }

        // --- R14: DEFAULT FALLBACK ---
        return $this->result('R14', 'Normal Learning', [ActionConstants::FEEDBACK], $facts);
    }

    private function result(string $ruleId, string $diagnosis, array $recommendations, array $facts = []): array
    {
        return [
            'id'              => $ruleId,
            'diagnosis'       => $diagnosis,
            'recommendations' => $recommendations,
            'facts'           => $facts,
            'timestamp'       => now()->toIso8601String(),
            'engine_metadata' => [
                'engine_version'  => '2.1.0-forward',
                'rule_count'      => 15,
                'fact_labels'     => array_merge(FactConstants::NAMES, FactConstants::VIRTUAL_NAMES),
                'fact_categories' => [
                    'primary' => 'primary',
                    'virtual' => 'virtual',
                ],
            ],
        ];
    }
}
