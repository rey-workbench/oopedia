<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Services\NextActionResolverServiceInterface;
use App\Models\Material;
use App\Models\Question;
use App\Rules\Adaptive\Constants\AdaptiveConstants;

final class NextActionResolverService implements NextActionResolverServiceInterface
{
    public function resolve(
        string $actionCode,
        Material $material,
        Question $question,
        ?string $userId = null,
    ): array {
        unset($question, $userId);

        $action = \App\Models\AdaptiveAction::where('code', $actionCode)->first();
        $instructions = $action?->instructions ?? [];
        $command = $instructions['next_action'] ?? AdaptiveConstants::ACTION_NEXT_QUESTION;
        $label = $instructions['label'] ?? 'Lanjut';

        return match ($command) {
            AdaptiveConstants::ACTION_STUDY_MATERIAL => [
                'label' => $label . ': ' . $material->title,
                'url'   => route('mahasiswa.materials.show', $material->id),
                'type'  => 'material',
            ],
            AdaptiveConstants::ACTION_REDUCE_DIFFICULTY,
            AdaptiveConstants::ACTION_INCREASE_DIFFICULTY,
            AdaptiveConstants::ACTION_NEXT_QUESTION => $this->questionAction($material, $label),

            AdaptiveConstants::ACTION_NEXT_MATERIAL => $this->jumpToNextMaterial($material),

            AdaptiveConstants::ACTION_FINISH_MATERIAL => [
                'label' => $label,
                'url'   => route('mahasiswa.dashboard'),
                'type'  => 'navigation',
            ],
            AdaptiveConstants::ACTION_ISSUE_CERTIFICATE => [
                'label' => $label,
                'url'   => route('mahasiswa.dashboard'),
                'type'  => 'certificate',
            ],
            AdaptiveConstants::ACTION_STUDY_SYNTAX => $this->studySubMaterial($material, 'sintaks', $label),
            AdaptiveConstants::ACTION_STUDY_THEORY => $this->studySubMaterial($material, 'teori', $label),
            AdaptiveConstants::ACTION_STUDY_MIXED  => $this->studySubMaterial($material, 'mixed', $label),
            AdaptiveConstants::ACTION_STUDY_VISUAL => $this->studySubMaterial($material, null, $label, 'visual'),
            AdaptiveConstants::ACTION_STUDY_TEXTUAL => $this->studySubMaterial($material, null, $label, 'textual'),
            default => $this->questionAction($material, $label),
        };
    }

    private function questionAction(Material $material, string $label = 'Soal Berikutnya'): array
    {
        return [
            'label' => $label,
            'url'   => $this->questionUrl($material),
            'type'  => 'question',
        ];
    }

    private function questionUrl(Material $material): string
    {
        return route('mahasiswa.materials.questions.show', ['material' => $material->id]);
    }

    protected function studySubMaterial(
        Material $material,
        ?string $jenisKonten,
        string $label,
        ?string $learningStyle = null,
    ): array {
        $query = $material->subMaterials();

        if ($jenisKonten) {
            $query->where('jenis_konten', $jenisKonten);
        }

        if ($learningStyle) {
            $query->where('learning_style', $learningStyle);
        }

        $subMaterial = $query->ordered()->first();

        return [
            'label' => $label,
            'url'   => $subMaterial
            ? route('mahasiswa.submaterials.show', ['material' => $material->id, 'submaterial' => $subMaterial->id])
            : route('mahasiswa.materials.show', $material->id),
            'type' => 'material',
        ];
    }

    protected function jumpToNextMaterial(Material $currentMaterial): array
    {
        $nextMaterial = $currentMaterial->getNextMaterial();

        if ($nextMaterial) {
            return [
                'label' => 'Lanjut ke: ' . $nextMaterial->title,
                'url'   => route('mahasiswa.materials.show', $nextMaterial->id),
                'type'  => 'material',
            ];
        }

        return [
            'label' => 'Selesai! Kembali ke Dashboard',
            'url'   => route('mahasiswa.dashboard'),
            'type'  => 'navigation',
        ];
    }
}
