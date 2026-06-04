<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Services\SusResultServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\DTOs\Survey\SusResultCreateDTO;
use App\Enums\Lms\AssessmentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Survey\StoreSusResultRequest;
use App\Http\Resources\SusQuestionResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class SusSurveyController extends Controller
{
    public function __construct(
        private readonly SusResultServiceInterface $susResultService,
        private readonly UserServiceInterface $userService,
    ) {}

    public function create(Request $request): Response|RedirectResponse
    {
        if ($this->susResultService->hasUserSubmitted((string) Auth::id(), AssessmentType::POST_TEST)) {
            return to_route('mahasiswa.surveys.sus.thankyou');
        }

        $questions = $this->susResultService->getOrderedQuestions();

        return $this->render('Mahasiswa/Sus/Create/Index', [
            'questions' => SusQuestionResource::collection($questions)->resolve(),
        ]);
    }

    public function store(StoreSusResultRequest $storeSusResultRequest): RedirectResponse
    {
        if ($this->susResultService->hasUserSubmitted((string) Auth::id(), AssessmentType::POST_TEST)) {
            return to_route('mahasiswa.surveys.sus.thankyou');
        }

        $susResultCreateDTO = SusResultCreateDTO::fromRequest($storeSusResultRequest, (string) Auth::id());

        if ($storeSusResultRequest->filled('nim') || $storeSusResultRequest->filled('class')) {
            $this->userService->updateProfile((string) Auth::id(), [
                'nim'   => $storeSusResultRequest->input('nim'),
                'class' => $storeSusResultRequest->input('class'),
            ]);
        }

        $this->susResultService->submitResult($susResultCreateDTO->toArray());

        return to_route('mahasiswa.surveys.sus.thankyou');
    }

    public function show(): Response
    {
        return $this->render('Mahasiswa/Sus/ThankYou/Index');
    }
}
