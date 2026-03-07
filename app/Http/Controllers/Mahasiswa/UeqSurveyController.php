<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Services\UeqSurveyServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Survey\StoreUeqSurveyRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class UeqSurveyController extends Controller
{
    public function __construct(protected
        UeqSurveyServiceInterface $ueqService,
        )
    {
    }

    public function create(): Response|RedirectResponse
    {
        if ($this->ueqService->hasUserSubmitted(Auth::id())) {
            return redirect()->route('mahasiswa.ueq-survey.thankyou');
        }

        $aspects = $this->getAspects();

        return Inertia::render('Mahasiswa/Ueq/Create/Index', compact('aspects'));
    }

    public function store(StoreUeqSurveyRequest $request): RedirectResponse
    {
        if ($this->ueqService->hasUserSubmitted(Auth::id())) {
            return redirect()->route('mahasiswa.ueq-survey.thankyou');
        }

        $data = array_merge($request->validated(), ['user_id' => Auth::id()]);

        $this->ueqService->createSurvey($data);

        return redirect()->route('mahasiswa.ueq-survey.thankyou');
    }

    public function show(): Response
    {
        return Inertia::render('Mahasiswa/Ueq/ThankYou/Index');
    }

    /** @return array<string, array<string, string>> */
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
