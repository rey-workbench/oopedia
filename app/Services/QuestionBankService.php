<?php

namespace App\Services;

use App\Repositories\QuestionBankRepository;
use App\Models\QuestionBankConfig;
use Illuminate\Support\Facades\DB;

class QuestionBankService
{
    protected $questionBankRepo;

    public function __construct(QuestionBankRepository $questionBankRepo)
    {
        $this->questionBankRepo = $questionBankRepo;
    }

    public function getAllQuestionBanks($search = null, $perPage = 10)
    {
        return $this->questionBankRepo->getWithCreator($search, $perPage);
    }

    public function createQuestionBank(array $data)
    {
        return $this->questionBankRepo->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'material_id' => $data['material_id'],
            'created_by' => auth()->id(),
        ]);
    }

    public function getQuestionBankDetails($id)
    {
        $questionBank = $this->questionBankRepo->getWithRelations($id, ['questions.material', 'questions.answers', 'configs.material']);
        
        $questions = $questionBank->questions;
        $questionCounts = [
            'beginner' => $questions->where('difficulty', 'beginner')->count(),
            'medium' => $questions->where('difficulty', 'medium')->count(),
            'hard' => $questions->where('difficulty', 'hard')->count(),
        ];

        return [
            'questionBank' => $questionBank,
            'questionCounts' => $questionCounts
        ];
    }

    public function updateQuestionBank($id, array $data)
    {
        return $this->questionBankRepo->update($id, [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
    }

    public function deleteQuestionBank($id)
    {
        return $this->questionBankRepo->delete($id);
    }

    public function addQuestionToBank($questionBankId, $questionId)
    {
        $questionBank = $this->questionBankRepo->find($questionBankId);
        if (!$questionBank->questions->contains($questionId)) {
            $questionBank->questions()->attach($questionId);
            return true;
        }
        return false;
    }

    public function removeQuestionFromBank($questionBankId, $questionId)
    {
        $questionBank = $this->questionBankRepo->find($questionBankId);
        $questionBank->questions()->detach($questionId);
        return true;
    }

    // Configuration related methods could also move here, but for brevity sticking to main QuestionBank CRUD first or extracting Config logic too if needed.
    // Given the complexity of Configs in controller, let's extract that too.

    public function getBankConfigs($questionBank)
    {
        return $questionBank->configs()->with('material')->get();
    }

    public function getBankConfigById($id, $questionBankId)
    {
        return QuestionBankConfig::where('id', $id)
            ->where('question_bank_id', $questionBankId)
            ->firstOrFail();
    }
    
    public function storeBankConfig($questionBank, array $data)
    {
        // Logic copied from controller and adapted
        if (isset($data['config_id'])) {
             // Update logic
             $config = QuestionBankConfig::findOrFail($data['config_id']);
             if ($config->question_bank_id != $questionBank->id) {
                 throw new \Exception('Konfigurasi tidak ditemukan');
             }
             
             $config->update([
                 'beginner_count' => $data['beginner_count'],
                 'medium_count' => $data['medium_count'],
                 'hard_count' => $data['hard_count'],
                 'is_active' => isset($data['is_active']),
             ]);
             return 'Konfigurasi bank soal berhasil diperbarui.';
        } else {
             // Create logic
             $existingConfig = QuestionBankConfig::where('question_bank_id', $questionBank->id)
                 ->where('material_id', $data['material_id'])
                 ->first();
                 
             if ($existingConfig) {
                 throw new \Exception('Konfigurasi untuk materi ini sudah ada.');
             }
             
             QuestionBankConfig::create([
                 'question_bank_id' => $questionBank->id,
                 'material_id' => $data['material_id'],
                 'beginner_count' => $data['beginner_count'],
                 'medium_count' => $data['medium_count'],
                 'hard_count' => $data['hard_count'],
                 'is_active' => isset($data['is_active']),
             ]);
             return 'Konfigurasi bank soal berhasil ditambahkan.';
        }
    }

    public function deleteBankConfig($config)
    {
        return $config->delete();
    }
}
