<?php

declare(strict_types=1);

namespace App\Services\Lms;

use App\Contracts\Repositories\MslqRepositoryInterface;
use App\Contracts\Services\Lms\MslqServiceInterface;
use App\Enums\Lms\MslqCategory;
use App\Models\MslqAnswer;
use App\Models\MslqResult;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class MslqService implements MslqServiceInterface
{
    public function __construct(
        private readonly MslqRepositoryInterface $mslqRepository,
    ) {
    }

    public function getAdminResults(?string $class = null): LengthAwarePaginator
    {
        return $this->mslqRepository->getAll($class);
    }

    public function getDistinctClasses(): Collection
    {
        return $this->mslqRepository->getDistinctClasses();
    }

    public function calculateGlobalAverages(?string $class = null): array
    {
        $results = $this->mslqRepository->getAllForCalculation($class);

        if ($results->isEmpty()) {
            return [];
        }

        $allScales = [];
        foreach ($results as $res) {
            foreach ($res->scores_by_scale as $scale => $score) {
                $allScales[$scale][] = $score;
            }
        }

        $averages = [];
        foreach ($allScales as $scale => $scores) {
            $averages[$scale] = round(array_sum($scores) / count($scores), 2);
        }

        return $averages;
    }

    public function getResultDetail(string $id): MslqResult
    {
        return $this->mslqRepository->findWithRelations($id);
    }

    public function storeSubmission(array $data, int|string $userId, string $nim, string $class): MslqResult
    {
        return DB::transaction(function () use ($data, $userId, $nim, $class) {
            $result = $this->mslqRepository->create([
                'user_id'          => $userId,
                'nim'              => $nim,
                'class'            => $class,
                'scores_by_scale'  => [],
                'total_motivation' => 0,
                'total_strategy'   => 0,
            ]);

            foreach ($data as $questionId => $value) {
                MslqAnswer::create([
                    'mslq_result_id'   => $result->getKey(),
                    'mslq_question_id' => $questionId,
                    'value'            => $value,
                ]);
            }

            $finalScores = $this->calculateScores($result);
            $result->update($finalScores);

            return $result;
        });
    }

    public function getResultsForExport(?string $class = null): Collection
    {
        return $this->mslqRepository->getAllForCalculation($class);
    }

    private function calculateScores(MslqResult $result): array
    {
        $answers = $result->answers()->with('question')->get();
        $grouped = $answers->groupBy(fn (MslqAnswer $answer) => $answer->question->scale);

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

        $total = $answers->sum(function (MslqAnswer $answer) {
            $value = $answer->value;
            if ($answer->question->is_reverse) {
                return 8 - $value;
            }

            return $value;
        });

        return $total / $answers->count();
    }
}
