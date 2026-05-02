<?php

namespace Database\Seeders;

use App\Enums\Lms\QuestionDifficulty;
use App\Enums\Lms\QuestionType;
use App\Models\Answer;
use App\Models\Material;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;

class FinalProjectSeeder extends Seeder
{
    public function run(): void
    {
        $dosen = User::whereHas('role', function ($q) {
            $q->where('role_name', 'dosen');
        })->first();
        $dosenId = $dosen ? $dosen->id : null;

        // 1. Create Final Project Material
        $material = Material::updateOrCreate(
            ['module_id' => '10'],
            [
                'title'   => 'Proyek Akhir: Arsitektur Sistem Terintegrasi',
                'content' => '<h2>Ujian Akhir Penguasaan OOP</h2>
                    <p>Selamat! Anda telah capai tahap akhir. Proyek ini akan menguji seluruh pemahaman Anda tentang 4 pilar OOP (Enkapsulasi, Pewarisan, Polimorfisme, dan Abstraksi) dalam satu skenario terpadu.</p>
                    <p>Selesaikan pertanyaan berikut dengan tingkat akurasi tinggi untuk mendapatkan sertifikasi.</p>',
                'created_by'       => $dosenId,
                'is_final_project' => true,
            ],
        );

        // 3. Create Final Questions
        $q1 = Question::updateOrCreate(
            ['question_text' => 'Dalam sebuah sistem pembayaran, Anda memiliki interface "PaymentProcessor" dan class "CreditCard", "EWallet", serta "BankTransfer". Konsep apa yang paling tepat menggambarkan kemampuan memanggil method "pay()" pada variabel bertipe "PaymentProcessor" tanpa peduli jenis pembayarannya?'],
            [
                'material_id'     => $material->id,
                'question_type'   => QuestionType::RADIO_BUTTON->value,
                'difficulty'      => QuestionDifficulty::HARD->value,
                'hint'            => 'Satu antarmuka, banyak wujud.',
                'created_by'      => $dosenId,
            ],
        );

        Answer::updateOrCreate(
            ['question_id' => $q1->id, 'answer_text' => 'Polimorfisme'],
            ['is_correct' => true, 'explanation' => 'Polimorfisme memungkinkan kita menggunakan interface umum untuk berbagai implementasi spesifik.'],
        );

        Answer::updateOrCreate(
            ['question_id' => $q1->id, 'answer_text' => 'Enkapsulasi'],
            ['is_correct' => false],
        );

        // 4. Create Syntax Question for Final Project
        $q2 = Question::updateOrCreate(
            ['question_text' => 'Manakah potongan kode Java yang benar untuk mendeklarasikan kelas "Manager" yang mewarisi dari "Employee" dan mengimplementasikan interface "Authenticatable"?'],
            [
                'material_id'     => $material->id,
                'question_type'   => QuestionType::RADIO_BUTTON->value,
                'difficulty'      => QuestionDifficulty::HARD->value,
                'hint'            => 'Urutannya adalah extends lalu implements.',
                'created_by'      => $dosenId,
            ],
        );

        Answer::updateOrCreate(
            ['question_id' => $q2->id, 'answer_text' => 'public class Manager extends Employee implements Authenticatable {}'],
            ['is_correct' => true, 'explanation' => 'Di Java, kelas hanya bisa extends satu superclass, tapi bisa implements banyak interface.'],
        );

    }
}
