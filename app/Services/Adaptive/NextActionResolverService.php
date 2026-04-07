<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Services\NextActionResolverServiceInterface;
use App\Models\Material;
use App\Models\Question;

/**
 * Service to resolve adaptive next action commands into URLs and metadata.
 */
final class NextActionResolverService implements NextActionResolverServiceInterface
{
    /**
     * Resolve dynamic next action command into URL and metadata.
     */
    public function resolve(
        string $actionCommand,
        Material $material,
        Question $question,
        ?string $userId = null,
    ): array {
        $subMaterialParam = $question->sub_material_id ? ['sub_material' => $question->sub_material_id] : [];

        return match ($actionCommand) {
            'STUDY_MATERIAL' => [
                'label' => 'Ulas Materi: ' . $material->title,
                'url'   => route('mahasiswa.materials.show', $material->id),
                'type'  => 'material',
            ],
            'REDUCE_DIFFICULTY' => [
                'label'   => 'Soal Berikutnya',
                'url'     => route('mahasiswa.materials.questions.show', array_merge(
                    ['material' => $material->id],
                    $subMaterialParam,
                )),
                'type'    => 'question',
                'message' => 'Sepertinya soal ini agak sulit. ' .
                    'Kami menyesuaikan tingkat kesulitannya agar kamu lebih nyaman belajar!',
            ],
            'INCREASE_DIFFICULTY' => [
                'label'   => 'Soal Berikutnya',
                'url'     => route('mahasiswa.materials.questions.show', array_merge(
                    ['material' => $material->id],
                    $subMaterialParam,
                )),
                'type'    => 'question',
                'message' => 'Luar Biasa! Kamu menjawab dengan sangat cepat dan tepat. ' .
                    'Tantangan selanjutnya telah menantimu di level yang lebih tinggi!',
            ],
            'NEXT_MATERIAL'   => $this->jumpToNextMaterial($material),
            'FINISH_MATERIAL' => [
                'label' => 'Selesaikan Modul',
                'url'   => route('mahasiswa.dashboard'),
                'type'  => 'navigation',
            ],
            'ISSUE_CERTIFICATE' => [
                'label' => 'Klaim Sertifikat',
                'url'   => route('mahasiswa.dashboard'),
                'type'  => 'certificate',
            ],
            'STUDY_SYNTAX'  => $this->studySubMaterial($material, 'sintaks', 'Pelajari Sintaks'),
            'STUDY_THEORY'  => $this->studySubMaterial($material, 'teori', 'Pahami Konsep'),
            'STUDY_MIXED'   => $this->studySubMaterial($material, 'mixed', 'Materi Komprehensif'),
            'STUDY_VISUAL'  => $this->studySubMaterial($material, null, 'Materi Visual', 'visual'),
            'STUDY_TEXTUAL' => $this->studySubMaterial($material, null, 'Materi Tekstual', 'textual'),
            default         => [
                'label' => 'Soal Berikutnya',
                'url'   => route('mahasiswa.materials.questions.show', array_merge(
                    ['material' => $material->id],
                    $subMaterialParam,
                )),
                'type'  => 'question',
            ],
        };
    }

    /**
     * Resolve study sub-material action.
     * Always fallback to main materials page if no sub-material found.
     */
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

    /**
     * Jump to next material in sequence (accelerated jump / fast-track).
     */
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

        // No more materials - completed all modules
        return [
            'label' => 'Selesai! Kembali ke Dashboard',
            'url'   => route('mahasiswa.dashboard'),
            'type'  => 'navigation',
        ];
    }
}
