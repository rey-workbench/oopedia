<?php

declare(strict_types=1);

namespace App\Http\Controllers\Mahasiswa;

use App\Contracts\Services\Lms\MslqServiceInterface;
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
        protected MslqServiceInterface $mslqService,
    ) {}

    public function create(): Response|RedirectResponse
    {
        // Check if user already submitted
        $existing = MslqResult::where('user_id', Auth::id())->first();
        if ($existing) {
            return redirect()->route('mahasiswa.mslq.thankyou');
        }

        $questions = MslqQuestion::orderBy('order')->get();

        return $this->render('Mahasiswa/Mslq/Create/Index', [
            'questions' => $questions,
        ]);
    }

    public function store(StoreMslqRequest $request): RedirectResponse
    {
        $existing = MslqResult::where('user_id', Auth::id())->first();
        if ($existing) {
            return redirect()->route('mahasiswa.mslq.thankyou');
        }

        try {
            // Reformating answers for service
            $answers = [];
            foreach ($request->answers as $ans) {
                $answers[$ans['question_id']] = $ans['value'];
            }

            $this->mslqService->storeSubmission(
                $answers,
                (int) Auth::id(),
                $request->nim,
                $request->class,
            );

            return redirect()->route('mahasiswa.mslq.thankyou');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan data survey: ' . $e->getMessage());
        }
    }

    public function show(): Response
    {
        return $this->render('Mahasiswa/Mslq/ThankYou/Index');
    }
}
