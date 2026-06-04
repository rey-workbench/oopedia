<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Services\MslqServiceInterface;
use App\Contracts\Services\UserServiceInterface;
use App\Enums\Lms\AssessmentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Survey\StoreMslqRequest;
use App\Http\Resources\MslqQuestionResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class MslqController extends Controller
{
    public function __construct(
        private readonly MslqServiceInterface $mslqService,
        private readonly UserServiceInterface $userService,
    ) {}

    public function create(Request $request): Response|RedirectResponse
    {
        $type = $request->query('type', 'post');

        if ($this->mslqService->hasExistingResult(Auth::id(), (string) $type)) {
            return to_route('mahasiswa.surveys.mslq.thankyou');
        }

        $questions = $this->mslqService->getOrderedQuestions();

        return $this->render('Mahasiswa/Mslq/Create/Index', [
            'questions' => MslqQuestionResource::collection($questions)->resolve(),
            'type'      => $type,
        ]);
    }

    public function store(StoreMslqRequest $storeMslqRequest): RedirectResponse
    {
        $validated = $storeMslqRequest->validated();

        if ($this->mslqService->hasExistingResult(Auth::id(), $validated['assessment_type'])) {
            return to_route('mahasiswa.surveys.mslq.thankyou');
        }

        try {
            if (! empty($validated['nim']) || ! empty($validated['class'])) {
                $this->userService->updateProfile(Auth::id(), [
                    'nim'   => $validated['nim']   ?? null,
                    'class' => $validated['class'] ?? null,
                ]);
            }

            $answers = [];
            foreach ($validated['answers'] as $ans) {
                $answers[$ans['question_id']] = $ans['value'];
            }

            $this->mslqService->storeSubmission(
                $answers,
                Auth::id(),
                AssessmentType::from($validated['assessment_type']),
            );

            return to_route('mahasiswa.surveys.mslq.thankyou');
        } catch (\Exception $exception) {
            report($exception);

            return back()->with('error', 'Gagal menyimpan data survey. Silakan coba lagi.');
        }
    }

    public function show(): Response
    {
        return $this->render('Mahasiswa/Mslq/ThankYou/Index');
    }
}
