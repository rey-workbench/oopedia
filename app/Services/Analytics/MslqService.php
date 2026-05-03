<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Contracts\Repositories\MslqRepositoryInterface;
use App\Contracts\Services\MslqServiceInterface;
use App\Enums\Lms\MslqCategory;
use App\Http\Resources\MslqResultResource;
use App\Models\MslqAnswer;
use App\Models\MslqResult;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use MathPHP\Statistics\Descriptive;

final readonly class MslqService implements MslqServiceInterface
{
    public function __construct(
        private MslqRepositoryInterface $mslqRepository,
        private StatisticalAnalysisService $statisticalAnalysisService,
    ) {}

    public function getAdminResults(?string $class = null): LengthAwarePaginator
    {
        return $this->mslqRepository->getAll($class)
            ->through(fn ($result) => new MslqResultResource($result)->resolve());
    }

    public function getDistinctClasses(): Collection
    {
        return $this->mslqRepository->getDistinctClasses();
    }

    public function calculateGlobalMetrics(?string $class = null): array
    {
        $results = $this->mslqRepository->getAllForCalculation($class);

        if ($results->isEmpty()) {
            return [
                'averages'       => [],
                'avg_motivation' => 0,
                'avg_strategy'   => 0,
            ];
        }

        $allScales        = [];
        $motivationTotals = [];
        $strategyTotals   = [];

        foreach ($results as $result) {
            foreach ($result->scores_by_scale as $scale => $score) {
                $allScales[$scale][] = $score;
            }

            $motivationTotals[] = $result->total_motivation;
            $strategyTotals[]   = $result->total_strategy;
        }

        $averages = [];
        foreach ($allScales as $scale => $scores) {
            $averages[$scale] = round(array_sum($scores) / count($scores), 2);
        }

        return [
            'averages'       => $averages,
            'avg_motivation' => round(array_sum($motivationTotals) / count($motivationTotals), 2),
            'avg_strategy'   => round(array_sum($strategyTotals) / count($strategyTotals), 2),
        ];
    }

    /** @return array<string, mixed> */
    public function getResultDetail(string $id): array
    {
        $result = $this->mslqRepository->findWithRelations($id);

        return new MslqResultResource($result)->resolve();
    }

    public function storeSubmission(array $data, int|string $userId, string $nim, string $class): MslqResult
    {
        return DB::transaction(function () use ($data, $userId, $nim, $class): MslqResult {
            $mslqResult = $this->mslqRepository->create([
                'user_id'          => $userId,
                'nim'              => $nim,
                'class'            => $class,
                'scores_by_scale'  => [],
                'total_motivation' => 0,
                'total_strategy'   => 0,
            ]);

            foreach ($data as $questionId => $value) {
                MslqAnswer::create([
                    'mslq_result_id'   => $mslqResult->getKey(),
                    'mslq_question_id' => $questionId,
                    'value'            => $value,
                ]);
            }

            $finalScores = $this->calculateScores($mslqResult);
            $mslqResult->update($finalScores);

            return $mslqResult;
        });
    }

    public function getResultsForExport(?string $class = null): Collection
    {
        return collect(MslqResultResource::collection(
            $this->mslqRepository->getAllForCalculation($class),
        )->resolve());
    }

    private function calculateScores(MslqResult $mslqResult): array
    {
        $answers = $mslqResult->answers()->with('question')->get();
        $grouped = $answers->groupBy(fn (MslqAnswer $mslqAnswer) => $mslqAnswer->question->scale);

        $scoresByScale    = [];
        $motivationScores = collect();
        $strategyScores   = collect();

        foreach ($grouped as $scaleValue => $scaleAnswers) {
            $average                    = $this->calculateScaleAverage($scaleAnswers);
            $scoresByScale[$scaleValue] = round($average, 2);

            $category = $scaleAnswers->first()->question->category;
            if ($category === MslqCategory::MOTIVATION) {
                $motivationScores->push($average);
            } else {
                $strategyScores->push($average);
            }
        }

        return [
            'scores_by_scale'  => $scoresByScale,
            'total_motivation' => round($motivationScores->avg() ?? 0, 2),
            'total_strategy'   => round($strategyScores->avg() ?? 0, 2),
        ];
    }

    private function calculateScaleAverage(Collection $answers): float
    {
        if ($answers->isEmpty()) {
            return 0.0;
        }

        $total = $answers->sum(function (MslqAnswer $mslqAnswer) {
            $value = $mslqAnswer->value;
            if ($mslqAnswer->question->is_reverse) {
                return 8 - $value;
            }

            return $value;
        });

        return $total / $answers->count();
    }

    public function calculateStatisticalAnalysis(?string $class1 = null, ?string $class2 = null): array
    {
        $results1 = $this->mslqRepository->getAllForCalculation($class1);
        $results2 = $class2 ? $this->mslqRepository->getAllForCalculation($class2) : collect();

        // 1. Cronbach's Alpha (Reliability)
        $matrix = $this->buildAnswerMatrix($results1->merge($results2));
        $alpha  = $this->statisticalAnalysisService->cronbachAlpha($matrix);

        // 2. Mann-Whitney U (Comparison if 2 groups provided)
        $comparison = null;
        $tTest      = null;
        $desc1      = null;
        $desc2      = null;

        if ($class1 && $results1->isNotEmpty()) {
            $scores1 = $results1->pluck('total_motivation')->toArray();
            $desc1   = Descriptive::describe($scores1);

            if ($class2 && $results2->isNotEmpty()) {
                $scores2 = $results2->pluck('total_motivation')->toArray();
                $desc2   = Descriptive::describe($scores2);

                // Perform Tests
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
            'comparison' => [
                'mann_whitney' => $comparison,
                't_test'       => $tTest,
            ],
        ];
    }

    private function buildAnswerMatrix(Collection $results): array
    {
        $matrix = [];
        foreach ($results as $result) {
            $matrix[] = $result->answers()->orderBy('mslq_question_id')->pluck('value')->toArray();
        }

        return array_filter($matrix);
    }
}
