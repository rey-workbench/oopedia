<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

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
        private readonly QuestionServiceInterface $questionService,
        private readonly MaterialServiceInterface $materialService,
    ) {}

    public function index(Request $request): Response
    {
        $search     = $request->input('search');
        $difficulty = QuestionDifficulty::tryFrom((string) $request->input('difficulty'));
        $materialId = $request->input('material');

        $material             = $materialId ? $this->materialService->getMaterialById((string) $materialId) : null;
        $lengthAwarePaginator = $this->questionService->getFilteredQuestions($search, $difficulty, (string) $materialId);

        return $this->render('Admin/Questions/Index', [
            'questions'  => $lengthAwarePaginator,
            'material'   => $material,
            'search'     => $search,
            'difficulty' => $difficulty?->value,
        ]);
    }

    public function create(Request $request): Response|RedirectResponse
    {
        $materialId = $request->input('material');
        if (! $materialId) {
            $materials = $this->materialService->getAllMaterials();
            $material  = null;

            return $this->render('Admin/Questions/Create/Index', ['materials' => $materials, 'material' => $material]);
        }

        $material = $this->materialService->getMaterialById((string) $materialId);

        if (! $material) {
            return to_route('admin.questions.index')
                ->with('error', 'Material tidak ditemukan');
        }

        $materials = collect([$material]);

        return $this->render('Admin/Questions/Create/Index', ['materials' => $materials, 'material' => $material]);
    }

    public function store(StoreQuestionRequest $storeQuestionRequest): RedirectResponse
    {
        $questionCreateDTO = QuestionCreateDTO::fromRequest($storeQuestionRequest, Auth::id());

        $correctCount = collect($questionCreateDTO->answers)->where('is_correct', '1')->count();

        if (in_array($questionCreateDTO->question_type, [QuestionType::RADIO_BUTTON->value, QuestionType::FILL_IN_THE_BLANK->value]) && $correctCount !== 1) {
            return back()->withInput()
                ->with(
                    'error',
                    ucfirst(str_replace('_', ' ', $questionCreateDTO->question_type))
                    . ' questions must have exactly one correct answer.',
                );
        }

        $this->questionService->createQuestion($questionCreateDTO);

        $redirectParams = $questionCreateDTO->material_id !== '' && $questionCreateDTO->material_id !== '0' ? ['material' => $questionCreateDTO->material_id] : [];

        return to_route('admin.questions.index', $redirectParams)
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    public function edit(string $questionId): Response|RedirectResponse
    {
        $question = $this->questionService->getQuestionWithAnswers($questionId);

        if (! $question) {
            return to_route('admin.questions.index')
                ->with('error', 'Soal tidak ditemukan');
        }

        $materials = $this->materialService->getAllMaterials();
        $material  = $this->materialService->getMaterialById((string) $question['material_id']);

        return $this->render(
            'Admin/Questions/Edit/Index',
            ['question' => $question, 'materials' => $materials, 'material' => $material],
        );
    }

    public function update(UpdateQuestionRequest $updateQuestionRequest, string $questionId): RedirectResponse
    {
        $questionUpdateDTO = QuestionUpdateDTO::fromRequest($updateQuestionRequest);

        if (in_array($questionUpdateDTO->question_type, [QuestionType::RADIO_BUTTON->value, QuestionType::FILL_IN_THE_BLANK->value])) {
            $correctCount = collect($questionUpdateDTO->answers)->where('is_correct', '1')->count();

            if ($correctCount !== 1) {
                return back()->withInput()
                    ->with(
                        'error',
                        ucfirst(str_replace('_', ' ', $questionUpdateDTO->question_type))
                        . ' Pertanyaan hanya boleh memiliki 1 jawaban benar.',
                    );
            }
        }

        $this->questionService->updateQuestion($questionId, $questionUpdateDTO);

        $redirectParams = $questionUpdateDTO->material_id ? ['material' => $questionUpdateDTO->material_id] : [];

        return to_route('admin.questions.index', $redirectParams)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(string $questionId): RedirectResponse
    {
        $question   = $this->questionService->getQuestionById($questionId);
        $materialId = $question ? $question['material_id'] : null;

        $this->questionService->deleteQuestion($questionId);

        $redirectParams = $materialId ? ['material' => $materialId] : [];

        return to_route('admin.questions.index', $redirectParams)
            ->with('success', 'Soal berhasil dihapus.');
    }
}
