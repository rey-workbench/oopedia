<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Services\UeqSurveyServiceInterface;
use App\DTOs\Survey\UeqSurveyCreateDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Survey\StoreUeqSurveyRequest;
use App\Http\Resources\UeqAspectResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class UeqSurveyController extends Controller
{
    public function __construct(
        private readonly UeqSurveyServiceInterface $ueqSurveyService,
    ) {}

    public function create(): Response|RedirectResponse
    {
        if ($this->ueqSurveyService->hasUserSubmitted(Auth::id())) {
            return to_route('mahasiswa.ueq-survey.thankyou');
        }

        $aspects = $this->getAspects();

        return $this->render('Mahasiswa/Ueq/Create/Index', [
            'aspects' => UeqAspectResource::collection(collect($aspects)->map(fn ($a): \stdClass => (object) $a))->resolve(),
        ]);
    }

    public function store(StoreUeqSurveyRequest $storeUeqSurveyRequest): RedirectResponse
    {
        if ($this->ueqSurveyService->hasUserSubmitted(Auth::id())) {
            return to_route('mahasiswa.ueq-survey.thankyou');
        }

        $ueqSurveyCreateDTO = UeqSurveyCreateDTO::fromRequest($storeUeqSurveyRequest, (string) Auth::id());

        $this->ueqSurveyService->createSurvey($ueqSurveyCreateDTO->toArray());

        return to_route('mahasiswa.ueq-survey.thankyou');
    }

    public function show(): Response
    {
        return $this->render('Mahasiswa/Ueq/ThankYou/Index');
    }

    /** @return array<int, array<string, string>> */
    private function getAspects(): array
    {
        return [
            ['name' => 'annoying_enjoyable'],
            ['name' => 'not_understandable_understandable'],
            ['name' => 'creative_dull'],
            ['name' => 'easy_difficult'],
            ['name' => 'valuable_inferior'],
            ['name' => 'boring_exciting'],
            ['name' => 'not_interesting_interesting'],
            ['name' => 'unpredictable_predictable'],
            ['name' => 'fast_slow'],
            ['name' => 'inventive_conventional'],
            ['name' => 'obstructive_supportive'],
            ['name' => 'good_bad'],
            ['name' => 'complicated_easy'],
            ['name' => 'unlikable_pleasing'],
            ['name' => 'usual_leading_edge'],
            ['name' => 'unpleasant_pleasant'],
            ['name' => 'secure_not_secure'],
            ['name' => 'motivating_demotivating'],
            ['name' => 'meets_expectations_does_not_meet'],
            ['name' => 'inefficient_efficient'],
            ['name' => 'clear_confusing'],
            ['name' => 'impractical_practical'],
            ['name' => 'organized_cluttered'],
            ['name' => 'attractive_unattractive'],
            ['name' => 'friendly_unfriendly'],
            ['name' => 'conservative_innovative'],
        ];
    }
}
