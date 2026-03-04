<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Question;
use App\Models\SubMaterial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComprehensiveQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Question::truncate();
        Answer::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $subMaterials = SubMaterial::all();
        $admin = User::whereIn('role_id', [1, 2])->first();

        if ($subMaterials->isEmpty()) {
            echo "Warning: No submaterials found. Please run SubMaterialsSeeder first.\n";

            return;
        }

        if (!$admin) {
            echo "Warning: No admin/dosen found. Please run SuperadminSeeder/DosenSeeder first.\n";

            return;
        }

        $totalCreated = 0;
        DB::beginTransaction();

        try {
            foreach ($subMaterials as $subMaterial) {
                // Create 15 questions per submaterial
                for ($i = 1; $i <= 15; $i++) {
                    // Cycle through difficulty levels
                    $difficulty = $i <= 5 ? 'beginner' : ($i <= 10 ? 'medium' : 'hard');

                    // Cycle through question types
                    $typePool = ['radio_button', 'fill_in_the_blank', 'drag_and_drop'];
                    $questionType = $typePool[($i - 1) % 3];

                    // Handle mixed content type for question category
                    $qType = $subMaterial->jenis_konten === 'mixed' ? 'teori' : $subMaterial->jenis_konten;

                    if ($questionType === 'radio_button') {
                        $this->createRadioQuestion(
                            $subMaterial->material_id,
                            $subMaterial->id,
                            "Pertanyaan dummy #{$i} untuk sub-materi: {$subMaterial->title}",
                            $qType,
                            $difficulty,
                            'Pilih jawaban yang bertuliskan "ini benar"',
                        [
                            ['ini jawaban benar', true, 'Benar!'],
                            ['ini jawaban salah (1)', false, 'Salah.'],
                            ['ini jawaban salah (2)', false, 'Salah.'],
                            ['ini jawaban salah (3)', false, 'Salah.'],
                        ],
                            $admin->id,
                        );
                    }
                    elseif ($questionType === 'fill_in_the_blank') {
                        $this->createFillBlankQuestion(
                            $subMaterial->material_id,
                            $subMaterial->id,
                            "Ketik kata 'benar' untuk menjawab pertanyaan dummy #{$i} ini: _____",
                            $qType,
                            $difficulty,
                            'Isi dengan: benar',
                        [
                            ['benar', 'Bagus!'],
                            ['Benar', 'Bagus!'],
                        ],
                            $admin->id,
                        );
                    }
                    else {
                        $this->createDragDropQuestion(
                            $subMaterial->material_id,
                            $subMaterial->id,
                            "Urutkan elemen dummy #{$i} berikut sesuai angka 1-2-3: [blank_1] [blank_2] [blank_3]",
                            $qType,
                            $difficulty,
                            'Urutkan 1, 2, kemudian 3',
                        [
                            ['Elemen ke-1 (ini benar)', '1', 'Tepat!'],
                            ['Elemen ke-2 (ini benar)', '2', 'Tepat!'],
                            ['Elemen ke-3 (ini benar)', '3', 'Tepat!'],
                        ],
                            $admin->id,
                        );
                    }
                    $totalCreated++;
                }
            }
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

        echo 'Total questions created: ' . $totalCreated . "\n";
    }

    // ==================== HELPER METHODS ====================

    private function createRadioQuestion($materialId, $subMaterialId, $text, $type, $difficulty, $hint, $answers, $adminId)
    {
        $question = Question::create([
            'material_id' => $materialId,
            'sub_material_id' => $subMaterialId,
            'question_text' => $text,
            'question_type' => 'radio_button',
            'type' => $type,
            'difficulty' => $difficulty,
            'hint' => $hint,
            'created_by' => $adminId,
        ]);

        $answerData = [];
        foreach ($answers as $answer) {
            $answerData[] = [
                'question_id' => $question->id,
                'is_correct' => $answer[1],
                'answer_text' => $answer[0],
                'explanation' => $answer[2],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Answer::insert($answerData);
    }

    private function createFillBlankQuestion($materialId, $subMaterialId, $text, $type, $difficulty, $hint, $correctAnswers, $adminId)
    {
        $question = Question::create([
            'material_id' => $materialId,
            'sub_material_id' => $subMaterialId,
            'question_text' => $text,
            'question_type' => 'fill_in_the_blank',
            'type' => $type,
            'difficulty' => $difficulty,
            'hint' => $hint,
            'created_by' => $adminId,
        ]);

        $answerData = [];
        foreach ($correctAnswers as $answer) {
            $answerData[] = [
                'question_id' => $question->id,
                'is_correct' => true,
                'answer_text' => $answer[0],
                'blank_position' => 1,
                'explanation' => $answer[1],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Answer::insert($answerData);
    }

    private function createDragDropQuestion($materialId, $subMaterialId, $text, $type, $difficulty, $hint, $items, $adminId)
    {
        $question = Question::create([
            'material_id' => $materialId,
            'sub_material_id' => $subMaterialId,
            'question_text' => $text,
            'question_type' => 'drag_and_drop',
            'type' => $type,
            'difficulty' => $difficulty,
            'hint' => $hint,
            'created_by' => $adminId,
        ]);

        $answerData = [];
        foreach ($items as $item) {
            $answerData[] = [
                'question_id' => $question->id,
                'is_correct' => true,
                'answer_text' => $item[0],
                'drag_target' => $item[1],
                'explanation' => $item[2],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Answer::insert($answerData);
    }
}
