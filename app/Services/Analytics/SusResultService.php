<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Contracts\Repositories\SusResultRepositoryInterface;
use App\Contracts\Services\SusResultServiceInterface;
use App\Http\Resources\SusResultResource;
use App\Models\SusAnswer;
use App\Models\SusQuestion;
use App\Models\SusResult;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use MathPHP\Statistics\Descriptive;

final readonly class SusResultService implements SusResultServiceInterface
{
    public function __construct(
        private SusResultRepositoryInterface $susResultRepository,
        private StatisticalAnalysisService $statisticalAnalysisService,
    ) {}

    public function getAllResults(?string $class = null): SupportCollection
    {
        return collect(SusResultResource::collection(
            $this->susResultRepository->getAllWithUser($class),
        )->resolve());
    }

    /** @return array<string> */
    public function getDistinctClasses(): array
    {
        return $this->susResultRepository->getDistinctClasses();
    }

    public function getStudentDetail(string $userId): ?array
    {
        $result = $this->susResultRepository->findByUserId($userId);

        if (! $result instanceof SusResult) {
            return null;
        }

        return new SusResultResource($result)->resolve();
    }

    public function hasUserSubmitted(string $userId): bool
    {
        return $this->susResultRepository->findByUserId($userId) instanceof SusResult;
    }

    public function submitResult(array $data): SusResult
    {
        return DB::transaction(function () use ($data): SusResult {
            $result = $this->susResultRepository->create([
                'user_id'     => $data['user_id'],
                'nim'         => $data['nim']         ?? null,
                'class'       => $data['class']       ?? null,
                'comments'    => $data['comments']    ?? null,
                'suggestions' => $data['suggestions'] ?? null,
                'total_score' => 0,
            ]);

            $answers = $data['answers'] ?? [];

            // If answers are passed as q1, q2... in the top level
            if (empty($answers)) {
                $questions = SusQuestion::orderBy('order')->get();
                foreach ($questions as $question) {
                    $key = 'q' . $question->order;
                    if (isset($data[$key])) {
                        $answers[$question->id] = $data[$key];
                    }
                }
            }

            $totalContribution = 0;
            foreach ($answers as $questionId => $value) {
                $answer = SusAnswer::create([
                    'sus_result_id'    => $result->id,
                    'sus_question_id'  => $questionId,
                    'value'            => (int) $value,
                ]);

                $question = $answer->question;
                if ($question->is_reverse) {
                    $totalContribution += (5 - (int) $value);
                } else {
                    $totalContribution += ((int) $value - 1);
                }
            }

            $result->update(['total_score' => (float) ($totalContribution * 2.5)]);

            return $result;
        });
    }

    public function calculateItemScores(SusResult|array $result): array
    {
        if ($result instanceof SusResult) {
            $answers = $result->answers()->with('question')->get();
            $scores  = [];
            foreach ($answers as $answer) {
                $val                                    = $answer->value;
                $scores['q' . $answer->question->order] = $answer->question->is_reverse
                    ? (5 - $val)
                    : ($val - 1);
            }

            return $scores;
        }

        return [];
    }

    /** @return array<string, mixed> */
    public function calculateGlobalMetrics(SupportCollection $results): array
    {
        if ($results->isEmpty()) {
            return [
                'average_score'   => 0,
                'total_responses' => 0,
                'grade'           => 'N/A',
                'adjective'       => 'N/A',
                'acceptability'   => 'N/A',
                'items'           => [],
            ];
        }

        $average = $results->avg('total_score');

        return [
            'average_score'   => round((float) $average, 2),
            'total_responses' => $results->count(),
            'grade'           => $this->getGradeForScore($average),
            'adjective'       => $this->getAdjectiveForScore($average),
            'acceptability'   => $this->getAcceptabilityForScore($average),
            'items'           => $this->calculateAveragePerItem($results),
        ];
    }

    private function calculateAveragePerItem(SupportCollection $results): array
    {
        $averages  = [];
        $questions = SusQuestion::orderBy('order')->get();

        foreach ($questions as $question) {
            $avgValue = SusAnswer::where('sus_question_id', $question->id)
                ->whereIn('sus_result_id', $results->pluck('id'))
                ->avg('value');

            $averages['q' . $question->order] = [
                'text'    => $question->text,
                'average' => round((float) ($avgValue ?? 0), 2),
            ];
        }

        return $averages;
    }

    private function getGradeForScore(float $score): string
    {
        if ($score >= 80.3) {
            return 'A';
        }

        if ($score >= 74) {
            return 'B';
        }

        if ($score >= 68) {
            return 'C';
        }

        if ($score >= 51) {
            return 'D';
        }

        return 'F';
    }

    private function getAdjectiveForScore(float $score): string
    {
        if ($score >= 85) {
            return 'Excellent';
        }

        if ($score >= 71.4) {
            return 'Good';
        }

        if ($score >= 50.9) {
            return 'OK';
        }

        if ($score >= 35.7) {
            return 'Poor';
        }

        return 'Worst Imaginable';
    }

    private function getAcceptabilityForScore(float $score): string
    {
        if ($score >= 71) {
            return 'Acceptable';
        }

        if ($score >= 51) {
            return 'Marginal';
        }

        return 'Not Acceptable';
    }

    public function calculateStatisticalAnalysis(?string $class1 = null, ?string $class2 = null): array
    {
        $results1 = $this->susResultRepository->getAllWithUser($class1);
        $results2 = $class2 ? $this->susResultRepository->getAllWithUser($class2) : collect();

        // 1. Reliability (Cronbach's Alpha) for Class 1 (or all if class1 is null)
        $matrix = $this->buildAnswerMatrix($results1);
        $alpha  = $this->statisticalAnalysisService->cronbachAlpha($matrix);

        // 2. Comparisons
        $comparison = null;
        $tTest      = null;
        $desc1      = null;
        $desc2      = null;

        if ($results1->isNotEmpty()) {
            $scores1 = $results1->pluck('total_score')->toArray();
            $desc1   = Descriptive::describe($scores1);

            if ($class2 && $results2->isNotEmpty()) {
                $scores2 = $results2->pluck('total_score')->toArray();
                $desc2   = Descriptive::describe($scores2);

                $comparison = $this->statisticalAnalysisService->mannWhitneyU($scores1, $scores2);
                $tTest      = $this->statisticalAnalysisService->independentTTest($scores1, $scores2);
            }
        }

        return [
            'reliability' => [
                'cronbach_alpha' => round($alpha, 3),
                'status'         => $alpha > 0.6 ? 'Reliabel' : 'Tidak Reliabel',
                'n_items'        => count($matrix[0] ?? []),
                'n_samples'      => count($matrix),
            ],
            'descriptive' => [
                'group1' => $desc1,
                'group2' => $desc2,
            ],
            'comparison'  => [
                'mann_whitney' => $comparison,
                't_test'       => $tTest,
            ],
        ];
    }

    private function buildAnswerMatrix(SupportCollection $results): array
    {
        $matrix = [];
        foreach ($results as $result) {
            $respondentAnswers = [];
            // Get item contributions (the processed 0-4 scores)
            $itemScores = $this->calculateItemScores($result);
            ksort($itemScores);
            $respondentAnswers = $itemScores;

            if ($respondentAnswers !== []) {
                $matrix[] = $respondentAnswers;
            }
        }

        return $matrix;
    }
}
