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
        string $actionCommand,
        Material $material,
        Question $question,
        ?string $userId = null,
    ): array {
        unset($question, $userId);

        return match ($actionCommand) {
            'STUDY_MATERIAL' => [
                'label' => 'Ulas Materi: ' . $material->title,
                'url'   => route('mahasiswa.materials.show', $material->id),
                'type'  => 'material',
            ],
            AdaptiveConstants::ACTION_REDUCE_DIFFICULTY => $this->questionAction(
                $material,
                'Sepertinya soal ini agak sulit. Kami menyesuaikan tingkat kesulitannya agar kamu lebih nyaman belajar!',
            ),
            AdaptiveConstants::ACTION_INCREASE_DIFFICULTY => $this->questionAction(
                $material,
                'Luar Biasa! Kamu menjawab dengan sangat cepat dan tepat. Tantangan selanjutnya telah menantimu di level yang lebih tinggi!',
            ),
            AdaptiveConstants::ACTION_NEXT_MATERIAL   => $this->jumpToNextMaterial($material),
            AdaptiveConstants::ACTION_FINISH_MATERIAL => [
                'label' => 'Selesaikan Modul',
                'url'   => route('mahasiswa.dashboard'),
                'type'  => 'navigation',
            ],
            AdaptiveConstants::ACTION_ISSUE_CERTIFICATE => [
                'label' => 'Klaim Sertifikat',
                'url'   => route('mahasiswa.dashboard'),
                'type'  => 'certificate',
            ],
            AdaptiveConstants::ACTION_STUDY_SYNTAX   => $this->studySubMaterial($material, 'sintaks', 'Pelajari Sintaks'),
            AdaptiveConstants::ACTION_STUDY_THEORY   => $this->studySubMaterial($material, 'teori', 'Pahami Konsep'),
            AdaptiveConstants::ACTION_STUDY_MIXED    => $this->studySubMaterial($material, 'mixed', 'Materi Komprehensif'),
            AdaptiveConstants::ACTION_STUDY_VISUAL   => $this->studySubMaterial($material, null, 'Materi Visual', 'visual'),
            AdaptiveConstants::ACTION_STUDY_TEXTUAL  => $this->studySubMaterial($material, null, 'Materi Tekstual', 'textual'),
            default                                  => $this->questionAction($material),
        };
    }

    private function questionAction(Material $material, ?string $message = null): array
    {
        $action = [
            'label' => 'Soal Berikutnya',
            'url'   => $this->questionUrl($material),
            'type'  => 'question',
        ];

        if (is_string($message) && $message !== '') {
            $action['message'] = $message;
        }

        return $action;
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
