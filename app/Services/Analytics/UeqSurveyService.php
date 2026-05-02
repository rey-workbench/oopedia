<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Contracts\Repositories\UeqSurveyRepositoryInterface;
use App\Contracts\Services\UeqSurveyServiceInterface;
use App\Http\Resources\UeqSurveyResource;
use App\Models\UeqSurvey;
use Illuminate\Support\Collection as SupportCollection;

final readonly class UeqSurveyService implements UeqSurveyServiceInterface
{
    public function __construct(
        public UeqSurveyRepositoryInterface $ueqRepo,
    ) {}

    public function getAllSurveys(?string $class = null): SupportCollection
    {
        return collect(UeqSurveyResource::collection(
            $this->ueqRepo->getAllWithUser($class),
        )->resolve());
    }

    /** @return array<string> */
    public function getDistinctClasses(): array
    {
        return $this->ueqRepo->getDistinctClasses();
    }

    /** @return array<string, mixed>|null */
    public function getStudentDetail(string $userId): ?array
    {
        $survey = $this->ueqRepo->findByUserId($userId);

        if (! $survey instanceof UeqSurvey) {
            return null;
        }

        return new UeqSurveyResource($survey)->resolve();
    }

    public function hasUserSubmitted(string $userId): bool
    {
        return $this->ueqRepo->findSurveyByUser($userId) instanceof UeqSurvey;
    }

    public function createSurvey(array $data): UeqSurvey
    {
        return $this->ueqRepo->create($data);
    }

    /** @return array<string, float> */
    public function calculateAverages(SupportCollection $surveys): array
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
                $survey['annoying_enjoyable']      +
                $survey['good_bad']                +
                $survey['unlikable_pleasing']      +
                $survey['unpleasant_pleasant']     +
                $survey['attractive_unattractive'] +
                $survey['friendly_unfriendly']
            ) / 6;

            $totals['perspicuity'] += (
                $survey['not_understandable_understandable'] +
                $survey['easy_difficult']                    +
                $survey['complicated_easy']                  +
                $survey['clear_confusing']
            ) / 4;

            $totals['efficiency'] += (
                $survey['fast_slow']             +
                $survey['inefficient_efficient'] +
                $survey['impractical_practical'] +
                $survey['organized_cluttered']
            ) / 4;

            $totals['dependability'] += (
                $survey['unpredictable_predictable'] +
                $survey['obstructive_supportive']    +
                $survey['secure_not_secure']         +
                $survey['meets_expectations_does_not_meet']
            ) / 4;

            $totals['stimulation'] += (
                $survey['valuable_inferior']           +
                $survey['boring_exciting']             +
                $survey['not_interesting_interesting'] +
                $survey['motivating_demotivating']
            ) / 4;

            $totals['novelty'] += (
                $survey['creative_dull']          +
                $survey['inventive_conventional'] +
                $survey['usual_leading_edge']     +
                $survey['conservative_innovative']
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
