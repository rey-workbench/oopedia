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

class RealQuestionSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Question::truncate();
        Answer::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $adminRoleIds = Role::whereIn('role_name', [RoleName::SUPERADMIN, RoleName::DOSEN])->pluck('id');
        $admin = User::whereIn('role_id', $adminRoleIds)->first();

        if (!$admin) {
            echo "Warning: No admin/dosen found.\n";
            return;
        }

        DB::beginTransaction();

        try {
            // --- MODUL 1: Pengantar Konsep Dasar OOP ---
            $m1 = Material::where('title', 'like', '%Konsep Dasar OOP%')->first();
            if ($m1) {
                $this->createRadioQuestion($m1->id, "Apa perbedaan mendasar antara paradigma prosedural dan Pemrograman Berorientasi Objek (OOP)?", ContentCategory::TEORI->value, QuestionDifficulty::BEGINNER->value, "Prosedural fokus pada langkah, OOP fokus pada objek.", [
                    ["Prosedural fokus pada urutan instruksi, sedangkan OOP fokus pada pengelompokan data dan perilaku (objek).", true, "Benar! Prosedural bersifat step-by-step."],
                    ["Prosedural lebih modern daripada OOP.", false, "Salah, OOP adalah paradigma yang lebih baru."],
                    ["OOP tidak membutuhkan variabel.", false, "Keduanya tetap menggunakan variabel."],
                    ["Prosedural lebih cocok untuk sistem skala besar.", false, "Sebaliknya, OOP dirancang untuk modularitas."],
                ], $admin->id);

                $this->createRadioQuestion($m1->id, "Manakah yang merupakan keuntungan utama dari penggunaan OOP di industri modern?", ContentCategory::TEORI->value, QuestionDifficulty::MEDIUM->value, "Fokus pada penggunaan kembali kode.", [
                    ["Code Reusability dan Extensibility.", true, "Benar! Memudahkan pengembangan fitur baru."],
                    ["Kecepatan eksekusi yang 10x lebih cepat.", false, "Bukan keuntungan utama secara teknis."],
                    ["Ukuran file kode yang lebih kecil.", false, "OOP justru seringkali memiliki kode yang lebih panjang."],
                    ["Tidak membutuhkan compiler.", false, "Paradigma tidak menentukan cara eksekusi."],
                ], $admin->id);
            }

            // --- MODUL 2: Class dan Object ---
            $m2 = Material::where('title', 'like', '%Class dan Object%')->first();
            if ($m2) {
                $this->createRadioQuestion($m2->id, "Jika Class diibaratkan sebagai sebuah cetakan (blueprint) rumah, maka Object adalah...", ContentCategory::TEORI->value, QuestionDifficulty::BEGINNER->value, "Object adalah instansi nyata.", [
                    ["Rumah fisik yang sudah dibangun berdasarkan cetakan tersebut.", true, "Benar! Object adalah wujud nyata dari Class."],
                    ["Gambar sketsa awal.", false, "Itu masih bagian dari desain Class."],
                    ["Peralatan tukang.", false, "Alat lebih cocok sebagai tool/compiler."],
                    ["Lahan tanah kosong.", false, "Lahan lebih cocok sebagai alokasi memori."],
                ], $admin->id);

                $this->createDragDropQuestion($m2->id, "Urutkan proses lifecycle objek berikut: [blank_1] -> [blank_2] -> [blank_3]", ContentCategory::TEORI->value, QuestionDifficulty::HARD->value, "Instansiasi adalah langkah pertama.", [
                    ["Instansiasi", "1", "Proses pembuatan objek."],
                    ["Operasional", "2", "Objek digunakan di memori."],
                    ["Garbage Collection", "3", "Proses penghancuran objek."],
                ], $admin->id);
            }

            // --- MODUL 3: Enkapsulasi (Encapsulation) ---
            $m3 = Material::where('title', 'like', '%Enkapsulasi%')->first();
            if ($m3) {
                $this->createRadioQuestion($m3->id, "Apa tujuan utama dari prinsip 'Information Hiding'?", ContentCategory::TEORI->value, QuestionDifficulty::MEDIUM->value, "Melindungi atribut internal.", [
                    ["Melindungi data internal agar tidak diubah secara ilegal dari luar objek.", true, "Tepat! Ini menjaga integritas data."],
                    ["Menyembunyikan source code agar tidak bisa dicuri.", false, "Salah, enkapsulasi bukan untuk proteksi hak cipta."],
                    ["Menghapus memori otomatis.", false, "Itu tugas Garbage Collector."],
                    ["Membuat program sulit dipahami.", false, "Tujuannya justru agar terstruktur."],
                ], $admin->id);

                $this->createFillBlankQuestion($m3->id, "Metode untuk mengambil nilai disebut _____, dan untuk mengubah nilai disebut _____.", ContentCategory::TEORI->value, QuestionDifficulty::HARD->value, "Gunakan istilah Getter dan Setter.", [
                    ["Getter", "Metode pengambil data."],
                    ["Setter", "Metode pengatur data."],
                ], $admin->id);
            }

            DB::commit();
            echo "Questions seeded successfully!\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    private function createRadioQuestion($materialId, $text, $type, $difficulty, $hint, $answers, $adminId)
    {
        $question = Question::create([
            'id' => str()->ulid()->toString(),
            'material_id' => $materialId,
            'question_text' => $text,
            'question_type' => QuestionType::RADIO_BUTTON->value,
            'type' => $type,
            'difficulty' => $difficulty,
            'hint' => $hint,
            'created_by' => $adminId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $answerData = [];
        foreach ($answers as $answer) {
            $answerData[] = [
                'id' => str()->ulid()->toString(),
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

    private function createFillBlankQuestion($materialId, $text, $type, $difficulty, $hint, $correctAnswers, $adminId)
    {
        $question = Question::create([
            'id' => str()->ulid()->toString(),
            'material_id' => $materialId,
            'question_text' => $text,
            'question_type' => QuestionType::FILL_IN_THE_BLANK->value,
            'type' => $type,
            'difficulty' => $difficulty,
            'hint' => $hint,
            'created_by' => $adminId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $answerData = [];
        foreach ($correctAnswers as $answer) {
            $answerData[] = [
                'id' => str()->ulid()->toString(),
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

    private function createDragDropQuestion($materialId, $text, $type, $difficulty, $hint, $items, $adminId)
    {
        $question = Question::create([
            'id' => str()->ulid()->toString(),
            'material_id' => $materialId,
            'question_text' => $text,
            'question_type' => QuestionType::DRAG_AND_DROP->value,
            'type' => $type,
            'difficulty' => $difficulty,
            'hint' => $hint,
            'created_by' => $adminId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $answerData = [];
        foreach ($items as $item) {
            $answerData[] = [
                'id' => str()->ulid()->toString(),
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
