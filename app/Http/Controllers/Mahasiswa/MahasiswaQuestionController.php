<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Services\QuestionAnswerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MahasiswaQuestionController extends Controller
{
    protected $questionAnswerService;

    public function __construct(QuestionAnswerService $questionAnswerService)
    {
        $this->questionAnswerService = $questionAnswerService;
    }

    public function checkAnswer(Request $request)
    {
        Log::info('Request data for checkAnswer:', $request->all());

        try {
            $request->validate([
                'question_id' => 'required|exists:questions,id',
                'material_id' => 'required|exists:materials,id'
            ]);

            $userId = auth()->id();
            $isGuest = !auth()->check() || (auth()->check() && auth()->user()->role_id === 4);

            $result = $this->questionAnswerService->checkAnswer(
                $request->all(),
                $userId,
                $isGuest
            );

            if (isset($result['http_code'])) {
                return response()->json($result, $result['http_code']);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error in checkAnswer: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkAllAnswers(Request $request)
    {
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'answers' => 'required|array'
        ]);

        $result = $this->questionAnswerService->checkAllAnswers(
            $request->all(),
            auth()->id()
        );

        if ($request->ajax()) {
            return response()->json($result);
        }

        return redirect()->route('mahasiswa.dashboard')
            ->with('success', $result['message']);
    }

    public function submitQuiz(Request $request)
    {
        $request->validate([
            'material_id' => 'required|exists:materials,id',
            'answers' => 'required|array'
        ]);

        $result = $this->questionAnswerService->checkAllAnswers(
            $request->all(),
            auth()->id()
        );

        return response()->json($result);
    }
}