<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Contracts\Services\QuestionServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Question\StoreQuestionRequest;
use App\Http\Requests\Question\UpdateQuestionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuestionController extends Controller
{
    public function __construct(
        protected QuestionServiceInterface $questionService,
        protected MaterialServiceInterface $materialService,
        protected MaterialRepositoryInterface $materialRepo,
    ) {}

    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $difficulty = $request->input('difficulty');
        $materialId = $request->input('material');

        $material = $materialId ? $this->materialRepo->find($materialId) : null;
        $questions = $this->questionService->getFilteredQuestions($search, $difficulty, $materialId);

        return Inertia::render('Admin/Questions/Index', [
            'questions' => $questions,
            'material' => $material,
            'search' => $search,
            'difficulty' => $difficulty,
        ]);
    }

    public function create(Request $request): Response|RedirectResponse
    {
        $materialId = $request->input('material');
        $material = null;
        $subMaterials = collect();

        if ($materialId) {
            $material = $this->materialRepo->find($materialId);

            if (! $material) {
                return redirect()->route('admin.questions.index')
                    ->with('error', 'Material tidak ditemukan');
            }

            $materials = collect([$material]);
            $subMaterials = $material->subMaterials()->orderBy('order')->get();
        } else {
            $materials = $this->materialService->getAllMaterials();
        }

        return Inertia::render('Admin/Questions/Create/Index', compact('materials', 'material', 'subMaterials'));
    }

    public function store(StoreQuestionRequest $request): RedirectResponse
    {
        $answers = $request->input('answers', []);

        if (in_array($request->question_type, ['radio_button', 'fill_in_the_blank'])) {
            if ($request->has('correct_answer')) {
                $correctIndex = $request->correct_answer;
                $answers = array_map(function ($answer, $index) use ($correctIndex) {
                    $answer['is_correct'] = ($index == $correctIndex) ? 1 : 0;

                    return $answer;
                }, $answers, array_keys($answers));

                $correctCount = collect($answers)->sum('is_correct');

                if ($correctCount !== 1) {
                    return redirect()->back()->withInput()
                        ->with('error', ucfirst(str_replace('_', ' ', $request->question_type)) . ' questions must have exactly one correct answer.');
                }
            } else {
                return redirect()->back()->withInput()
                    ->with('error', 'Please select the correct answer.');
            }
        }

        $data = $request->only(['question_text', 'question_type', 'difficulty', 'material_id', 'sub_material_id']);
        $data['answers'] = $answers;

        $this->questionService->createQuestion($data);

        $redirectParams = $request->material_id ? ['material' => $request->material_id] : [];

        return redirect()->route('admin.questions.index', $redirectParams)
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(int|string $questionId): Response|RedirectResponse
    {
        $question = $this->questionService->getQuestionWithAnswers((int) $questionId);

        if (! $question) {
            return redirect()->route('admin.questions.index')
                ->with('error', 'Soal tidak ditemukan');
        }

        $materials = $this->materialService->getAllMaterials();
        $material = $this->materialRepo->find($question->material_id);
        $subMaterials = $material ? $material->subMaterials()->orderBy('order')->get() : collect();

        return Inertia::render('Admin/Questions/Edit/Index', compact('question', 'materials', 'material', 'subMaterials'));
    }

    public function update(UpdateQuestionRequest $request, int|string $questionId): RedirectResponse
    {
        $questionType = $request->question_type;

        if (in_array($questionType, ['radio_button', 'fill_in_the_blank'])) {
            $correctCount = collect($request->answers)->where('is_correct', '1')->count();

            if ($correctCount !== 1) {
                return back()->withInput()
                    ->with('error', ucfirst(str_replace('_', ' ', $questionType)) . ' Pertanyaan hanya boleh memiliki 1 jawaban benar.');
            }
        }

        $data = $request->only(['question_text', 'question_type', 'difficulty', 'material_id', 'sub_material_id']);
        $data['answers'] = $request->input('answers');

        $this->questionService->updateQuestion((int) $questionId, $data);

        $redirectParams = $request->material_id ? ['material' => $request->material_id] : [];

        return redirect()->route('admin.questions.index', $redirectParams)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(int|string $questionId): RedirectResponse
    {
        $question = $this->questionService->getQuestionById((int) $questionId);
        $materialId = $question?->material_id;

        $this->questionService->deleteQuestion((int) $questionId);

        $redirectParams = $materialId ? ['material' => $materialId] : [];

        return redirect()->route('admin.questions.index', $redirectParams)
            ->with('success', 'Soal berhasil dihapus.');
    }
}
