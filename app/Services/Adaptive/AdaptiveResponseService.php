<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Rules\Adaptive\Constants\ActionConstants;
use App\Rules\Adaptive\Constants\StudentStateSchema;

final class AdaptiveResponseService
{
    /**
     * Map adaptive engine results to UI-friendly navigation data.
     */
    public function resolveUiResponse(array $engineResult, string $materialId, bool $isCorrect): array
    {
        $flow = $engineResult['triggered_rule']['action']
              ?? $engineResult['new_state']['next_action']
              ?? ActionConstants::FLOW_NEXT;

        $rule = $engineResult['triggered_rule'] ?? [];

        $baseResponse = match ($flow) {
            ActionConstants::FLOW_FINISH => [
                'type'  => 'redirect',
                'url'   => route('mahasiswa.materials.questions.index'),
                'label' => 'Selesai Materi',
            ],
            ActionConstants::FLOW_REVIEW => [
                'type'  => 'redirect',
                'url'   => $this->resolveReviewUrl($materialId, $engineResult),
                'label' => 'Lihat Materi',
            ],
            default => [
                'type' => 'continue',
                'url'  => route('mahasiswa.materials.questions.show', [
                    'material'     => $materialId,
                    'sub_material' => $engineResult['new_state'][StudentStateSchema::TARGET_DIFFICULTY] ?? null,
                ]),
                'label' => 'Lanjut',
            ]
        };

        return array_merge($baseResponse, [
            'title'   => $rule['title']   ?? ($isCorrect ? 'Luar Biasa!' : 'Belum Tepat'),
            'message' => $rule['message'] ?? null,
        ]);
    }

    private function resolveReviewUrl(string $materialId, array $engineResult): string
    {
        // Jika ada target sub_materi spesifik dari intervensi krisis/remedial
        $subMaterialId = $engineResult['new_state']['target_sub_material_id'] ?? null;

        return route('mahasiswa.materials.show', [
            'material'        => $materialId,
            'sub_material_id' => $subMaterialId,
        ]);
    }
}
