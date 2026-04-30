<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Contracts\Repositories\SusResultRepositoryInterface;
use App\Contracts\Services\SusResultServiceInterface;
use App\Models\SusResult;
use Illuminate\Database\Eloquent\Collection;

final readonly class SusResultService implements SusResultServiceInterface
{
    public function __construct(
        private SusResultRepositoryInterface $susResultRepository,
    ) {}

    /** @return Collection<int, SusResult> */
    public function getAllResults(?string $class = null): Collection
    {
        return $this->susResultRepository->getAllWithUser($class);
    }

    /** @return array<string> */
    public function getDistinctClasses(): array
    {
        return $this->susResultRepository->getDistinctClasses();
    }

    public function getStudentDetail(string $userId): ?SusResult
    {
        return $this->susResultRepository->findByUserId($userId);
    }

    public function hasUserSubmitted(string $userId): bool
    {
        return $this->susResultRepository->findByUserId($userId) instanceof SusResult;
    }

    public function submitResult(array $data): SusResult
    {
        $data['total_score'] = $this->calculateScore($data);

        return $this->susResultRepository->create($data);
    }

    public function calculateItemScores(SusResult|array $result): array
    {
        $data   = $result instanceof SusResult ? $result->toArray() : $result;
        $scores = [];

        // Odd items: 1, 3, 5, 7, 9 -> (X - 1)
        foreach ([1, 3, 5, 7, 9] as $i) {
            $scores['q' . $i] = (int) $data['q' . $i] - 1;
        }

        // Even items: 2, 4, 6, 8, 10 -> (5 - X)
        foreach ([2, 4, 6, 8, 10] as $i) {
            $scores['q' . $i] = 5 - (int) $data['q' . $i];
        }

        return $scores;
    }

    /** @param array<string, mixed> $data */
    private function calculateScore(array $data): float
    {
        $itemScores = $this->calculateItemScores($data);

        return array_sum($itemScores) * 2.5;
    }

    /** @return array<string, mixed> */
    public function calculateGlobalMetrics(Collection $results): array
    {
        if ($results->isEmpty()) {
            return [
                'average_score'   => 0,
                'total_responses' => 0,
                'grade'           => 'N/A',
                'adjective'       => 'N/A',
            ];
        }

        $totalScore = $results->sum('total_score');
        $average    = $totalScore / $results->count();

        return [
            'average_score'   => round($average, 2),
            'total_responses' => $results->count(),
            'grade'           => $this->getGradeForScore($average),
            'adjective'       => $this->getAdjectiveForScore($average),
            'acceptability'   => $this->getAcceptabilityForScore($average),
            'items'           => $this->calculateAveragePerItem($results),
        ];
    }

    private function calculateAveragePerItem(Collection $results): array
    {
        $averages = [];
        for ($i = 1; $i <= 10; $i++) {
            $averages['q' . $i] = round($results->avg('q' . $i), 2);
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
}
