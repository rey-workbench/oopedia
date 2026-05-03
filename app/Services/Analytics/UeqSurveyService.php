<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Contracts\Repositories\UeqSurveyRepositoryInterface;
use App\Contracts\Services\UeqSurveyServiceInterface;
use App\Http\Resources\UeqSurveyResource;
use App\Models\UeqSurvey;
use Illuminate\Support\Collection as SupportCollection;
use MathPHP\Statistics\Descriptive;

final readonly class UeqSurveyService implements UeqSurveyServiceInterface
{
    public function __construct(
        public UeqSurveyRepositoryInterface $ueqRepo,
        private StatisticalAnalysisService $statisticalAnalysisService,
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

    public function calculateStatisticalAnalysis(?string $class1 = null, ?string $class2 = null): array
    {
        $results1 = $this->ueqRepo->getAllWithUser($class1);
        $results2 = $class2 ? $this->ueqRepo->getAllWithUser($class2) : collect();

        // 1. Cronbach's Alpha (Reliability)
        $matrix = $this->buildAnswerMatrix($results1->merge($results2));
        $alpha  = $this->statisticalAnalysisService->cronbachAlpha($matrix);

        // 2. Mann-Whitney U & T-Test
        $comparison = null;
        $tTest      = null;
        $desc1      = null;
        $desc2      = null;

        if ($class1 && $results1->isNotEmpty()) {
            // Use attractiveness as primary metric for overall comparison
            $scores1 = $results1->map(fn ($s) => (
                $s->annoying_enjoyable  + $s->good_bad + $s->unlikable_pleasing +
                $s->unpleasant_pleasant + $s->attractive_unattractive + $s->friendly_unfriendly
            ) / 6)->toArray();

            $desc1 = Descriptive::describe($scores1);

            if ($class2 && $results2->isNotEmpty()) {
                $scores2 = $results2->map(fn ($s) => (
                    $s->annoying_enjoyable  + $s->good_bad + $s->unlikable_pleasing +
                    $s->unpleasant_pleasant + $s->attractive_unattractive + $s->friendly_unfriendly
                ) / 6)->toArray();

                $desc2 = Descriptive::describe($scores2);

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

    private function buildAnswerMatrix(SupportCollection $results): array
    {
        $matrix = [];
        foreach ($results as $result) {
            // UEQ has 26 items (or 32 depending on version, here it seems to be 26/32)
            // We collect all numerical scores
            $matrix[] = [
                $result->annoying_enjoyable,
                $result->not_understandable_understandable,
                $result->creative_dull,
                $result->easy_difficult,
                $result->valuable_inferior,
                $result->boring_exciting,
                $result->not_interesting_interesting,
                $result->unpredictable_predictable,
                $result->fast_slow,
                $result->inventive_conventional,
                $result->obstructive_supportive,
                $result->good_bad,
                $result->complicated_easy,
                $result->unlikable_pleasing,
                $result->usual_leading_edge,
                $result->unpleasant_pleasant,
                $result->secure_not_secure,
                $result->motivating_demotivating,
                $result->meets_expectations_does_not_meet,
                $result->inefficient_efficient,
                $result->clear_confusing,
                $result->impractical_practical,
                $result->organized_cluttered,
                $result->attractive_unattractive,
                $result->friendly_unfriendly,
                $result->conservative_innovative,
            ];
        }

        return array_filter($matrix);
    }
}
