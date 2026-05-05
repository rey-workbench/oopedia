<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Services\MslqServiceInterface;
use App\Enums\Lms\AssessmentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Survey\StoreMslqRequest;
use App\Http\Resources\MslqQuestionResource;
use App\Models\MslqQuestion;
use App\Models\MslqResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class MslqController extends Controller
{
    public function __construct(
        private readonly MslqServiceInterface $mslqService,
    ) {}

    public function create(Request $request): Response|RedirectResponse
    {
        $type = $request->query('type', 'pre');
        $existing = MslqResult::where('user_id', Auth::id())
            ->where('assessment_type', $type)
            ->first();
            
        if ($existing) {
            return to_route('mahasiswa.surveys.mslq.thankyou');
        }

        $questions = MslqQuestion::orderBy('order')->get();

        return $this->render('Mahasiswa/Mslq/Create/Index', [
            'questions' => MslqQuestionResource::collection($questions)->resolve(),
            'type'      => $type,
        ]);
    }

    public function store(StoreMslqRequest $storeMslqRequest): RedirectResponse
    {
        $validated = $storeMslqRequest->validated();
        $existing = MslqResult::where('user_id', Auth::id())
            ->where('assessment_type', $validated['assessment_type'])
            ->first();
            
        if ($existing) {
            return to_route('mahasiswa.surveys.mslq.thankyou');
        }

        try {
            $validated = $storeMslqRequest->validated();
            
            // Update user profile if nim or class is provided
            if (!empty($validated['nim']) || !empty($validated['class'])) {
                Auth::user()->update([
                    'nim'   => $validated['nim'] ?? Auth::user()->nim,
                    'class' => $validated['class'] ?? Auth::user()->class,
                ]);
            }

            $answers   = [];
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
            return back()->with('error', 'Gagal menyimpan data survey: ' . $exception->getMessage());
        }
    }

    public function show(): Response
    {
        return $this->render('Mahasiswa/Mslq/ThankYou/Index');
    }
}
