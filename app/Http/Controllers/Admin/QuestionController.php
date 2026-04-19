<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Services\MaterialServiceInterface;
use App\Contracts\Services\QuestionServiceInterface;
use App\DTOs\Question\QuestionCreateDTO;
use App\DTOs\Question\QuestionUpdateDTO;
use App\Enums\Lms\QuestionDifficulty;
use App\Enums\Lms\QuestionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Question\StoreQuestionRequest;
use App\Http\Requests\Question\UpdateQuestionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class QuestionController extends Controller
{
    public function __construct(
        protected QuestionServiceInterface $questionService,
        protected MaterialServiceInterface $materialService,
        protected MaterialRepositoryInterface $materialRepo,
    ) {}

    public function index(Request $request): Response
    {
        $search     = $request->input('search');
        $difficulty = QuestionDifficulty::tryFrom((string) $request->input('difficulty'))?->value;
        $materialId = $request->input('material');

        $material  = $materialId ? $this->materialRepo->find((string) $materialId) : null;
        $questions = $this->questionService->getFilteredQuestions($search, $difficulty, (string) $materialId);

        return $this->render('Admin/Questions/Index', [
            'questions'  => $questions,
            'material'   => $material,
            'search'     => $search,
            'difficulty' => $difficulty,
        ]);
    }

    public function create(Request $request): Response|RedirectResponse
    {
        $materialId = $request->input('material');
        if (! $materialId) {
            $materials    = $this->materialService->getAllMaterials();
            $material     = null;
            $subMaterials = collect();

            return $this->render('Admin/Questions/Create/Index', compact('materials', 'material', 'subMaterials'));
        }

        $material = $this->materialRepo->find($materialId);

        if (! $material) {
            return redirect()->route('admin.questions.index')
                ->with('error', 'Material tidak ditemukan');
        }

        $materials    = collect([$material]);
        $subMaterials = $material->subMaterials()->orderBy('order')->get();

        return $this->render('Admin/Questions/Create/Index', compact('materials', 'material', 'subMaterials'));
    }

    public function store(StoreQuestionRequest $request): RedirectResponse
    {
        $dto = QuestionCreateDTO::fromRequest($request, Auth::id());

        $correctCount = collect($dto->answers)->where('is_correct', '1')->count();

        if (in_array($dto->question_type, [QuestionType::RADIO_BUTTON->value, QuestionType::FILL_IN_THE_BLANK->value]) && $correctCount !== 1) {
            return redirect()->back()->withInput()
                ->with(
                    'error',
                    ucfirst(str_replace('_', ' ', $dto->question_type))
                    . ' questions must have exactly one correct answer.',
                );
        }

        $this->questionService->createQuestion($dto->toArray());

        $redirectParams = $dto->material_id ? ['material' => $dto->material_id] : [];

        return redirect()->route('admin.questions.index', $redirectParams)
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(string $questionId): Response|RedirectResponse
    {
        $question = $this->questionService->getQuestionWithAnswers($questionId);

        if (! $question) {
            return redirect()->route('admin.questions.index')
                ->with('error', 'Soal tidak ditemukan');
        }

        $materials    = $this->materialService->getAllMaterials();
        $material     = $this->materialRepo->find($question->material_id);
        $subMaterials = $material ? $material->subMaterials()->orderBy('order')->get() : collect();

        return $this->render(
            'Admin/Questions/Edit/Index',
            compact('question', 'materials', 'material', 'subMaterials'),
        );
    }

    public function update(UpdateQuestionRequest $request, string $questionId): RedirectResponse
    {
        $dto = QuestionUpdateDTO::fromRequest($request);

        if (in_array($dto->question_type, [QuestionType::RADIO_BUTTON->value, QuestionType::FILL_IN_THE_BLANK->value])) {
            $correctCount = collect($dto->answers)->where('is_correct', '1')->count();

            if ($correctCount !== 1) {
                return back()->withInput()
                    ->with(
                        'error',
                        ucfirst(str_replace('_', ' ', $dto->question_type))
                        . ' Pertanyaan hanya boleh memiliki 1 jawaban benar.',
                    );
            }
        }

        $this->questionService->updateQuestion($questionId, $dto->toArray());

        $redirectParams = $dto->material_id ? ['material' => $dto->material_id] : [];

        return redirect()->route('admin.questions.index', $redirectParams)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(string $questionId): RedirectResponse
    {
        $question   = $this->questionService->getQuestionById($questionId);
        $materialId = $question?->material_id;

        $this->questionService->deleteQuestion($questionId);

        $redirectParams = $materialId ? ['material' => $materialId] : [];

        return redirect()->route('admin.questions.index', $redirectParams)
            ->with('success', 'Soal berhasil dihapus.');
    }
}
