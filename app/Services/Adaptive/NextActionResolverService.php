<?php

namespace App\Services\Adaptive;

use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Services\NextActionResolverServiceInterface;
use App\Contracts\Services\ProgressServiceInterface;
use App\Contracts\Services\QuestionServiceInterface;
use App\Models\Material;
use App\Models\Question;

/**
 * Service to resolve adaptive next action commands into URLs and metadata.
 */
class NextActionResolverService implements NextActionResolverServiceInterface
{
    public function __construct(
        protected QuestionServiceInterface $questionService,
        protected ProgressServiceInterface $progressService,
        protected QuestionRepositoryInterface $questionRepo,
    ) {}

    /**
     * Resolve dynamic next action command into URL and metadata.
     */
    public function resolve(string $actionCommand, Material $material, Question $question, ?int $userId = null): array
    {
        return match ($actionCommand) {
            'STUDY_MATERIAL' => [
                'label' => 'Ulas Materi: ' . $material->title,
                'url' => route('mahasiswa.materials.show', $material->id),
                'type' => 'material',
            ],
            'REDUCE_DIFFICULTY' => $this->reduceDifficulty($material, $question, $userId),
            'INCREASE_DIFFICULTY' => $this->increaseDifficulty($material, $userId),
            'NEXT_MATERIAL' => $this->jumpToNextMaterial($material),
            'FINISH_MATERIAL' => [
                'label' => 'Selesaikan Modul',
                'url' => route('mahasiswa.dashboard'),
                'type' => 'navigation',
            ],
            'ISSUE_CERTIFICATE' => [
                'label' => 'Klaim Sertifikat',
                'url' => route('mahasiswa.dashboard'),
                'type' => 'certificate',
            ],
            'STUDY_SYNTAX' => $this->studySubMaterial($material, 'sintaks', 'Pelajari Sintaks'),
            'STUDY_THEORY' => $this->studySubMaterial($material, 'teori', 'Pahami Konsep'),
            'STUDY_MIXED' => $this->studySubMaterial($material, 'mixed', 'Materi Komprehensif'),
            'STUDY_VISUAL' => $this->studySubMaterial($material, null, 'Materi Visual'),
            'STUDY_TEXTUAL' => $this->studySubMaterial($material, null, 'Materi Tekstual'),
            default => [
                'label' => 'Soal Berikutnya',
                'url' => (function () use ($material, $question) {
                    session(['quiz_difficulty' => $question->difficulty]);

                    return route('mahasiswa.materials.questions.show', ['material' => $material->id]);
                })(),
                'type' => 'question',
            ],
        };
    }

    protected function reduceDifficulty(Material $material, Question $question, ?int $userId = null): array
    {
        $hasBeginner = $this->questionService->existsByMaterialAndDifficulty($material->id, 'beginner');

        // Check if there are unanswered beginner questions
        $hasUnansweredBeginner = false;
        if ($hasBeginner && $userId) {
            $answeredIds = $this->progressService->getAnsweredQuestionIds($userId, $material->id);
            $beginnerQuestions = $this->questionRepo->getByMaterialAndDifficulty($material->id, 'beginner');
            $hasUnansweredBeginner = $beginnerQuestions->whereNotIn('id', $answeredIds->toArray())->isNotEmpty();
        }

        // Store difficulty preference in session only if there are unanswered questions
        if ($hasUnansweredBeginner) {
            session(['quiz_difficulty' => 'beginner']);
        }

        return [
            'label' => $hasUnansweredBeginner ? 'Coba Soal Pemula' : 'Ulas Materi Dasar',
            'url' => $hasUnansweredBeginner
            ? route('mahasiswa.materials.questions.show', ['material' => $material->id])
            : route('mahasiswa.materials.show', $material->id),
            'type' => $hasUnansweredBeginner ? 'question' : 'material',
        ];
    }

    protected function increaseDifficulty(Material $material, ?int $userId = null): array
    {
        $hasHard = $this->questionService->existsByMaterialAndDifficulty($material->id, 'hard');

        // Check if there are unanswered hard questions
        $hasUnansweredHard = false;
        if ($hasHard && $userId) {
            $answeredIds = $this->progressService->getAnsweredQuestionIds($userId, $material->id);
            $hardQuestions = $this->questionRepo->getByMaterialAndDifficulty($material->id, 'hard');
            $hasUnansweredHard = $hardQuestions->whereNotIn('id', $answeredIds->toArray())->isNotEmpty();
        }

        // Store difficulty preference in session only if there are unanswered questions
        if ($hasUnansweredHard) {
            session(['quiz_difficulty' => 'hard']);
        }

        return [
            'label' => $hasUnansweredHard ? 'Tantangan Menantang' : 'Ulas Materi Lagi',
            'url' => $hasUnansweredHard
            ? route('mahasiswa.materials.questions.show', ['material' => $material->id])
            : route('mahasiswa.materials.show', $material->id),
            'type' => $hasUnansweredHard ? 'question' : 'material',
        ];
    }

    /**
     * Resolve study sub-material action.
     * Always fallback to main materials page if no sub-material found.
     */
    protected function studySubMaterial(Material $material, ?string $jenisKonten, string $label, ?string $learningStyle = null): array
    {
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
            'url' => $subMaterial
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
                'url' => route('mahasiswa.materials.show', $nextMaterial->id),
                'type' => 'material',
            ];
        }

        // No more materials - completed all modules
        return [
            'label' => 'Selesai! Kembali ke Dashboard',
            'url' => route('mahasiswa.dashboard'),
            'type' => 'navigation',
        ];
    }
}
