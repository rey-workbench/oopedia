<?php

namespace App\Services\Adaptive;

use App\Models\Material;
use App\Models\Question;
use App\Contracts\Services\QuestionServiceInterface;

/**
 * Service to resolve adaptive next action commands into URLs and metadata.
 */
class NextActionResolverService
{
    public function __construct(
        protected QuestionServiceInterface $questionService
    ) {}

    /**
     * Resolve dynamic next action command into URL and metadata.
     */
    public function resolve(string $actionCommand, Material $material, Question $question): array
    {
        return match ($actionCommand) {
            'STUDY_MATERIAL' => [
                'label' => 'Ulas Materi: ' . $material->title,
                'url' => route('mahasiswa.materials.show', $material->id),
                'type' => 'material'
            ],
            'REDUCE_DIFFICULTY' => $this->reduceDifficulty($material, $question),
            'INCREASE_DIFFICULTY' => $this->increaseDifficulty($material),
            'FINISH_MATERIAL' => [
                'label' => 'Selesaikan Modul',
                'url' => route('mahasiswa.dashboard'),
                'type' => 'navigation'
            ],
            'ISSUE_CERTIFICATE' => [
                'label' => 'Klaim Sertifikat',
                'url' => route('mahasiswa.dashboard'),
                'type' => 'certificate'
            ],
            'STUDY_SYNTAX' => $this->studySubMaterial($material, 'sintaks', 'Pelajari Sintaks'),
            'STUDY_THEORY' => $this->studySubMaterial($material, 'teori', 'Pahami Konsep'),
            'STUDY_MIXED' => $this->studySubMaterial($material, 'mixed', 'Materi Komprehensif'),
            'STUDY_VISUAL' => $this->studySubMaterial($material, null, 'Materi Visual'),
            'STUDY_TEXTUAL' => $this->studySubMaterial($material, null, 'Materi Tekstual'),
            default => [
                'label' => 'Soal Berikutnya',
                'url' => route('mahasiswa.materials.questions.show', ['material' => $material->id]),
                'type' => 'question'
            ],
        };
    }

    protected function reduceDifficulty(Material $material, Question $question): array
    {
        $hasBeginner = $this->questionService->existsByMaterialAndDifficulty($material->id, 'beginner');
        
        return [
            'label' => $hasBeginner ? 'Coba Soal Pemula' : 'Ulas Materi Dasar',
            'url' => $hasBeginner 
                ? route('mahasiswa.materials.questions.show', ['material' => $material->id, 'difficulty' => 'beginner'])
                : route('mahasiswa.materials.show', $material->id),
            'type' => $hasBeginner ? 'question' : 'material'
        ];
    }

    protected function increaseDifficulty(Material $material): array
    {
        $hasHard = $this->questionService->existsByMaterialAndDifficulty($material->id, 'hard');
        
        return [
            'label' => $hasHard ? 'Tantangan Menantang' : 'Lanjut ke Materi Baru',
            'url' => $hasHard
                ? route('mahasiswa.materials.questions.show', ['material' => $material->id, 'difficulty' => 'hard'])
                : route('mahasiswa.dashboard'),
            'type' => $hasHard ? 'question' : 'navigation'
        ];
    }

    /**
     * Resolve study sub-material action.
     * Always fallback to main materials page if no sub-material found.
     */
    protected function studySubMaterial(Material $material, ?string $jenisKonten, string $label): array
    {
        // Try to find specific sub-material by jenis_konten if specified
        $subMaterial = $jenisKonten 
            ? $material->subMaterials()->where('jenis_konten', $jenisKonten)->ordered()->first()
            : $material->subMaterials()->ordered()->first();

        return [
            'label' => $label,
            'url' => $subMaterial 
                ? route('mahasiswa.submaterials.show', ['material' => $material->id, 'submaterial' => $subMaterial->id])
                : route('mahasiswa.materials.show', $material->id),
            'type' => 'material'
        ];
    }
}
