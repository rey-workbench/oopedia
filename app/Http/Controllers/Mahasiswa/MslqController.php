<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Services\MslqServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Survey\StoreMslqRequest;
use App\Models\MslqQuestion;
use App\Models\MslqResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class MslqController extends Controller
{
    public function __construct(
        private readonly MslqServiceInterface $mslqService,
    ) {}

    public function create(): Response|RedirectResponse
    {
        $existing = MslqResult::where('user_id', Auth::id())->first();
        if ($existing) {
            return to_route('mahasiswa.mslq.thankyou');
        }

        $questions = MslqQuestion::orderBy('order')->get();

        return $this->render('Mahasiswa/Mslq/Create/Index', [
            'questions' => $questions,
        ]);
    }

    public function store(StoreMslqRequest $storeMslqRequest): RedirectResponse
    {
        $existing = MslqResult::where('user_id', Auth::id())->first();
        if ($existing) {
            return to_route('mahasiswa.mslq.thankyou');
        }

        try {
            $validated = $storeMslqRequest->validated();
            $answers   = [];
            foreach ($validated['answers'] as $ans) {
                $answers[$ans['question_id']] = $ans['value'];
            }

            $this->mslqService->storeSubmission(
                $answers,
                (int) Auth::id(),
                (string) $validated['nim'],
                (string) $validated['class'],
            );

            return to_route('mahasiswa.mslq.thankyou');
        } catch (\Exception $exception) {
            return back()->with('error', 'Gagal menyimpan data survey: ' . $exception->getMessage());
        }
    }

    public function show(): Response
    {
        return $this->render('Mahasiswa/Mslq/ThankYou/Index');
    }
}
