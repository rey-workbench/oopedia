<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Services\AdaptiveEngineServiceInterface;
use App\Enums\Lms\StudentLevel;
use App\Rules\Adaptive\Constants\ActionConstants;
use App\Rules\Adaptive\Constants\FactConstants;
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

        // 2. Evaluate Rules R01-R15 in order

        // --- KRISIS PEMBELAJARAN ---
        // R01: Akurasi <40%, Tren turun 2 sesi, Bantuan >3x
        if ($accuracy < 40 && $trend === 'down' && $hints > 3) {
            return $this->result('R01', FactConstants::V_CRISIS, [ActionConstants::REMEDIAL, ActionConstants::REDUCE_DIFF]);
        }
        // R02: Akurasi <40%, Tren turun 2 sesi, Bantuan <=3x
        if ($accuracy < 40 && $trend === 'down' && $hints <= 3) {
            return $this->result('R02', FactConstants::V_CRISIS, [ActionConstants::REMEDIAL]);
        }
        // R03: Akurasi <40%, Tren stabil/naik, Bantuan >3x
        if ($accuracy < 40 && $hints > 3) {
            return $this->result('R03', FactConstants::V_CRISIS, [ActionConstants::REDUCE_DIFF, ActionConstants::SCAFFOLD_REDUCTION]);
        }

        // --- SEDANG KESULITAN ---
        // R04: Akurasi 40-60%, Respons lambat, Bantuan <=3x
        if ($accuracy >= 40 && $accuracy <= 60 && $speed === 'slow' && $hints <= 3) {
            return $this->result('R04', FactConstants::V_STRUGGLING, [ActionConstants::REDUCE_DIFF]);
        }
        // R05: Akurasi 40-60%, Respons normal, Bantuan 2-3x
        if ($accuracy >= 40 && $accuracy <= 60 && $hints >= 2) {
            return $this->result('R05', FactConstants::V_STRUGGLING, [ActionConstants::REMEDIAL]);
        }
        // R06: Akurasi 60-70%, Tren stabil, Bantuan <=2x
        if ($accuracy >= 60 && $accuracy <= 70 && $trend === 'stable' && $hints <= 2) {
            return $this->result('R06', FactConstants::V_STRUGGLING, [ActionConstants::FEEDBACK]);
        }

        // --- PERFORMA OPTIMAL ---
        // R07: Akurasi >80%, Tren naik, Level < Ahli
        if ($accuracy > 80 && $trend === 'up' && $level !== StudentLevel::AHLI->value) {
            return $this->result('R07', FactConstants::V_OPTIMAL, [ActionConstants::INCREASE_DIFF]);
        }
        // R08: Akurasi >80%, Tren naik, Level = Ahli
        if ($accuracy > 80 && $trend === 'up' && $level === StudentLevel::AHLI->value) {
            return $this->result('R08', FactConstants::V_OPTIMAL, [ActionConstants::NEW_CHALLENGE]);
        }
        // R09: Akurasi >80%, Respons cepat, Streak >=3
        if ($accuracy > 80 && $speed === 'fast' && $streak >= 3) {
            return $this->result('R09', FactConstants::V_OPTIMAL, [ActionConstants::INCREASE_DIFF, ActionConstants::STREAK_BONUS]);
        }

        // --- KETERGANTUNGAN BANTUAN ---
        // R10: Bantuan >3x, Akurasi <50% tanpa bantuan, Tren stabil
        if ($hints > 3 && $accuracy < 50 && $trend === 'stable') {
            return $this->result('R10', FactConstants::V_DEPENDENCY, [ActionConstants::SCAFFOLD_REDUCTION, ActionConstants::REMEDIAL]);
        }
        // R11: Bantuan >3x, Akurasi >60% dengan bantuan, Tren naik
        if ($hints > 3 && $accuracy > 60 && $trend === 'up') {
            return $this->result('R11', FactConstants::V_DEPENDENCY, [ActionConstants::SCAFFOLD_REDUCTION]);
        }

        // --- POTENSI KEBOSANAN ---
        // R12: Akurasi >80%, Skor stagnan >=3 sesi, Streak >=5
        if ($accuracy > 80 && $stagnantCount >= 3 && $streak >= 5) {
            return $this->result('R12', FactConstants::V_BOREDOM, [ActionConstants::NEW_CHALLENGE, ActionConstants::STREAK_BONUS]);
        }
        // R13: Akurasi >80%, Respons cepat, Skor stagnan >=3 sesi
        if ($accuracy > 80 && $speed === 'fast' && $stagnantCount >= 3) {
            return $this->result('R13', FactConstants::V_BOREDOM, [ActionConstants::INCREASE_DIFF]);
        }

        // --- SPECIAL: CERTIFICATION ---
        // R15: Level = Ahli, Akurasi >85%, Streak >=7, Bantuan = 0
        if ($level === StudentLevel::AHLI->value && $accuracy > 85 && $streak >= 7 && $hints === 0) {
            return $this->result('R15', FactConstants::V_OPTIMAL, [ActionConstants::CERTIFICATION]);
        }

        // --- R14: DEFAULT FALLBACK ---
        return $this->result('R14', 'Normal Learning', [ActionConstants::FEEDBACK]);
    }

    private function result(string $ruleId, string $diagnosis, array $recommendations): array
    {
        return [
            'rule_id'         => $ruleId,
            'diagnosis'       => $diagnosis,
            'recommendations' => $recommendations,
            'timestamp'       => now()->toIso8601String(),
        ];
    }
}
