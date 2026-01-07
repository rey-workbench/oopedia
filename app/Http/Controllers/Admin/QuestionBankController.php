<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\Question;
use App\Models\QuestionBankConfig;
use Illuminate\Http\Request;
use App\Services\QuestionBankService;
use App\Services\MaterialService;
use App\Services\QuestionService;

class QuestionBankController extends Controller
{
    protected $questionBankService;
    protected $materialService;
    protected $questionService;

    public function __construct(
        QuestionBankService $questionBankService,
        MaterialService $materialService,
        QuestionService $questionService
    ) {
        $this->questionBankService = $questionBankService;
        $this->materialService = $materialService;
        $this->questionService = $questionService;
    }
    /**
     * Display a listing of the question banks.
     */
    public function index(Request $request)
    {
        // Hanya superadmin dan admin yang boleh akses
        if (auth()->user()->role_id > 2) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke bank soal');
        }
        
        $search = $request->input('search');
        $questionBanks = $this->questionBankService->getAllQuestionBanks($search);
        
        return view('admin.question-banks.index', compact('questionBanks'));
    }

    /**
     * Show the form for creating a new question bank.
     */
    public function create()
    {
        $materials = $this->materialService->getAllMaterials();
        return view('admin.question-banks.create', compact('materials'));
    }

    /**
     * Store a newly created question bank in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'material_id' => 'required|exists:materials,id',
        ]);
        
        $questionBank = new QuestionBank([
            'name' => $request->name,
            'description' => $request->description,
            'material_id' => $request->material_id,
            'created_by' => auth()->id(),
        ]);
        
        $questionBank->save();
        
        return redirect()->route('admin.question-banks.index')
            ->with('success', 'Bank soal berhasil dibuat.');
    }

    /**
     * Display the specified question bank.
     */
    public function show(QuestionBank $questionBank)
    {
        $questionBank->load(['questions.material', 'questions.answers', 'configs.material']);
        $questions = $questionBank->questions;
        
        // Count questions by difficulty
        $questionCounts = [
            'beginner' => $questions->where('difficulty', 'beginner')->count(),
            'medium' => $questions->where('difficulty', 'medium')->count(),
            'hard' => $questions->where('difficulty', 'hard')->count(),
        ];

        return view('admin.question-banks.show', compact('questionBank', 'questionCounts'));
    }

    /**
     * Show the form for editing the specified question bank.
     */
    public function edit(QuestionBank $questionBank)
    {
        return view('admin.question-banks.edit', compact('questionBank'));
    }

    /**
     * Update the specified question bank in storage.
     */
    public function update(Request $request, QuestionBank $questionBank)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $questionBank->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        
        return redirect()->route('admin.question-banks.index')
            ->with('success', 'Bank soal berhasil diperbarui.');
    }

    /**
     * Remove the specified question bank from storage.
     */
    public function destroy(QuestionBank $questionBank)
    {
        $this->questionBankService->deleteQuestionBank($questionBank->id);
        
        return redirect()->route('admin.question-banks.index')
            ->with('success', 'Bank soal berhasil dihapus.');
    }

    /**
     * Show questions that can be added to the bank.
     */
    public function manageQuestions(QuestionBank $questionBank, Request $request)
    {
        $search = $request->input('search');
        $difficulty = $request->input('difficulty');
        
        // Get existing question IDs in this bank
        $existingQuestionIds = $questionBank->questions->pluck('id')->toArray();
        
        $questions = $this->questionService->getAvailableQuestionsForBank(
            $questionBank->material_id,
            $existingQuestionIds,
            $search,
            $difficulty
        );
        
        return view('admin.question-banks.manage-questions', compact(
            'questionBank', 
            'questions'
        ));
    }
    
    /**
     * Add a question to the bank.
     */
    public function addQuestion(QuestionBank $questionBank, Question $question)
    {
        // Check if question already exists in the bank and from same material
        if ($question->material_id != $questionBank->material_id) {
            return redirect()->back()->with('error', 'Soal tidak dapat ditambahkan karena tidak sesuai dengan materi bank soal.');
        }
        
        $success = $this->questionBankService->addQuestionToBank($questionBank->id, $question->id);
        
        if ($success) {
            return redirect()->back()->with('success', 'Soal berhasil ditambahkan ke bank soal.');
        }
        
        return redirect()->back()->with('error', 'Soal sudah ada dalam bank soal.');
    }
    
    /**
     * Remove a question from the bank.
     */
    public function removeQuestion(QuestionBank $questionBank, Question $question)
    {
        $this->questionBankService->removeQuestionFromBank($questionBank->id, $question->id);
        return redirect()->back()->with('success', 'Soal berhasil dihapus dari bank soal.');
    }
    
    /**
     * Show configuration form for question bank.
     */
    public function configureBank(QuestionBank $questionBank, Request $request)
    {
        $materials = $this->materialService->getAllMaterials();
        $configs = $this->questionBankService->getBankConfigs($questionBank);
        
        // Handle edit mode
        $editConfig = null;
        if ($request->has('edit')) {
            $editConfig = $this->questionBankService->getBankConfigById($request->edit, $questionBank->id);
        }
        
        return view('admin.question-banks.configure', compact('questionBank', 'materials', 'configs', 'editConfig'));
    }
    
    /**
     * Store bank configuration
     */
    public function storeConfig(Request $request, QuestionBank $questionBank)
    {
        // Validasi data
        $rules = [
            'beginner_count' => 'required|integer|min:0',
            'medium_count' => 'required|integer|min:0',
            'hard_count' => 'required|integer|min:0',
        ];
        
        // Jika ini konfigurasi baru, wajib pilih materi
        if (!$request->has('config_id')) {
            $rules['material_id'] = 'required|exists:materials,id';
        }
        
        $request->validate($rules);
        
        // Pastikan ada minimal satu soal yang diatur
        $totalQuestions = (int)$request->beginner_count + (int)$request->medium_count + (int)$request->hard_count;
        if ($totalQuestions <= 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Total soal harus lebih dari 0');
        }
        
        try {
            $message = $this->questionBankService->storeBankConfig($questionBank, $request->all());
            return redirect()->route('admin.question-banks.configure', $questionBank)
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
    
    /**
     * Delete a bank configuration.
     */
    public function deleteConfig(QuestionBankConfig $config)
    {
        $questionBankId = $config->question_bank_id;
        $this->questionBankService->deleteBankConfig($config);
        
        return redirect()->route('admin.question-banks.configure', $questionBankId)
            ->with('success', 'Konfigurasi bank soal berhasil dihapus.');
    }
}
