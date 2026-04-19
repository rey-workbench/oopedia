<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Contracts\Repositories\UeqSurveyRepositoryInterface;
use App\Contracts\Services\UeqSurveyServiceInterface;
use App\Models\UeqSurvey;
use Illuminate\Database\Eloquent\Collection;

final class UeqSurveyService implements UeqSurveyServiceInterface
{
    public function __construct(
        public readonly UeqSurveyRepositoryInterface $ueqRepo,
    ) {}

    /** @return Collection<int, UeqSurvey> */
    public function getAllSurveys(?string $class = null): Collection
    {
        return $this->ueqRepo->getAllWithUser($class);
    }

    /** @return array<string> */
    public function getDistinctClasses(): array
    {
        return $this->ueqRepo->getDistinctClasses();
    }

    public function getStudentDetail(string $userId): ?UeqSurvey
    {
        return $this->ueqRepo->findByUserId($userId);
    }

    public function hasUserSubmitted(string $userId): bool
    {
        return $this->ueqRepo->findSurveyByUser($userId) !== null;
    }

    public function createSurvey(array $data): UeqSurvey
    {
        return $this->ueqRepo->create($data);
    }

    /** @return array<string, float> */
    public function calculateAverages(Collection $surveys): array
    {
        if ($surveys->isEmpty()) {
            return [];
        }

        $totals = [
            'attractiveness' => 0,
            'perspicuity'    => 0,
            'efficiency'     => 0,
            'dependability'  => 0,
            'stimulation'    => 0,
            'novelty'        => 0,
        ];

        foreach ($surveys as $survey) {
            $totals['attractiveness'] += (
                $survey->annoying_enjoyable      +
                $survey->good_bad                +
                $survey->unlikable_pleasing      +
                $survey->unpleasant_pleasant     +
                $survey->attractive_unattractive +
                $survey->friendly_unfriendly
            ) / 6;

            $totals['perspicuity'] += (
                $survey->not_understandable_understandable +
                $survey->easy_difficult                    +
                $survey->complicated_easy                  +
                $survey->clear_confusing
            ) / 4;

            $totals['efficiency'] += (
                $survey->fast_slow             +
                $survey->inefficient_efficient +
                $survey->impractical_practical +
                $survey->organized_cluttered
            ) / 4;

            $totals['dependability'] += (
                $survey->unpredictable_predictable +
                $survey->obstructive_supportive    +
                $survey->secure_not_secure         +
                $survey->meets_expectations_does_not_meet
            ) / 4;

            $totals['stimulation'] += (
                $survey->valuable_inferior           +
                $survey->boring_exciting             +
                $survey->not_interesting_interesting +
                $survey->motivating_demotivating
            ) / 4;

            $totals['novelty'] += (
                $survey->creative_dull          +
                $survey->inventive_conventional +
                $survey->usual_leading_edge     +
                $survey->conservative_innovative
            ) / 4;
        }

        $count    = $surveys->count();
        $averages = [];

        foreach ($totals as $key => $total) {
            $averages[$key] = $total / $count;
        }

        return $averages;
    }
}
