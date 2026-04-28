<?php

namespace Database\Seeders;

use App\Enums\Lms\ContentCategory;
use App\Enums\Lms\QuestionDifficulty;
use App\Enums\Lms\QuestionType;
use App\Enums\User\RoleName;
use App\Models\Answer;
use App\Models\Material;
use App\Models\Question;
use App\Models\Role;

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

        $materials    = Material::all();
        $adminRoleIds = Role::whereIn('role_name', [RoleName::SUPERADMIN, RoleName::DOSEN])->pluck('id');
        $admin        = User::whereIn('role_id', $adminRoleIds)->first();

        if ($materials->isEmpty()) {
            echo "Warning: No materials found. Please run MaterialsSeeder first.\n";

            return;
        }

        if (! $admin) {
            echo "Warning: No admin/dosen found.\n";

            return;
        }

        $totalCreated = 0;
        DB::beginTransaction();

        try {
            foreach ($materials as $material) {
                // Create 15 questions per material
                for ($i = 1; $i <= 15; $i++) {
                    $difficulty   = $i <= 5 ? QuestionDifficulty::BEGINNER->value : ($i <= 10 ? QuestionDifficulty::MEDIUM->value : QuestionDifficulty::HARD->value);
                    $typePool     = [QuestionType::RADIO_BUTTON->value, QuestionType::FILL_IN_THE_BLANK->value, QuestionType::DRAG_AND_DROP->value];
                    $questionType = $typePool[($i - 1) % 3];
                    $qType        = ContentCategory::TEORI->value;

                    if ($questionType === 'radio_button') {
                        $this->createRadioQuestion(
                            $material->id,
                            "Pertanyaan dummy #{$i} untuk materi: {$material->title}",
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
                    } elseif ($questionType === 'fill_in_the_blank') {
                        $this->createFillBlankQuestion(
                            $material->id,
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
                    } else {
                        $this->createDragDropQuestion(
                            $material->id,
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
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        echo 'Total questions created: ' . $totalCreated . "\n";
    }

    private function createRadioQuestion($materialId, $text, $type, $difficulty, $hint, $answers, $adminId)
    {
        $question = Question::create([
            'material_id'     => $materialId,
            'question_text'   => $text,
            'question_type'   => QuestionType::RADIO_BUTTON->value,
            'type'            => $type,
            'difficulty'      => $difficulty,
            'hint'            => $hint,
            'created_by'      => $adminId,
        ]);

        $answerData = [];
        foreach ($answers as $answer) {
            $answerData[] = [
                'id'          => str()->ulid()->toString(),
                'question_id' => $question->id,
                'is_correct'  => $answer[1],
                'answer_text' => $answer[0],
                'explanation' => $answer[2],
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }
        Answer::insert($answerData);
    }

    private function createFillBlankQuestion($materialId, $text, $type, $difficulty, $hint, $correctAnswers, $adminId)
    {
        $question = Question::create([
            'material_id'     => $materialId,
            'question_text'   => $text,
            'question_type'   => QuestionType::FILL_IN_THE_BLANK->value,
            'type'            => $type,
            'difficulty'      => $difficulty,
            'hint'            => $hint,
            'created_by'      => $adminId,
        ]);

        $answerData = [];
        foreach ($correctAnswers as $answer) {
            $answerData[] = [
                'id'             => str()->ulid()->toString(),
                'question_id'    => $question->id,
                'is_correct'     => true,
                'answer_text'    => $answer[0],
                'blank_position' => 1,
                'explanation'    => $answer[1],
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }
        Answer::insert($answerData);
    }

    private function createDragDropQuestion($materialId, $text, $type, $difficulty, $hint, $items, $adminId)
    {
        $question = Question::create([
            'material_id'     => $materialId,
            'question_text'   => $text,
            'question_type'   => QuestionType::DRAG_AND_DROP->value,
            'type'            => $type,
            'difficulty'      => $difficulty,
            'hint'            => $hint,
            'created_by'      => $adminId,
        ]);

        $answerData = [];
        foreach ($items as $item) {
            $answerData[] = [
                'id'          => str()->ulid()->toString(),
                'question_id' => $question->id,
                'is_correct'  => true,
                'answer_text' => $item[0],
                'drag_target' => $item[1],
                'explanation' => $item[2],
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }
        Answer::insert($answerData);
    }
}
