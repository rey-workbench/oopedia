<?php

declare(strict_types=1);

namespace App\Services\Adaptive;

use App\Contracts\Services\NextActionResolverServiceInterface;
use App\Models\Material;
use App\Models\Question;

final class NextActionResolverService implements NextActionResolverServiceInterface
{
    public function resolve(
        string $actionCommand,
        Material $material,
        Question $question,
        ?string $userId = null,
    ): array {
        return match ($actionCommand) {
            'STUDY_MATERIAL' => [
                'label' => 'Ulas Materi: ' . $material->title,
                'url'   => route('mahasiswa.materials.show', $material->id),
                'type'  => 'material',
            ],
            'REDUCE_DIFFICULTY' => [
                'label'   => 'Soal Berikutnya',
                'url'     => $this->questionUrl($material),
                'type'    => 'question',
                'message' => 'Sepertinya soal ini agak sulit. ' .
                    'Kami menyesuaikan tingkat kesulitannya agar kamu lebih nyaman belajar!',
            ],
            'INCREASE_DIFFICULTY' => [
                'label'   => 'Soal Berikutnya',
                'url'     => $this->questionUrl($material),
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
                'url'   => $this->questionUrl($material),
                'type'  => 'question',
            ],
        };
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
