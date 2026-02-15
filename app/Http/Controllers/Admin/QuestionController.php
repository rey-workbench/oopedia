<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Material;
use Illuminate\Http\Request;
use App\Services\Lms\Question\QuestionListingService;
use App\Services\Lms\Question\QuestionService;
use App\Services\Lms\Material\MaterialService;
use Inertia\Inertia;

class QuestionController extends Controller
{

    public function __construct(
        protected QuestionListingService $questionListingService,
        protected QuestionService $questionService,
        protected MaterialService $materialService
    ) {}

    public function index(Request $request, Material $material = null)
    {
        $search = $request->input('search');
        $difficulty = $request->input('difficulty');
        $materialId = $material ? $material->id : null;

        $questions = $this->questionService->getFilteredQuestions($search, $difficulty, $materialId, 10);

        return Inertia::render('Admin/Questions/Index', [
            'questions' => $questions,
            'material' => $material,
            'search' => $search,
            'difficulty' => $difficulty
        ]);
    }


    public function create(Material $material = null)
    {
        if ($material) {
            $materials = collect([$material]);
            $subMaterials = $material->subMaterials()->orderBy('order')->get();
        } else {
            $materials = $this->materialService->getAllMaterials();
            $subMaterials = collect();
        }

        return Inertia::render('Admin/Questions/Create/Index', compact('materials', 'material', 'subMaterials'));
    }

    public function store(Request $request, Material $material = null)
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
        } else {
            $answersValidation = ['answers' => 'required|array|min:2'];
        }
        
        $validationRules = array_merge($baseValidation, $answersValidation);
        
        $validationRules['answers.*.answer_text'] = 'required|string';
        
        $request->validate($validationRules);

        // Proses correct_answer untuk radio_button dan fill_in_the_blank
        // Note: This pre-processing is still done here as it relates to request format adaptation before business logic
        $answers = $request->answers;
        
        if (in_array($request->question_type, ['radio_button', 'fill_in_the_blank'])) {
            if ($request->has('correct_answer')) {
                $correctIndex = $request->correct_answer;
                
                // Create a new array instead of trying to modify by reference
                $processedAnswers = [];
                foreach ($answers as $index => $answer) {
                    $answer['is_correct'] = ($index == $correctIndex) ? 1 : 0;
                    $processedAnswers[] = $answer;
                }
                $answers = $processedAnswers;
                
                // Pastikan hanya ada 1 jawaban benar
                $correctAnswersCount = collect($answers)->sum(function ($answer) {
                    return $answer['is_correct'];
                });
                
                if ($correctAnswersCount !== 1) {
                    return redirect()->back()->withInput()->with('error', ucfirst(str_replace('_', ' ', $request->question_type)) . ' questions must have exactly one correct answer.');
                }
            } else {
                return redirect()->back()->withInput()->with('error', 'Please select the correct answer.');
            }
        }

        // Prepare data for service
        $data = $request->only(['question_text', 'question_type', 'difficulty', 'material_id', 'sub_material_id']);
        $data['answers'] = $answers;

        try {
            $this->questionService->createQuestion($data);

            if ($material) {
                return redirect()
                    ->route('admin.materials.questions.index', $material)
                    ->with('success', 'Soal berhasil ditambahkan.');
            }

            return redirect()
                ->route('admin.questions.index')
                ->with('success', 'Soal berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan soal: ' . $e->getMessage());
        }
    }

    public function edit(Material $material = null, Question $question)
    {
        $materials = $this->materialService->getAllMaterials();
        $question->load('answers');
        
        $material = $question->material;
        $subMaterials = $material ? $material->subMaterials()->orderBy('order')->get() : collect();
        
        return Inertia::render('Admin/Questions/Edit/Index', compact('question', 'materials', 'material', 'subMaterials'));
    }

    public function update(Request $request, Material $material = null, Question $question)
    {
        // Validasi dasar untuk semua field kecuali answers
        $baseValidation = [
            'question_text' => 'required|string',
            'question_type' => 'required|in:radio_button,drag_and_drop,fill_in_the_blank',
            'difficulty' => 'required|in:beginner,medium,hard',
            'material_id' => 'required|exists:materials,id',
            'sub_material_id' => 'nullable|exists:sub_materials,id',
        ];
        
        // Validasi khusus untuk answers berdasarkan tipe soal
        if ($request->question_type === 'fill_in_the_blank') {
            $answersValidation = ['answers' => 'required|array|min:1'];
        } else {
            $answersValidation = ['answers' => 'required|array|min:2'];
        }
        
        // Gabungkan validasi
        $validationRules = array_merge($baseValidation, $answersValidation);
        
        // Tambahkan validasi untuk setiap jawaban
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

        try {
            $this->questionService->updateQuestion($question, $request->all());

            $material = $question->material;
            
            if ($material) {
                return redirect()
                    ->route('admin.materials.questions.index', $material)
                    ->with('success', 'Question updated successfully.');
            }

            return redirect()
                ->route('admin.questions.index')
                ->with('success', 'Question updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui soal: ' . $e->getMessage());
        }
    }

    public function destroy(Material $material = null, Question $question)
    {
        $material_id = $question->material_id;
        
        try {
            $this->questionService->deleteQuestion($question);

            if ($material) {
                return redirect()
                    ->route('admin.materials.questions.index', $material)
                    ->with('success', 'Soal berhasil dihapus.');
            }

            return redirect()
                ->route('admin.questions.index')
                ->with('success', 'Soal berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus soal: ' . $e->getMessage());
        }
    }
}
    