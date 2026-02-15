<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contracts\Services\QuestionServiceInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Contracts\Repositories\MaterialRepositoryInterface;
use Inertia\Inertia;

class QuestionController extends Controller
{
    public function __construct(protected
        QuestionServiceInterface $questionService, protected
        MaterialServiceInterface $materialService, protected
        MaterialRepositoryInterface $materialRepo
        )
    {
    }

    /**
     * Display a listing of questions.
     * Optional: filter by material via query param ?material=1
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $difficulty = $request->input('difficulty');
        $materialId = $request->input('material'); // Filter by material via query param

        $material = null;
        if ($materialId) {
            $material = $this->materialRepo->find($materialId);
        }

        $questions = $this->questionService->getFilteredQuestions($search, $difficulty, $materialId);

        return Inertia::render('Admin/Questions/Index', [
            'questions' => $questions,
            'material' => $material,
            'search' => $search,
            'difficulty' => $difficulty
        ]);
    }
    /**
     * Show the form for creating a new question.
     * Optional: pre-select material via query param ?material=1
     */
    public function create(Request $request)
    {
        $materialId = $request->input('material'); // Pre-select material via query param
        $material = null;
        $subMaterials = collect();

        if ($materialId) {
            $material = $this->materialRepo->find($materialId);
            if ($material) {
                $materials = collect([$material]);
                $subMaterials = $material->subMaterials()->orderBy('order')->get();
            }
            else {
                return redirect()->route('admin.questions.index')
                    ->with('error', 'Material tidak ditemukan');
            }
        }
        else {
            $materials = $this->materialService->getAllMaterials();
        }

        return Inertia::render('Admin/Questions/Create/Index', compact('materials', 'material', 'subMaterials'));
    }

    /**
     * Store a newly created question in storage.
     */
    public function store(Request $request)
    {
        $baseValidation = [
            'question_text' => 'required|string',
            'question_type' => 'required|in:radio_button,drag_and_drop,fill_in_the_blank',
            'difficulty' => 'required|in:beginner,medium,hard',
            'material_id' => 'required|exists:materials,id',
            'sub_material_id' => 'nullable|exists:sub_materials,id',
        ];

        if ($request->question_type === 'fill_in_the_blank') {
            $answersValidation = ['answers' => 'required|array|min:1'];
        }
        else {
            $answersValidation = ['answers' => 'required|array|min:2'];
        }

        $validationRules = array_merge($baseValidation, $answersValidation);

        $validationRules['answers.*.answer_text'] = 'required|string';

        $request->validate($validationRules);

        // Proses correct_answer untuk radio_button dan fill_in_the_blank
        $answers = $request->answers;

        if (in_array($request->question_type, ['radio_button', 'fill_in_the_blank'])) {
            if ($request->has('correct_answer')) {
                $correctIndex = $request->correct_answer;

                $processedAnswers = [];
                foreach ($answers as $index => $answer) {
                    $answer['is_correct'] = ($index == $correctIndex) ? 1 : 0;
                    $processedAnswers[] = $answer;
                }
                $answers = $processedAnswers;

                $correctAnswersCount = collect($answers)->sum(function ($answer) {
                    return $answer['is_correct'];
                });

                if ($correctAnswersCount !== 1) {
                    return redirect()->back()->withInput()->with('error', ucfirst(str_replace('_', ' ', $request->question_type)) . ' questions must have exactly one correct answer.');
                }
            }
            else {
                return redirect()->back()->withInput()->with('error', 'Please select the correct answer.');
            }
        }

        $data = $request->only(['question_text', 'question_type', 'difficulty', 'material_id', 'sub_material_id']);
        $data['answers'] = $answers;

        try {
            $this->questionService->createQuestion($data);

            // Redirect back to questions index, preserving material filter if it exists
            $redirectParams = $request->material_id ? ['material' => $request->material_id] : [];
            return redirect()->route('admin.questions.index', $redirectParams)
                ->with('success', 'Soal berhasil ditambahkan.');
        }
        catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(int $questionId)
    {
        $question = $this->questionService->getQuestionWithAnswers($questionId);

        if (!$question) {
            return redirect()->route('admin.questions.index')
                ->with('error', 'Soal tidak ditemukan');
        }

        $materials = $this->materialService->getAllMaterials();
        $material = $this->materialRepo->find($question->material_id);
        $subMaterials = $material ? $material->subMaterials()->orderBy('order')->get() : collect();

        return Inertia::render('Admin/Questions/Edit/Index', compact('question', 'materials', 'material', 'subMaterials'));
    }

    /**
     * Update the specified question in storage.
     */
    public function update(Request $request, int $questionId)
    {
        $baseValidation = [
            'question_text' => 'required|string',
            'question_type' => 'required|in:radio_button,drag_and_drop,fill_in_the_blank',
            'difficulty' => 'required|in:beginner,medium,hard',
            'material_id' => 'required|exists:materials,id',
            'sub_material_id' => 'nullable|exists:sub_materials,id',
        ];

        if ($request->question_type === 'fill_in_the_blank') {
            $answersValidation = ['answers' => 'required|array|min:1'];
        }
        else {
            $answersValidation = ['answers' => 'required|array|min:2'];
        }

        $validationRules = array_merge($baseValidation, $answersValidation);
        $validationRules['answers.*.answer_text'] = 'required|string';
        $validationRules['answers.*.is_correct'] = 'required|boolean';
        $validationRules['answers.*.explanation'] = 'nullable|string|max:500';

        $request->validate($validationRules);

        $questionType = $request->question_type;

        if (in_array($questionType, ['radio_button', 'fill_in_the_blank'])) {
            $correctAnswersCount = collect($request->answers)->where('is_correct', '1')->count();
            if ($correctAnswersCount !== 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => ucfirst(str_replace('_', ' ', $questionType)) . ' Pertanyaan hanya boleh memliki 1 jawaban.'
                ], 422);
            }
        }

        $data = $request->only(['question_text', 'question_type', 'difficulty', 'material_id', 'sub_material_id']);
        $data['answers'] = $request->answers;

        try {
            $this->questionService->updateQuestion($questionId, $data);

            // Redirect back to questions index, preserving material filter if it exists  
            $redirectParams = $request->material_id ? ['material' => $request->material_id] : [];
            return redirect()->route('admin.questions.index', $redirectParams)
                ->with('success', 'Soal berhasil diperbarui.');
        }
        catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroy(int $questionId)
    {
        try {
            $question = $this->questionService->getQuestionById($questionId);
            $materialId = $question?->material_id;
            
            $this->questionService->deleteQuestion($questionId);

            // Redirect back to questions index, preserving material filter if it exists
            $redirectParams = $materialId ? ['material' => $materialId] : [];
            return redirect()->route('admin.questions.index', $redirectParams)
                ->with('success', 'Soal berhasil dihapus.');
        }
        catch (\Exception $e) {
            return redirect()->route('admin.questions.index')
                ->with('error', 'Gagal menghapus soal: ' . $e->getMessage());
        }
    }
}