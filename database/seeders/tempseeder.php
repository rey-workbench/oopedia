<?php

namespace Database\Seeders;

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
        $admin        = User::whereIn('role_id', $adminRoleIds)->first();

        if (! $admin) {
            echo "Warning: No admin/dosen found.\n";

            return;
        }

        $materials      = Material::select(['id', 'title'])->get();
        $totalQuestions = 0;

        DB::beginTransaction();

        try {
            foreach ($materials as $material) {
                $questions = $this->getQuestionsForModule($material->title);
                foreach ($questions as $q) {
                    $this->createQuestion($material->id, $q, $admin->id);
                    $totalQuestions++;
                }
            }

            DB::commit();
            echo "Successfully seeded $totalQuestions questions across " . $materials->count() . " modules!\n";
        } catch (\Exception $e) {
            DB::rollBack();
            echo 'Error: ' . $e->getMessage() . "\n";
        }
    }

    private function createQuestion($materialId, $data, $adminId)
    {
        $question = Question::create([
            'id'            => str()->ulid()->toString(),
            'material_id'   => $materialId,
            'question_text' => $data['text'],
            'question_type' => $data['type'],
            'difficulty'    => $data['difficulty'],
            'hint'          => $data['hint'] ?? 'Pikirkan konsep dasar yang telah dipelajari.',
            'created_by'    => $adminId,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $answerData = [];
        if ($data['type'] === QuestionType::RADIO_BUTTON->value) {
            foreach ($data['answers'] as $answer) {
                $answerData[] = [
                    'id'          => str()->ulid()->toString(),
                    'question_id' => $question->id,
                    'is_correct'  => $answer[1],
                    'answer_text' => $answer[0],
                    'explanation' => $answer[2] ?? ($answer[1] ? 'Jawaban Anda benar!' : 'Jawaban ini kurang tepat.'),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
        } elseif ($data['type'] === QuestionType::FILL_IN_THE_BLANK->value) {
            foreach ($data['answers'] as $index => $answer) {
                $answerData[] = [
                    'id'             => str()->ulid()->toString(),
                    'question_id'    => $question->id,
                    'is_correct'     => true,
                    'answer_text'    => $answer[0],
                    'blank_position' => $index + 1,
                    'explanation'    => $answer[1] ?? 'Istilah ini tepat untuk mengisi bagian yang kosong.',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
            }
        } elseif ($data['type'] === QuestionType::DRAG_AND_DROP->value) {
            foreach ($data['answers'] as $answer) {
                $answerData[] = [
                    'id'          => str()->ulid()->toString(),
                    'question_id' => $question->id,
                    'is_correct'  => true,
                    'answer_text' => $answer[0],
                    'drag_target' => $answer[1],
                    'explanation' => $answer[2] ?? 'Urutan/pasangan ini sudah benar.',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
        }

        Answer::insert($answerData);
    }

    private function getQuestionsForModule($title)
    {
        $allQuestions = [
            'Paradigma OOP vs Struktural' => [
                // BEGINNER (7)
                [
                    'text'       => 'Apa perbedaan utama antara pemrograman prosedural dan OOP?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Pikirkan tentang fokus utama eksekusi: apakah langkah-langkah fungsi atau entitas data?',
                    'answers'    => [
                        ['Prosedural fokus pada fungsi, OOP fokus pada data dan objek', true, 'Benar!'],
                        ['Prosedural lebih cepat daripada OOP', false, 'Bukan perbedaan utama'],
                        ['OOP tidak menggunakan fungsi sama sekali', false, 'Salah'],
                        ['Prosedural hanya untuk bahasa C', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Unit terkecil yang memiliki data dan perilaku dalam OOP disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Ini adalah perwujudan nyata (instansi) dari sebuah blueprint atau class.',
                    'answers'    => [
                        ['Objek', true, 'Objek adalah instansi dari class'],
                        ['Fungsi', false, 'Fungsi adalah bagian dari objek'],
                        ['Variabel', false, 'Variabel menyimpan data'],
                        ['Array', false, 'Array adalah struktur data'],
                    ],
                ],
                [
                    'text'       => 'Apa yang dimaksud dengan modularitas dalam OOP?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Konsep ini membagi sistem besar menjadi bagian-bagian kecil yang independen.',
                    'answers'    => [
                        ['Membagi program menjadi bagian-bagian kecil yang independen', true, 'Ya, ini inti modularitas'],
                        ['Membuat program dalam satu file besar', false, 'Ini justru tidak modular'],
                        ['Menggunakan banyak variabel global', false, 'Salah'],
                        ['Menyembunyikan kode dari user', false, 'Itu enkapsulasi'],
                    ],
                ],
                [
                    'text'       => 'Kemampuan untuk menggunakan kembali kode yang sudah ada disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Istilah ini merujuk pada "penggunaan kembali" (re-use) komponen perangkat lunak.',
                    'answers'    => [
                        ['Code Reusability', true, 'Benar!'],
                        ['Redundansi', false, 'Ini hal negatif'],
                        ['Refactoring', false, 'Ini perbaikan struktur'],
                        ['Debugging', false, 'Ini pencarian bug'],
                    ],
                ],
                [
                    'text'       => 'Dalam OOP, "Atribut" mewakili apa?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Atribut menjelaskan karakteristik atau data yang dimiliki oleh sebuah objek.',
                    'answers'    => [
                        ['Data atau karakteristik objek', true, 'Benar!'],
                        ['Tindakan yang bisa dilakukan', false, 'Itu Method'],
                        ['Cara objek dibuat', false, 'Itu Konstruktor'],
                        ['Alamat memori', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Manakah yang termasuk pilar utama OOP?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Ingat 4 konsep dasar yang menyokong paradigma OOP (A-P-I-E).',
                    'answers'    => [
                        ['Encapsulation, Inheritance, Polymorphism, Abstraction', true, 'Benar!'],
                        ['Variable, Loop, Function, Class', false, 'Itu konsep dasar pemrograman'],
                        ['HTML, CSS, JS, PHP', false, 'Itu teknologi web'],
                        ['Array, Object, String, Integer', false, 'Itu tipe data'],
                    ],
                ],
                [
                    'text'       => 'Apa yang dimaksud dengan Abstraction?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Pikirkan tentang penyembunyian kerumitan internal di balik antarmuka sederhana.',
                    'answers'    => [
                        ['Menyembunyikan detail implementasi dan menampilkan fungsi penting saja', true, 'Benar!'],
                        ['Membuat kode menjadi sangat kompleks', false, 'Salah'],
                        ['Menghapus data yang tidak perlu', false, 'Salah'],
                        ['Menggabungkan dua class', false, 'Salah'],
                    ],
                ],
                // MEDIUM (7)
                [
                    'text'       => 'Mengapa OOP dianggap lebih mudah dikelola untuk sistem besar?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Bayangkan sistem yang terbagi-bagi dalam "kotak" (objek) yang rapi dan terisolasi.',
                    'answers'    => [
                        ['Karena setiap bagian sistem terisolasi dalam objek', true, 'Mempermudah debugging dan pengembangan'],
                        ['Karena tidak memerlukan compiler', false, 'Salah'],
                        ['Karena jalannya program lebih linier', false, 'Salah'],
                        ['Karena memori yang digunakan lebih sedikit', false, 'Belum tentu'],
                    ],
                ],
                [
                    'text'       => 'Konsep "Information Hiding" berhubungan erat dengan?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Konsep ini menjaga agar detail internal objek tidak bocor atau diubah sembarangan dari luar.',
                    'answers'    => [
                        ['Encapsulation', true, 'Benar!'],
                        ['Inheritance', false, 'Salah'],
                        ['Polymorphism', false, 'Salah'],
                        ['Recursion', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Istilah untuk tindakan yang bisa dilakukan oleh sebuah objek adalah?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Ini adalah istilah untuk fungsi yang didefinisikan di dalam lingkup sebuah class.',
                    'answers'    => [
                        ['Method', true, 'Benar!'],
                        ['Field', false, 'Salah'],
                        ['Property', false, 'Salah'],
                        ['State', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dalam paradigma prosedural, fokus utama adalah pada?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Pikirkan tentang resep masakan: kumpulan langkah-langhat yang harus diikuti secara berurutan.',
                    'answers'    => [
                        ['Algoritma dan langkah-langkah kerja', true, 'Benar!'],
                        ['Hubungan antar entitas', false, 'Salah'],
                        ['Keamanan data', false, 'Salah'],
                        ['Efisiensi memori objek', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Siapa tokoh yang mempopulerkan istilah "Object Oriented Programming"?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Tokoh ini adalah perintis bahasa Smalltalk dan pencetus istilah OOP.',
                    'answers'    => [
                        ['Alan Kay', true, 'Benar!'],
                        ['James Gosling', false, 'Pencipta Java'],
                        ['Dennis Ritchie', false, 'Pencipta C'],
                        ['Bjarne Stroustrup', false, 'Pencipta C++'],
                    ],
                ],
                [
                    'text'       => 'Apa keuntungan menggunakan OOP dalam hal pemeliharaan (maintenance)?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Konsep modularitas membantu isolasi kesalahan sehingga perubahan di satu tempat tidak merusak bagian lain.',
                    'answers'    => [
                        ['Perubahan pada satu objek tidak langsung merusak objek lain', true, 'Benar!'],
                        ['Kode otomatis memperbaiki dirinya sendiri', false, 'Salah'],
                        ['Tidak perlu melakukan testing', false, 'Salah'],
                        ['Semua jawaban benar', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa perbedaan antara State dan Behavior?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'State merujuk pada data (apa yang dimiliki), sementara Behavior merujuk pada aksi (apa yang dilakukan).',
                    'answers'    => [
                        ['State adalah data, Behavior adalah fungsi', true, 'Benar!'],
                        ['State adalah fungsi, Behavior adalah data', false, 'Terbalik'],
                        ['Keduanya adalah hal yang sama', false, 'Salah'],
                        ['State bersifat dinamis, Behavior bersifat statis', false, 'Salah'],
                    ],
                ],
                // HARD (6)
                [
                    'text'       => 'Urutkan proses berpikir dalam desain OOP: [blank_1] -> [blank_2] -> [blank_3]',
                    'type'       => QuestionType::DRAG_AND_DROP->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Mulailah dengan mengenali benda-bendanya (entitas), baru kemudian bagaimana mereka saling berhubungan.',
                    'answers'    => [
                        ['Identifikasi Objek', '1', 'Langkah pertama'],
                        ['Tentukan Interaksi', '2', 'Langkah kedua'],
                        ['Implementasi Class', '3', 'Langkah ketiga'],
                    ],
                ],
                [
                    'text'       => '____ adalah paradigma yang memisahkan data dan fungsi, sedangkan ____ menggabungkannya.',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Struktural memisahkan data dari logika, sedangkan OOP menyatukan keduanya dalam satu wadah.',
                    'answers'    => [
                        ['Prosedural', 'Memisahkan data dan fungsi'],
                        ['OOP', 'Menggabungkan data dan fungsi'],
                    ],
                ],
                [
                    'text'       => 'Manakah dari pernyataan berikut yang BENAR tentang modularitas?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Pikirkan tentang kemandirian tiap bagian kecil sistem (Loose Coupling).',
                    'answers'    => [
                        ['Modularitas tinggi mengurangi ketergantungan antar modul (loose coupling)', true, 'Benar!'],
                        ['Modularitas tinggi meningkatkan ketergantungan antar modul', false, 'Salah'],
                        ['Modularitas hanya bisa dicapai dengan inheritance', false, 'Salah'],
                        ['Modularitas membuat eksekusi program menjadi lambat', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa dampak negatif jika sebuah program TIDAK modular?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Bayangkan jika semua kabel di rumah Anda menyatu tanpa saklar terpisah: satu konslet, semua mati.',
                    'answers'    => [
                        ['Satu kesalahan kecil dapat berdampak luas ke seluruh sistem', true, 'Benar!'],
                        ['Program menjadi terlalu cepat', false, 'Salah'],
                        ['Program menjadi terlalu aman', false, 'Salah'],
                        ['Tidak ada dampak negatif', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dalam OOP, komunikasi antar objek dilakukan melalui?',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Objek saling berinteraksi dengan cara mengirimkan sinyal atau memanggil method objek lain.',
                    'answers'    => [
                        ['Message Passing', 'Komunikasi antar objek'],
                    ],
                ],
                [
                    'text'       => 'Prinsip SOLID dalam OOP bertujuan untuk?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'SOLID adalah akronim untuk 5 prinsip desain dasar yang membuat software lebih fleksibel dan mudah dipelihara.',
                    'answers'    => [
                        ['Meningkatkan maintainability dan fleksibilitas kode', true, 'Benar!'],
                        ['Mempercepat proses kompilasi', false, 'Salah'],
                        ['Menghilangkan kebutuhan akan dokumentasi', false, 'Salah'],
                        ['Mengurangi jumlah baris kode secara drastis', false, 'Salah'],
                    ],
                ],
            ],
            'Anatomi Class & Object' => [
                // BEGINNER (7)
                [
                    'text'       => 'Class diibaratkan sebagai sebuah ____, sedangkan Object adalah ____.',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Pikirkan tentang perbedaan antara desain arsitektur dan gedung aslinya.',
                    'answers'    => [
                        ['Blueprint', 'Cetak biru'],
                        ['Instance', 'Wujud nyata'],
                    ],
                ],
                [
                    'text'       => 'Jika kita memiliki class "Mobil", maka objeknya bisa berupa?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Objek adalah wujud spesifik yang bisa Anda tunjuk di dunia nyata.',
                    'answers'    => [
                        ['Mobil Toyota Avanza milik Pak Budi', true, 'Ini adalah instansi nyata'],
                        ['Daftar spesifikasi mobil', false, 'Ini adalah bagian dari class'],
                        ['Cara mengendarai mobil', false, 'Ini adalah method'],
                        ['Pabrik mobil', false, 'Ini entitas lain'],
                    ],
                ],
                [
                    'text'       => 'Keyword apa yang biasanya digunakan untuk membuat objek baru di Java/C#/PHP?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Keyword ini berarti "baru" dalam bahasa Inggris.',
                    'answers'    => [
                        ['new', true, 'Benar!'],
                        ['create', false, 'Salah'],
                        ['make', false, 'Salah'],
                        ['build', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa yang mendefinisikan struktur data dalam sebuah class?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Ini adalah variabel yang melekat pada objek untuk menyimpan data.',
                    'answers'    => [
                        ['Atribut / Field', true, 'Benar!'],
                        ['Method', false, 'Itu perilaku'],
                        ['Konstruktor', false, 'Itu inisialisasi'],
                        ['Package', false, 'Itu pengelompokan'],
                    ],
                ],
                [
                    'text'       => 'Satu class dapat digunakan untuk membuat berapa banyak objek?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Pikirkan tentang satu cetakan kue yang bisa digunakan untuk membuat banyak kue yang serupa.',
                    'answers'    => [
                        ['Banyak (tidak terbatas)', true, 'Benar!'],
                        ['Hanya satu', false, 'Itu Singleton pattern'],
                        ['Maksimal sepuluh', false, 'Salah'],
                        ['Tergantung ukuran file', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Method khusus yang dipanggil saat objek dibuat disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Namanya berasal dari kata dalam bahasa Inggris yang berarti "membangun" atau "penyusun".',
                    'answers'    => [
                        ['Constructor', true, 'Benar!'],
                        ['Destructor', false, 'Salah'],
                        ['Initializator', false, 'Salah'],
                        ['Main method', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Di mana objek disimpan dalam memori komputer?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Ada dua area utama: satu untuk data kecil (Stack) dan area luas ini (Heap) untuk menyimpan wujud fisik objek.',
                    'answers'    => [
                        ['Heap', true, 'Objek biasanya disimpan di heap'],
                        ['Stack', false, 'Variabel lokal biasanya di stack'],
                        ['Register', false, 'Salah'],
                        ['Hard disk', false, 'Salah'],
                    ],
                ],
                // MEDIUM (7)
                [
                    'text'       => 'Apa perbedaan antara Atribut Statis dan Atribut Instansi?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Statis adalah "milik bersama" satu kelas, sedangkan Instansi adalah "milik pribadi" tiap objek masing-masing.',
                    'answers'    => [
                        ['Statis milik class, Instansi milik objek individual', true, 'Benar!'],
                        ['Statis tidak bisa diubah, Instansi bisa', false, 'Salah'],
                        ['Statis hanya untuk angka, Instansi untuk string', false, 'Salah'],
                        ['Keduanya sama saja', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa kegunaan utama dari Keyword "this" (atau "$this" di PHP)?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Gunakan kata ini untuk menunjuk kepada "diri sendiri" atau objek yang saat ini sedang aktif menjalankan kode.',
                    'answers'    => [
                        ['Merujuk pada objek saat ini (current instance)', true, 'Benar!'],
                        ['Membuat objek baru', false, 'Salah'],
                        ['Menghapus objek', false, 'Salah'],
                        ['Memanggil class induk', false, 'Itu super/parent'],
                    ],
                ],
                [
                    'text'       => 'Jika sebuah atribut dideklarasikan sebagai "final" atau "const", apa artinya?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Sesuai namanya (final), nilai yang sudah diberikan tidak dapat diganggu gugat atau diubah lagi.',
                    'answers'    => [
                        ['Nilainya tidak dapat diubah setelah diinisialisasi', true, 'Benar!'],
                        ['Hanya bisa diakses di hari libur', false, 'Lelucon'],
                        ['Metodenya tidak bisa dipanggil', false, 'Salah'],
                        ['Atribut tersebut akan otomatis dihapus', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Proses pembuatan objek dari sebuah class disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Berasal dari kata "instance", yaitu proses menciptakan wujud nyata dari sebuah konsep (class).',
                    'answers'    => [
                        ['Instantiation', true, 'Benar!'],
                        ['Initialization', false, 'Salah'],
                        ['Declaration', false, 'Salah'],
                        ['Allocation', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa yang terjadi jika kita tidak mendefinisikan konstruktor dalam sebuah class?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Jangan khawatir, Java akan secara otomatis memberikan satu konstruktor "bawaan" yang kosong agar objek tetap bisa dibuat.',
                    'answers'    => [
                        ['Compiler akan memberikan default constructor otomatis', true, 'Benar!'],
                        ['Program akan error dan tidak bisa jalan', false, 'Salah'],
                        ['Objek tidak bisa dibuat', false, 'Salah'],
                        ['Objek akan bernilai null', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa tujuan utama dari Constructor?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Tugas utamanya adalah memberikan "bekal" atau nilai awal pada atribut saat sebuah objek baru saja diciptakan.',
                    'answers'    => [
                        ['Inisialisasi nilai awal atribut objek', true, 'Benar!'],
                        ['Menghancurkan objek', false, 'Itu destructor'],
                        ['Mencetak data ke layar', false, 'Salah'],
                        ['Melakukan perhitungan matematika', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Manakah pernyataan yang benar tentang Class?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Class bukanlah sebuah benda nyata, melainkan sebuah "blueprint" atau tipe data baru yang kita rancang sendiri.',
                    'answers'    => [
                        ['Class adalah tipe data yang didefinisikan pengguna', true, 'Benar!'],
                        ['Class adalah variabel global', false, 'Salah'],
                        ['Class tidak bisa memiliki method', false, 'Salah'],
                        ['Class harus selalu memiliki nama "Main"', false, 'Salah'],
                    ],
                ],
                // HARD (6)
                [
                    'text'       => 'Urutkan lifecycle sebuah objek: [blank_1] -> [blank_2] -> [blank_3]',
                    'type'       => QuestionType::DRAG_AND_DROP->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Pikirkan urutan logisnya: Siapkan nama variabel -> Buat benda fisiknya di memori -> Beri nilai-nilai awal.',
                    'answers'    => [
                        ['Deklarasi Variabel Referensi', '1', 'Siapkan wadah'],
                        ['Instansiasi (new)', '2', 'Buat di memori'],
                        ['Inisialisasi (Constructor)', '3', 'Set nilai awal'],
                    ],
                ],
                [
                    'text'       => 'Apa perbedaan utama antara Heap dan Stack dalam manajemen memori objek?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Satu tempat digunakan untuk menyimpan "alamat" (referensi), sedangkan tempat yang lain digunakan untuk menyimpan "benda fisiknya".',
                    'answers'    => [
                        ['Stack menyimpan referensi, Heap menyimpan objek fisik', true, 'Benar!'],
                        ['Heap lebih cepat daripada Stack', false, 'Salah'],
                        ['Stack tidak terbatas ukurannya', false, 'Salah'],
                        ['Keduanya menyimpan data yang sama', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dalam Java, "Garbage Collection" bertugas untuk?',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Bayangkan sebuah petugas kebersihan otomatis yang tugasnya membuang objek-objek yang sudah tidak digunakan lagi agar memori tetap bersih.',
                    'answers'    => [
                        ['Manajemen Memori Otomatis', 'Menghapus objek yang tidak terpakai'],
                    ],
                ],
                [
                    'text'       => 'Kapan sebuah objek memenuhi syarat untuk dihapus oleh Garbage Collector?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Objek akan dibuang jika sudah benar-benar "terisolasi" atau tidak ada lagi variabel lain yang menunjuk ke arahnya.',
                    'answers'    => [
                        ['Saat tidak ada lagi referensi yang menunjuk ke objek tersebut', true, 'Benar!'],
                        ['Saat program dimatikan', false, 'Salah'],
                        ['Setelah 10 menit pembuatan', false, 'Salah'],
                        ['Saat memori komputer penuh', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Jika kita menulis "Mobil m1 = new Mobil();", "m1" disebut sebagai?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Variabel ini bukanlah objek mobil itu sendiri, melainkan sebuah "penunjuk" atau perantara untuk mengakses objek tersebut.',
                    'answers'    => [
                        ['Reference Variable', true, 'Benar!'],
                        ['Literal', false, 'Salah'],
                        ['Constant', false, 'Salah'],
                        ['Primitive Variable', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa kegunaan dari Static Initializer block?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Blok kode ini hanya dijalankan satu kali saja saat sebuah class pertama kali dikenal atau dimuat oleh sistem.',
                    'answers'    => [
                        ['Inisialisasi variabel statis saat class dimuat ke memori', true, 'Benar!'],
                        ['Membuat objek statis', false, 'Salah'],
                        ['Mempercepat jalannya perulangan', false, 'Salah'],
                        ['Menggantikan fungsi main', false, 'Salah'],
                    ],
                ],
            ],
            'Enkapsulasi & Information Hiding' => [
                // BEGINNER (7)
                [
                    'text'       => 'Enkapsulasi sering disebut sebagai "____" data.',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Bayangkan membungkus data agar tidak bisa diakses sembarangan dari luar.',
                    'answers'    => [
                        ['Pembungkusan', 'Data wrapping'],
                    ],
                ],
                [
                    'text'       => 'Akses modifier mana yang paling ketat?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Modifier ini hanya memperbolehkan akses di dalam class itu sendiri.',
                    'answers'    => [
                        ['private', true, 'Hanya bisa diakses di class itu sendiri'],
                        ['public', false, 'Bisa diakses siapa saja'],
                        ['protected', false, 'Hanya untuk turunan/package'],
                        ['default', false, 'Hanya untuk satu package'],
                    ],
                ],
                [
                    'text'       => 'Mengapa kita perlu menggunakan private untuk atribut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Pikirkan tentang keamanan data agar tidak bisa diubah sembarangan dari luar.',
                    'answers'    => [
                        ['Untuk mencegah akses langsung dari luar class', true, 'Menjaga integritas data'],
                        ['Agar kode terlihat lebih keren', false, 'Salah'],
                        ['Agar program berjalan lebih cepat', false, 'Salah'],
                        ['Memang aturannya begitu saja', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Metode untuk mengambil nilai atribut private disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Namanya berasal dari kata "Get" yang berarti mengambil dalam bahasa Inggris.',
                    'answers'    => [
                        ['Getter', true, 'Benar!'],
                        ['Setter', false, 'Itu pengubah'],
                        ['Constructor', false, 'Itu inisialisasi'],
                        ['Helper', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Metode untuk mengubah nilai atribut private disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Namanya berasal dari kata "Set" yang berarti memasang atau mengatur.',
                    'answers'    => [
                        ['Setter', true, 'Benar!'],
                        ['Getter', false, 'Itu pengambil'],
                        ['Changer', false, 'Salah'],
                        ['Modifier', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Akses modifier "public" berarti?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Public artinya umum atau terbuka untuk siapa saja di dalam program.',
                    'answers'    => [
                        ['Bisa diakses dari mana saja', true, 'Benar!'],
                        ['Bisa diakses hanya oleh admin', false, 'Salah'],
                        ['Hanya bisa dilihat tidak bisa diubah', false, 'Salah'],
                        ['Hanya bisa diakses di satu file', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa itu Read-Only class?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Read-only berarti hanya bisa dibaca tanpa bisa diubah nilainya.',
                    'answers'    => [
                        ['Class yang hanya memiliki Getter tanpa Setter', true, 'Benar!'],
                        ['Class yang tidak bisa dibaca', false, 'Salah'],
                        ['Class yang tidak memiliki atribut', false, 'Salah'],
                        ['Class yang isinya rahasia', false, 'Salah'],
                    ],
                ],
                // MEDIUM (7)
                [
                    'text'       => 'Manakah yang merupakan keuntungan dari Enkapsulasi?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Enkapsulasi memungkinkan kita mengontrol bagaimana data dimasukkan (sebagai filter).',
                    'answers'    => [
                        ['Memudahkan validasi data sebelum disimpan', true, 'Bisa dilakukan di dalam Setter'],
                        ['Menambah jumlah file kode', false, 'Salah'],
                        ['Membuat program jadi open source', false, 'Salah'],
                        ['Menghilangkan kebutuhan akan class', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Modifier "protected" memungkinkan akses bagi?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Protected adalah jalan tengah antara private dan public, sering terkait dengan "warisan".',
                    'answers'    => [
                        ['Class itu sendiri, subclass, dan package yang sama', true, 'Benar!'],
                        ['Hanya subclass saja', false, 'Salah'],
                        ['Seluruh dunia', false, 'Salah'],
                        ['Hanya class induk', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa yang terjadi jika kita tidak menuliskan modifier (default)?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Ini adalah akses default di Java yang sering disebut "package-private".',
                    'answers'    => [
                        ['Aksesnya terbatas hanya pada package yang sama (Package-Private)', true, 'Benar!'],
                        ['Otomatis menjadi public', false, 'Salah'],
                        ['Otomatis menjadi private', false, 'Salah'],
                        ['Program akan error', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Bagaimana cara terbaik menerapkan Enkapsulasi?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Kombinasi paling standar: data disembunyikan, akses dikontrol lewat method public.',
                    'answers'    => [
                        ['Atribut private, method public', true, 'Benar!'],
                        ['Atribut public, method private', false, 'Salah'],
                        ['Semua public', false, 'Salah'],
                        ['Semua private', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dalam Setter, kita bisa menambahkan logik untuk?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Setter bisa bertindak sebagai "penjaga gerbang" atau penyaring data.',
                    'answers'    => [
                        ['Validasi data (misal: umur tidak boleh negatif)', true, 'Benar!'],
                        ['Menghapus memori RAM', false, 'Salah'],
                        ['Menginstal antivirus', false, 'Salah'],
                        ['Mencetak dokumen', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa hubungan Enkapsulasi dengan keamanan data?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Fokus pada perlindungan integritas internal objek agar tidak rusak.',
                    'answers'    => [
                        ['Melindungi kondisi internal objek dari kerusakan akibat perubahan yang tidak valid', true, 'Benar!'],
                        ['Menghack server orang lain', false, 'Salah'],
                        ['Enkapsulasi tidak ada hubungannya dengan keamanan', false, 'Salah'],
                        ['Membuat data menjadi terenkripsi (AES)', false, 'Beda istilah'],
                    ],
                ],
                [
                    'text'       => 'Jika class A memiliki atribut private x, apakah class B bisa mengakses x secara langsung?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Ingat, private berarti "hanya untuk konsumsi internal class itu sendiri".',
                    'answers'    => [
                        ['Tidak bisa, harus lewat Getter/Setter public', true, 'Benar!'],
                        ['Bisa jika class B adalah teman baik class A', false, 'Salah'],
                        ['Bisa jika class B di file yang sama', false, 'Tetap tidak bisa jika private'],
                        ['Bisa tanpa syarat', false, 'Salah'],
                    ],
                ],
                // HARD (6)
                [
                    'text'       => 'Urutkan tingkat aksesibilitas dari yang PALING LUAS ke PALING SEMPIT:',
                    'type'       => QuestionType::DRAG_AND_DROP->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Pikirkan jangkauan dari "seluruh dunia" (public) ke "hanya satu class" (private).',
                    'answers'    => [
                        ['Public', '1', 'Semua'],
                        ['Protected', '2', 'Package & Subclass'],
                        ['Private', '3', 'Class Internal'],
                    ],
                ],
                [
                    'text'       => 'Apa perbedaan mendasar antara data hiding dan enkapsulasi?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Data hiding adalah tentang visibilitas akses, enkapsulasi adalah tentang penyatuan data dan perilakunya.',
                    'answers'    => [
                        ['Data hiding fokus pada aksesibilitas, enkapsulasi fokus pada pembungkusan', true, 'Benar!'],
                        ['Sama saja tidak ada bedanya', false, 'Salah'],
                        ['Enkapsulasi hanya untuk method', false, 'Salah'],
                        ['Data hiding hanya ada di C++', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dalam Java, sebuah top-level class (class utama) TIDAK BOLEH memiliki modifier?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Class utama harus bisa ditemukan oleh sistem Java meskipun berada di luar folder (package) aslinya.',
                    'answers'    => [
                        ['private atau protected', true, 'Top-level class hanya boleh public atau default'],
                        ['public', false, 'Boleh'],
                        ['default', false, 'Boleh'],
                        ['abstract', false, 'Boleh'],
                    ],
                ],
                [
                    'text'       => 'Apa keuntungan menggunakan Getter untuk mengembalikan salinan objek (copy) daripada referensi aslinya?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Ini mencegah pihak luar mengubah "isi" objek internal kita meskipun mereka punya akses melihatnya.',
                    'answers'    => [
                        ['Mencegah perubahan pada objek internal melalui referensi yang dibagikan', true, 'Ini teknik Deep Copy'],
                        ['Mempercepat jalannya program', false, 'Salah'],
                        ['Menghemat memori', false, 'Malah memboroskan memori tapi lebih aman'],
                        ['Agar kode terlihat lebih rumit', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Prinsip "Least Privilege" dalam enkapsulasi menyarankan agar?',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Berikan hak akses sesedikit mungkin; jika tidak butuh akses publik, maka buatlah menjadi tertutup (private).',
                    'answers'    => [
                        ['Memberikan akses sesempit mungkin', 'Gunakan private jika tidak butuh public'],
                    ],
                ],
                [
                    'text'       => 'Enkapsulasi membantu mencapai "Loose Coupling". Apa maksudnya?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Bayangkan hubungan yang "longgar" agar jika satu bagian rusak atau diubah, bagian lain tidak ikut berantakan.',
                    'answers'    => [
                        ['Mengurangi ketergantungan antar bagian sistem sehingga lebih fleksibel', true, 'Benar!'],
                        ['Mempererat hubungan antar class sehingga sulit dipisahkan', false, 'Itu tight coupling'],
                        ['Menghubungkan program dengan kabel longgar', false, 'Salah'],
                        ['Membuat program tidak membutuhkan internet', false, 'Salah'],
                    ],
                ],
            ],
            'Relasi Antar Class (UML Dasar)' => [
                // BEGINNER (7)
                [
                    'text'       => 'Relasi "is-a" biasanya diimplementasikan dengan?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Hubungan ini menyatakan bahwa sebuah objek ADALAH tipe dari objek lain (misal: Kucing ADALAH Hewan).',
                    'answers'    => [
                        ['Inheritance (Pewarisan)', true, 'Benar!'],
                        ['Composition (Komposisi)', false, 'Itu has-a'],
                        ['Association (Asosiasi)', false, 'Itu relasi umum'],
                        ['Looping', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Relasi "has-a" biasanya diimplementasikan dengan?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Hubungan ini menyatakan bahwa sebuah objek MEMILIKI objek lain sebagai bagian darinya (misal: Mobil MEMILIKI Mesin).',
                    'answers'    => [
                        ['Composition / Aggregation', true, 'Benar!'],
                        ['Inheritance', false, 'Salah'],
                        ['Polymorphism', false, 'Salah'],
                        ['Abstraction', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Jika class Mobil memiliki objek Roda, ini disebut relasi?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Pikirkan apakah Mobil ADALAH Roda atau Mobil MEMILIKI Roda.',
                    'answers'    => [
                        ['Has-a (Memiliki)', true, 'Benar!'],
                        ['Is-a (Adalah)', false, 'Salah, Mobil bukan Roda'],
                        ['Depends-on', false, 'Kurang tepat'],
                        ['Inherits-from', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Relasi di mana satu objek hanya sekadar mengenal objek lain tanpa memilikinya disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Ini adalah hubungan kerja sama paling sederhana antar dua objek yang independen.',
                    'answers'    => [
                        ['Association', true, 'Benar!'],
                        ['Composition', false, 'Salah'],
                        ['Aggregation', false, 'Salah'],
                        ['Inheritance', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dalam UML, garis panah dengan kepala segitiga kosong melambangkan?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Simbol ini menghubungkan class anak yang lebih spesifik ke class induk yang lebih umum.',
                    'answers'    => [
                        ['Inheritance / Generalization', true, 'Benar!'],
                        ['Composition', false, 'Itu belah ketupat hitam'],
                        ['Aggregation', false, 'Itu belah ketupat putih'],
                        ['Dependency', false, 'Itu garis putus-putus'],
                    ],
                ],
                [
                    'text'       => 'Relasi di mana satu objek TIDAK bisa hidup tanpa objek induknya disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Bayangkan hubungan jantung dengan tubuh: jika tubuh mati, jantung tidak bisa hidup mandiri.',
                    'answers'    => [
                        ['Composition', true, 'Benar! (Strong relation)'],
                        ['Aggregation', false, 'Itu relasi lemah'],
                        ['Inheritance', false, 'Salah'],
                        ['Abstraction', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Relasi di mana objek anak bisa tetap hidup meskipun objek induk dihapus disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Bayangkan mahasiswa dalam sebuah kampus: jika kampus ditutup, mahasiswa masih tetap ada.',
                    'answers'    => [
                        ['Aggregation', true, 'Benar! (Weak relation)'],
                        ['Composition', false, 'Itu relasi kuat'],
                        ['Inheritance', false, 'Salah'],
                        ['Dependency', false, 'Salah'],
                    ],
                ],
                // MEDIUM (7)
                [
                    'text'       => 'Apa perbedaan utama antara Aggregation dan Composition?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Fokus pada apakah objek "bagian" memiliki siklus hidup yang bergantung pada induknya.',
                    'answers'    => [
                        ['Kekuatan ikatan kepemilikan (ownership)', true, 'Benar!'],
                        ['Kecepatan eksekusi', false, 'Salah'],
                        ['Bahasa pemrograman yang digunakan', false, 'Salah'],
                        ['Jumlah atribut yang dimiliki', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Jika kita menghapus objek Perpustakaan, dan semua buku di dalamnya juga terhapus, maka itu adalah?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Relasi ini sangat kuat (ownership) sehingga bagian-bagiannya ikut hancur bersama induknya.',
                    'answers'    => [
                        ['Composition', true, 'Benar!'],
                        ['Aggregation', false, 'Salah'],
                        ['Association', false, 'Salah'],
                        ['Generalization', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Jika kita membubarkan Tim, tapi pemainnya masih tetap ada dan bisa pindah ke tim lain, maka itu adalah?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Relasi ini lebih lemah karena objek-objek bagian bisa tetap eksis secara mandiri.',
                    'answers'    => [
                        ['Aggregation', true, 'Benar!'],
                        ['Composition', false, 'Salah'],
                        ['Inheritance', false, 'Salah'],
                        ['Abstraction', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dependency (Ketergantungan) terjadi jika?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Ini adalah hubungan jangka pendek, misalnya sebuah class hanya dipinjam sebagai parameter method.',
                    'answers'    => [
                        ['Satu class menggunakan class lain sebagai parameter di dalam method', true, 'Benar!'],
                        ['Satu class mewarisi atribut class lain', false, 'Itu inheritance'],
                        ['Satu class memiliki atribut bertipe class lain', false, 'Itu association'],
                        ['Dua class tidak saling kenal', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Garis panah putus-putus dalam UML mewakili?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Garis putus-putus melambangkan hubungan yang tidak permanen atau hanya sekadar "memakai".',
                    'answers'    => [
                        ['Dependency', true, 'Benar!'],
                        ['Association', false, 'Salah'],
                        ['Composition', false, 'Salah'],
                        ['Inheritance', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Relasi "Dosen mengajar Mahasiswa" paling tepat digambarkan sebagai?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Dosen dan Mahasiswa saling mengenal dan berinteraksi, namun tidak ada kepemilikan hidup-mati.',
                    'answers'    => [
                        ['Association', true, 'Benar!'],
                        ['Inheritance', false, 'Salah'],
                        ['Composition', false, 'Salah'],
                        ['Aggregation', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Multiplicity dalam relasi menentukan?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Misalnya, menentukan apakah satu dosen bisa mengajar satu atau banyak mahasiswa.',
                    'answers'    => [
                        ['Jumlah objek yang terlibat dalam relasi (misal 1 ke banyak)', true, 'Benar!'],
                        ['Jumlah method dalam class', false, 'Salah'],
                        ['Ukuran memori objek', false, 'Salah'],
                        ['Berapa kali program dijalankan', false, 'Salah'],
                    ],
                ],
                // HARD (6)
                [
                    'text'       => 'Urutkan kekuatan relasi dari yang PALING LEMAH ke PALING KUAT:',
                    'type'       => QuestionType::DRAG_AND_DROP->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Urutkan dari hubungan sekilas (memakai), punya tapi pisah, hingga punya dan menyatu.',
                    'answers'    => [
                        ['Dependency', '1', 'Hanya pakai'],
                        ['Aggregation', '2', 'Punya tapi pisah'],
                        ['Composition', '3', 'Punya dan menyatu'],
                    ],
                ],
                [
                    'text'       => 'Manakah yang lebih disarankan oleh banyak pakar desain: Inheritance atau Composition?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Desain modern cenderung lebih menyukai fleksibilitas daripada hierarki yang kaku.',
                    'answers'    => [
                        ['Favor composition over inheritance', true, 'Prinsip desain modern untuk fleksibilitas'],
                        ['Favor inheritance over composition', false, 'Kurang tepat'],
                        ['Keduanya sama saja tidak ada bedanya', false, 'Salah'],
                        ['Hanya gunakan inheritance saja', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Mengapa Composition dianggap lebih fleksibel daripada Inheritance?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Karena kita bisa mengganti atau memasang komponen baru saat program sedang berjalan, tidak kaku dan terpaku pada silsilah keluarga sejak awal.',
                    'answers'    => [
                        ['Karena relasi dapat diubah secara dinamis saat runtime', true, 'Benar!'],
                        ['Karena kode jadi lebih pendek', false, 'Salah'],
                        ['Karena memori jadi lebih irit', false, 'Salah'],
                        ['Karena tidak perlu membuat objek baru', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Relasi sirkular (Class A butuh B, Class B butuh A) harus dihindari karena?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Hubungan yang saling membutuhkan secara berputar ini membuat kode sulit dipisahkan dan sulit untuk diuji (test).',
                    'answers'    => [
                        ['Menyebabkan tight coupling dan sulit ditest', true, 'Benar!'],
                        ['Menghapus file secara otomatis', false, 'Salah'],
                        ['Mempercepat loading program', false, 'Salah'],
                        ['Membuat program jadi gratis', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dalam Java, relasi "implements" digunakan untuk relasi antara ____ dan ____.',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Relasi ini digunakan ketika sebuah class setuju untuk menjalankan "peraturan" atau janji yang ada di dalam sebuah kontrak.',
                    'answers'    => [
                        ['Class', 'Yang melakukan implementasi'],
                        ['Interface', 'Kontrak yang dijalankan'],
                    ],
                ],
                [
                    'text'       => 'Apa istilah untuk relasi di mana sebuah class mewarisi dari lebih dari satu class induk?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Mewarisi dari banyak induk sekaligus. Di Java, hal ini hanya bisa dilakukan melalui Interface, bukan Class induk langsung.',
                    'answers'    => [
                        ['Multiple Inheritance', true, 'Catatan: Java tidak mendukung ini secara langsung'],
                        ['Single Inheritance', false, 'Salah'],
                        ['Multilevel Inheritance', false, 'Salah'],
                        ['Hierarchical Inheritance', false, 'Salah'],
                    ],
                ],
            ],
            'Inheritance (Pewarisan)' => [
                // BEGINNER (7)
                [
                    'text'       => 'Inheritance memungkinkan kita untuk membuat class baru berdasarkan ____ yang sudah ada.',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Hubungan ini mirip seperti hubungan antara orang tua dan anak.',
                    'answers'    => [
                        ['Class', 'Induk / Base class'],
                    ],
                ],
                [
                    'text'       => 'Class yang mewarisi sifat dari class lain disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Pikirkan tentang "anak" yang mewarisi sifat atau warisan dari orang tuanya.',
                    'answers'    => [
                        ['Subclass / Child class / Derived class', true, 'Benar!'],
                        ['Superclass', false, 'Itu induknya'],
                        ['Main class', false, 'Salah'],
                        ['Inner class', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Keyword apa yang digunakan untuk pewarisan di Java/PHP?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Kata ini dalam bahasa Inggris berarti "memperluas" fungsionalitas dari class induk.',
                    'answers'    => [
                        ['extends', true, 'Benar!'],
                        ['implements', false, 'Untuk interface'],
                        ['inherits', false, 'Salah'],
                        ['using', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Class yang memberikan sifatnya kepada class lain disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Pikirkan tentang "orang tua" yang memberikan warisan kepada anak-anaknya.',
                    'answers'    => [
                        ['Superclass / Parent class / Base class', true, 'Benar!'],
                        ['Subclass', false, 'Itu anaknya'],
                        ['Abstract class', false, 'Salah'],
                        ['Final class', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa keuntungan utama dari Inheritance?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Fokus pada kemampuan untuk menggunakan kembali kode yang sudah ada tanpa menulis ulang.',
                    'answers'    => [
                        ['Code Reusability (menghindari duplikasi kode)', true, 'Benar!'],
                        ['Menghilangkan kebutuhan akan variabel', false, 'Salah'],
                        ['Mempercepat koneksi internet', false, 'Salah'],
                        ['Membuat kode sulit dibaca', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apakah Java mendukung Multiple Inheritance (satu anak banyak bapak)?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Java membatasi ini untuk menghindari kerumitan yang disebut Diamond Problem.',
                    'answers'    => [
                        ['Tidak (hanya Single Inheritance)', true, 'Benar! Untuk menghindari Diamond Problem'],
                        ['Ya (bebas sebanyak-banyaknya)', false, 'Salah'],
                        ['Hanya di hari Minggu', false, 'Salah'],
                        ['Tergantung versinya', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Atribut dengan modifier apa yang bisa diakses oleh subclass tapi tidak oleh class luar?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Modifier ini dirancang khusus untuk menjaga rahasia di dalam "keluarga" (hierarki) class.',
                    'answers'    => [
                        ['protected', true, 'Benar!'],
                        ['private', false, 'Hanya internal class saja'],
                        ['public', false, 'Bisa diakses siapa saja'],
                        ['static', false, 'Beda konteks'],
                    ],
                ],
                // MEDIUM (7)
                [
                    'text'       => 'Apa fungsi dari keyword "super" (atau "parent::" di PHP)?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Keyword ini digunakan untuk menunjuk ke "atas", yaitu ke arah class induk.',
                    'answers'    => [
                        ['Memanggil constructor atau method dari superclass', true, 'Benar!'],
                        ['Menghapus objek subclass', false, 'Salah'],
                        ['Memanggil method dari subclass sendiri', false, 'Itu this'],
                        ['Membuat class baru', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Jika superclass memiliki constructor berparameter, apa yang harus dilakukan subclass?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Subclass wajib memastikan "orang tuanya" lahir dengan data yang lengkap terlebih dahulu.',
                    'answers'    => [
                        ['Memanggil super(...) di baris pertama constructor-nya', true, 'Benar!'],
                        ['Mengabaikan saja', false, 'Akan menyebabkan error'],
                        ['Menghapus constructor superclass', false, 'Salah'],
                        ['Membuat constructor baru tanpa parameter', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa itu "Multilevel Inheritance"?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Bayangkan silsilah keluarga yang memanjang ke bawah: Kakek -> Ayah -> Anak.',
                    'answers'    => [
                        ['Relasi berantai (A diwarisi B, B diwarisi C)', true, 'Benar!'],
                        ['Satu anak punya banyak bapak', false, 'Itu Multiple'],
                        ['Satu bapak punya banyak anak', false, 'Itu Hierarchical'],
                        ['Satu class mewarisi dirinya sendiri', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa itu "Hierarchical Inheritance"?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Bayangkan satu orang tua yang memiliki banyak anak dengan sifat yang berbeda-beda.',
                    'answers'    => [
                        ['Satu bapak memiliki banyak anak class', true, 'Benar!'],
                        ['Satu anak memiliki banyak bapak class', false, 'Itu Multiple'],
                        ['Relasi berantai', false, 'Itu Multilevel'],
                        ['Semua jawaban salah', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Jika kita menulis "final class A", apa dampaknya?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Final artinya akhir atau mutlak; tidak boleh ada lagi kelanjutan atau keturunan darinya.',
                    'answers'    => [
                        ['Class A tidak bisa diwariskan (tidak bisa punya anak)', true, 'Benar!'],
                        ['Class A tidak bisa dibuat objeknya', false, 'Salah'],
                        ['Class A tidak bisa memiliki method', false, 'Salah'],
                        ['Class A otomatis menjadi abstract', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dapatkah kita mengakses atribut private milik superclass dari dalam subclass?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Private tetaplah rahasia pribadi bagi class tersebut, bahkan bagi "anaknya" sendiri.',
                    'answers'    => [
                        ['Tidak bisa secara langsung (harus lewat method public/protected)', true, 'Benar!'],
                        ['Bisa tanpa syarat', false, 'Salah'],
                        ['Bisa jika menggunakan keyword super', false, 'Salah'],
                        ['Hanya jika subclass di file yang sama', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Pernyataan "Setiap Mobil adalah Kendaraan" menggambarkan relasi?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Ingat perbedaan antara hubungan "ADALAH" (is-a) dan hubungan "MEMILIKI" (has-a).',
                    'answers'    => [
                        ['Is-a', true, 'Benar!'],
                        ['Has-a', false, 'Salah'],
                        ['Part-of', false, 'Salah'],
                        ['Member-of', false, 'Salah'],
                    ],
                ],
                // HARD (6)
                [
                    'text'       => 'Urutkan eksekusi constructor saat objek subclass dibuat: [blank_1] -> [blank_2]',
                    'type'       => QuestionType::DRAG_AND_DROP->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Secara logis, orang tua harus ada (lahir) terlebih dahulu sebelum anaknya bisa lahir.',
                    'answers'    => [
                        ['Constructor Superclass', '1', 'Dijalankan dulu'],
                        ['Constructor Subclass', '2', 'Dijalankan setelahnya'],
                    ],
                ],
                [
                    'text'       => 'Apa yang dimaksud dengan "Diamond Problem" dalam pewarisan?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Ambiguitas method jika satu class mewarisi dua class yang punya bapak sama', true, 'Benar! Alasan Multiple Inheritance dilarang'],
                        ['Kesalahan saat memakai berlian dalam kode', false, 'Salah'],
                        ['Bentuk hierarki class yang menyerupai intan', false, 'Salah'],
                        ['Masalah memori saat memakai inheritance', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Inheritance menciptakan ikatan yang sangat kuat antar class. Istilah teknisnya adalah?',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Tight Coupling', 'Keterikatan yang kuat'],
                    ],
                ],
                [
                    'text'       => 'Mengapa kita tidak boleh menggunakan Inheritance hanya untuk sekadar mengambil fungsi (code reuse)?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Karena melanggar prinsip "is-a" dan menyebabkan kekacauan desain', true, 'Benar! Gunakan Komposisi jika bukan relasi "is-a"'],
                        ['Karena akan membuat program lambat', false, 'Salah'],
                        ['Karena memori akan cepat penuh', false, 'Salah'],
                        ['Tidak ada alasan, boleh saja', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dapatkah konstruktor diwariskan?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Tidak, tapi constructor subclass WAJIB memanggil constructor superclass', true, 'Benar!'],
                        ['Ya, otomatis diwariskan', false, 'Salah'],
                        ['Tergantung modifier-nya', false, 'Salah'],
                        ['Hanya jika superclass-nya abstract', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa yang terjadi jika subclass tidak memanggil super() secara eksplisit?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Compiler otomatis menyisipkan super() tanpa parameter', true, 'Benar!'],
                        ['Program akan error kompilasi', false, 'Jika superclass tidak punya default constructor'],
                        ['Constructor superclass tidak akan dijalankan', false, 'Salah'],
                        ['Objek tidak akan memiliki atribut induk', false, 'Salah'],
                    ],
                ],
            ],
            'Overriding dan Overloading' => [
                // BEGINNER (7)
                [
                    'text'       => 'Menulis ulang method milik induk di class anak dengan nama dan parameter yang SAMA disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Istilah ini berarti "menimpa" atau "mengganti" perilaku yang diwariskan.',
                    'answers'    => [
                        ['Method Overriding', true, 'Benar!'],
                        ['Method Overloading', false, 'Parameter harus beda'],
                        ['Method Overlapping', false, 'Salah'],
                        ['Method Overacting', false, 'Lelucon'],
                    ],
                ],
                [
                    'text'       => 'Membuat beberapa method dengan nama yang sama tapi parameter BERBEDA disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Pikirkan tentang memberikan "beban berlebih" pada satu nama method dengan banyak versi input yang berbeda.',
                    'answers'    => [
                        ['Method Overloading', true, 'Benar!'],
                        ['Method Overriding', false, 'Nama dan parameter harus sama'],
                        ['Method Overwriting', false, 'Salah'],
                        ['Method Calling', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Annotation apa yang biasanya digunakan di atas method overriding?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Tanda ini memberi tahu compiler (dan pembaca kode) bahwa kita sengaja ingin mengganti method dari induk.',
                    'answers'    => [
                        ['@Override', true, 'Benar!'],
                        ['@Overwrite', false, 'Salah'],
                        ['@Load', false, 'Salah'],
                        ['@New', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Overloading terjadi di dalam ____ class, sedangkan Overriding terjadi di antara ____ dan ____.',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Overloading adalah persaingan di dalam satu class, sedangkan Overriding melibatkan hubungan orang tua-anak.',
                    'answers'    => [
                        ['Satu', 'Dalam class itu sendiri'],
                        ['Induk', 'Superclass'],
                        ['Anak', 'Subclass'],
                    ],
                ],
                [
                    'text'       => 'Syarat utama Overloading adalah perbedaan pada?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Compiler membedakan satu method dengan lainnya berdasarkan "tanda tangan" (signature) dari inputnya.',
                    'answers'    => [
                        ['Parameter (jumlah atau tipe data)', true, 'Benar!'],
                        ['Nama method', false, 'Nama harus sama'],
                        ['Tipe return saja', false, 'Tidak cukup hanya tipe return'],
                        ['Akses modifier saja', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apakah return type harus sama dalam Overriding?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Anak harus memberikan hasil yang setidaknya setara atau lebih spesifik (sub-type) dari janji orang tuanya.',
                    'answers'    => [
                        ['Ya (atau sub-type dari return type aslinya)', true, 'Benar!'],
                        ['Tidak, bebas', false, 'Salah'],
                        ['Harus void semua', false, 'Salah'],
                        ['Harus int semua', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dapatkah kita melakukan Overriding pada method "static"?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Keyword "static" mengikat method secara permanen pada class, bukan pada objek yang bisa berubah perilakunya.',
                    'answers'    => [
                        ['Tidak (itu disebut Method Hiding, bukan Overriding)', true, 'Benar!'],
                        ['Bisa tanpa masalah', false, 'Salah'],
                        ['Hanya di bahasa C++', false, 'Salah'],
                        ['Tergantung modifier lainnya', false, 'Salah'],
                    ],
                ],
                // MEDIUM (7)
                [
                    'text'       => 'Overloading merupakan contoh dari polimorfisme ____, sedangkan Overriding adalah polimorfisme ____.',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Pikirkan kapan pemilihan method dilakukan: saat kode diperiksa (compile) atau saat kode benar-benar berjalan (runtime).',
                    'answers'    => [
                        ['Statik', 'Compile-time'],
                        ['Dinamis', 'Runtime'],
                    ],
                ],
                [
                    'text'       => 'Apa yang terjadi jika kita mencoba Overriding method yang ditandai sebagai "final"?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Keyword "final" berarti keputusan terakhir yang mutlak dan tidak boleh diganggu gugat atau diganti.',
                    'answers'    => [
                        ['Akan terjadi error kompilasi', true, 'Method final tidak bisa di-override'],
                        ['Program jalan tapi lambat', false, 'Salah'],
                        ['Method tetap terganti', false, 'Salah'],
                        ['Semua jawaban benar', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Bolehkah akses modifier method overriding lebih sempit daripada aslinya (misal public ke private)?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Class anak tidak boleh lebih pelit (lebih tertutup) daripada orang tuanya dalam memberikan akses.',
                    'answers'    => [
                        ['Tidak boleh (tidak bisa mempersempit akses)', true, 'Benar!'],
                        ['Boleh saja', false, 'Salah'],
                        ['Hanya jika diizinkan admin', false, 'Salah'],
                        ['Tergantung return type', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa kegunaan utama dari Method Overloading?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Memungkinkan kita menggunakan satu nama perintah yang sama untuk menangani berbagai jenis atau jumlah data yang masuk.',
                    'answers'    => [
                        ['Memberikan kemudahan bagi user untuk memanggil fungsi yang sama dengan input berbeda', true, 'Benar!'],
                        ['Mempercepat jalannya perulangan', false, 'Salah'],
                        ['Menyembunyikan kode rahasia', false, 'Salah'],
                        ['Menambah jumlah baris kode agar terlihat rajin', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dapatkah kita meng-overload Constructor?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Sering digunakan untuk memberikan berbagai cara alternatif bagi sebuah objek untuk diinisialisasi (dilahirkan).',
                    'answers'    => [
                        ['Ya, sangat sering dilakukan', true, 'Benar!'],
                        ['Tidak bisa', false, 'Salah'],
                        ['Hanya satu kali', false, 'Salah'],
                        ['Hanya jika tidak punya parameter', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Manakah yang BENAR tentang Method Signature?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Ini adalah identitas unik yang digunakan oleh sistem Java untuk mengenali dan membedakan satu method dengan yang lain.',
                    'answers'    => [
                        ['Nama method + daftar parameter', true, 'Benar!'],
                        ['Tipe return + nama method', false, 'Salah'],
                        ['Modifier + nama method', false, 'Salah'],
                        ['Isi kode di dalam method', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Mengapa return type saja tidak cukup untuk membedakan method overloading?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Bayangkan memanggil sebuah fungsi tanpa menyimpan hasilnya; sistem tidak akan tahu versi mana yang harus dipanggil.',
                    'answers'    => [
                        ['Karena compiler akan bingung saat method dipanggil tanpa menampung return value-nya', true, 'Benar!'],
                        ['Karena memori akan cepat penuh', false, 'Salah'],
                        ['Karena aturan internasional pemrograman', false, 'Salah'],
                        ['Bisa kok, hanya Java saja yang tidak mau', false, 'Salah'],
                    ],
                ],
                // HARD (6)
                [
                    'text'       => 'Tentukan hasil overriding: [blank_1] ditentukan saat compile, [blank_2] ditentukan saat runtime.',
                    'type'       => QuestionType::DRAG_AND_DROP->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Reference Type', '1', 'Compile-time'],
                        ['Object Type', '2', 'Runtime'],
                    ],
                ],
                [
                    'text'       => 'Apa istilah teknis untuk "Method Overloading"?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Early Binding / Static Binding', true, 'Benar!'],
                        ['Late Binding / Dynamic Binding', false, 'Itu Overriding'],
                        ['Recursive Binding', false, 'Salah'],
                        ['Loose Binding', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa istilah teknis untuk "Method Overriding"?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Late Binding / Dynamic Binding', true, 'Benar!'],
                        ['Early Binding / Static Binding', false, 'Itu Overloading'],
                        ['Fast Binding', false, 'Salah'],
                        ['Global Binding', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dapatkah method "private" di-override?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Tidak (karena subclass tidak bisa melihat method private induknya)', true, 'Benar!'],
                        ['Bisa jika menggunakan keyword super', false, 'Salah'],
                        ['Bisa jika diletakkan di file yang sama', false, 'Salah'],
                        ['Ya, otomatis', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa konsekuensinya jika kita melakukan overriding tanpa @Override annotation?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Tetap jalan, tapi compiler tidak akan memberi peringatan jika kita salah tulis nama/parameter', true, 'Benar! Berbahaya'],
                        ['Program akan error total', false, 'Salah'],
                        ['Program akan berjalan sangat lambat', false, 'Salah'],
                        ['Komputer akan meledak', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apakah "Method Overriding" WAJIB ada dalam pewarisan?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Tidak wajib, hanya jika subclass butuh perilaku yang berbeda dari induknya', true, 'Benar!'],
                        ['Wajib untuk semua method', false, 'Salah'],
                        ['Hanya wajib untuk method statis', false, 'Salah'],
                        ['Hanya wajib untuk method final', false, 'Salah'],
                    ],
                ],
            ],
            'Abstract Class' => [
                // BEGINNER (7)
                [
                    'text'       => 'Class yang tidak dapat diinstansiasi (dibuat objeknya) secara langsung disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Class ini dianggap "tidak lengkap" atau terlalu umum untuk dijadikan objek nyata.',
                    'answers'    => [
                        ['Abstract Class', true, 'Benar!'],
                        ['Concrete Class', false, 'Bisa diinstansiasi'],
                        ['Static Class', false, 'Salah'],
                        ['Interface', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Keyword apa yang digunakan untuk mendefinisikan class abstrak?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Keyword ini sama dengan nama konsep yang sedang kita pelajari di modul ini.',
                    'answers'    => [
                        ['abstract', true, 'Benar!'],
                        ['virtual', false, 'Biasanya di C++'],
                        ['empty', false, 'Salah'],
                        ['hidden', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Abstract method adalah method yang ____ implementasi (body).',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Hanya ada deklarasi "apa yang harus dilakukan", tanpa isi "bagaimana cara melakukannya".',
                    'answers'    => [
                        ['Tidak memiliki', 'Hanya deklarasi'],
                    ],
                ],
                [
                    'text'       => 'Dapatkah abstract class memiliki method yang SUDAH ada implementasinya?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Abstract class bisa menjadi campuran antara kerangka (abstract) dan fungsionalitas nyata (concrete).',
                    'answers'    => [
                        ['Ya (bisa punya mix antara abstract dan concrete method)', true, 'Benar!'],
                        ['Tidak (harus abstract semua)', false, 'Itu Interface (sebelum Java 8)'],
                        ['Hanya satu method saja', false, 'Salah'],
                        ['Tergantung cuaca', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Class biasa (bukan abstrak) yang mewarisi abstract class disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Ini adalah class nyata yang sudah lengkap fungsionalitasnya sehingga bisa dibuat objeknya.',
                    'answers'    => [
                        ['Concrete Class', true, 'Benar!'],
                        ['Final Class', false, 'Salah'],
                        ['Inherited Class', false, 'Salah'],
                        ['Real Class', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa yang HARUS dilakukan subclass concrete terhadap abstract method induknya?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Subclass harus "menepati janji" atau melengkapi bagian yang masih kosong dari induknya.',
                    'answers'    => [
                        ['Wajib mengimplementasikan (override) semua abstract method tersebut', true, 'Benar!'],
                        ['Boleh mengabaikannya', false, 'Akan menyebabkan error'],
                        ['Wajib menghapusnya', false, 'Salah'],
                        ['Menjadikannya static', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Abstract class digunakan jika kita ingin mendefinisikan sebuah ____ bagi turunannya.',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Pikirkan tentang sebuah "kerangka dasar" yang akan digunakan bersama oleh banyak class lain.',
                    'answers'    => [
                        ['Kerangka (Template / Blueprint umum)', true, 'Benar!'],
                        ['Database', false, 'Salah'],
                        ['Variabel global', false, 'Salah'],
                        ['Looping', false, 'Salah'],
                    ],
                ],
                // MEDIUM (7)
                [
                    'text'       => 'Bolehkah abstract class memiliki atribut (variabel)?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Meskipun class-nya abstrak, ia tetap bisa menyimpan data (state) untuk diwariskan ke anak-anaknya.',
                    'answers'    => [
                        ['Boleh (sama seperti class biasa)', true, 'Benar!'],
                        ['Tidak boleh sama sekali', false, 'Salah'],
                        ['Hanya boleh konstanta', false, 'Salah'],
                        ['Hanya boleh tipe data integer', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Bolehkah abstract class memiliki Constructor?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Constructor ini berguna untuk menginisialisasi atribut induk saat objek class anak dibuat.',
                    'answers'    => [
                        ['Boleh (untuk digunakan oleh subclass via super())', true, 'Benar!'],
                        ['Tidak boleh (karena tidak bisa diinstansiasi)', false, 'Salah'],
                        ['Hanya jika atributnya public', false, 'Salah'],
                        ['Hanya di bahasa PHP', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Jika sebuah class memiliki minimal satu abstract method, maka class tersebut ____.',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Satu saja bagian yang "tidak lengkap" sudah cukup untuk membuat seluruh class menjadi abstrak.',
                    'answers'    => [
                        ['Wajib dideklarasikan sebagai abstract class', true, 'Benar!'],
                        ['Boleh tetap jadi concrete class', false, 'Akan error kompilasi'],
                        ['Otomatis menjadi interface', false, 'Salah'],
                        ['Wajib dihapus', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Kapan kita lebih memilih Abstract Class daripada Interface?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Fokus pada keinginan untuk berbagi kode (implementasi) di antara class-class yang berelasi dekat.',
                    'answers'    => [
                        ['Saat kita ingin berbagi kode (implementasi) antar class yang berelasi erat', true, 'Benar!'],
                        ['Saat kita butuh multiple inheritance', false, 'Gunakan Interface'],
                        ['Saat kita tidak butuh atribut', false, 'Gunakan Interface'],
                        ['Hanya jika kita malas coding', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Manakah yang BENAR tentang penulisan abstract method?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Abstract method tidak boleh memiliki kurung kurawal {}, melainkan langsung diakhiri titik koma (;).',
                    'answers'    => [
                        ['abstract void lari(); (diakhiri titik koma, tanpa kurung kurawal)', true, 'Benar!'],
                        ['abstract void lari() {}', false, 'Salah, tidak boleh ada body'],
                        ['void abstract lari();', false, 'Salah urutan'],
                        ['abstract lari;', false, 'Salah, harus method'],
                    ],
                ],
                [
                    'text'       => 'Abstract class mewakili tingkat ____ yang tinggi dalam hierarki class.',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Ini tentang menyembunyikan detail implementasi yang rumit dan fokus pada konsep umum.',
                    'answers'    => [
                        ['Abstraksi', 'Tingkat abstraksi'],
                    ],
                ],
                [
                    'text'       => 'Dapatkah abstract class mewarisi (extends) abstract class lainnya?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'answers'    => [
                        ['Ya, diperbolehkan', true, 'Benar!'],
                        ['Tidak bisa', false, 'Salah'],
                        ['Hanya satu level saja', false, 'Salah'],
                        ['Hanya jika di file yang berbeda', false, 'Salah'],
                    ],
                ],
                // HARD (6)
                [
                    'text'       => 'Pasangkan konsep: [blank_1] untuk blueprint murni, [blank_2] untuk blueprint dengan sebagian implementasi.',
                    'type'       => QuestionType::DRAG_AND_DROP->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Interface', '1', 'Hanya deklarasi (biasanya)'],
                        ['Abstract Class', '2', 'Campuran'],
                    ],
                ],
                [
                    'text'       => 'Apa yang terjadi jika subclass concrete tidak mengimplementasikan semua abstract method induknya?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Subclass tersebut juga harus dideklarasikan sebagai abstract', true, 'Benar!'],
                        ['Program akan memberikan nilai default null', false, 'Salah'],
                        ['Method akan hilang otomatis', false, 'Salah'],
                        ['Akan terjadi error saat runtime (saat dipanggil)', false, 'Error terjadi saat compile'],
                    ],
                ],
                [
                    'text'       => 'Bolehkah sebuah abstract method ditandai sebagai "private"?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Tidak (karena private tidak bisa di-override, sehingga kontradiksi)', true, 'Benar!'],
                        ['Boleh saja', false, 'Salah'],
                        ['Boleh jika class-nya juga private', false, 'Salah'],
                        ['Hanya di Java versi terbaru', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Bolehkah sebuah abstract method ditandai sebagai "static"?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Tidak (karena static tidak bisa di-override secara dinamis)', true, 'Benar!'],
                        ['Boleh saja', false, 'Salah'],
                        ['Hanya untuk variabel', false, 'Salah'],
                        ['Tergantung IDE-nya', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Mengapa kita tidak bisa membuat objek dari abstract class?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Karena memiliki method yang belum lengkap implementasinya (unfinished)', true, 'Benar!'],
                        ['Karena dilarang oleh sistem operasi', false, 'Salah'],
                        ['Karena akan merusak memori RAM', false, 'Salah'],
                        ['Karena namanya mengandung kata "abstract"', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa perbedaan antara "Abstraction" dan "Abstract Class"?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Abstraction adalah konsep/prinsip, Abstract Class adalah alat implementasinya', true, 'Benar!'],
                        ['Sama saja', false, 'Salah'],
                        ['Abstraction hanya untuk database', false, 'Salah'],
                        ['Abstract class lebih sulit dipahami', false, 'Salah'],
                    ],
                ],
            ],
            'Interface' => [
                // BEGINNER (7)
                [
                    'text'       => 'Interface adalah sebuah "____" yang berisi daftar method tanpa implementasi.',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Pikirkan tentang sebuah perjanjian atau kesepakatan tertulis.',
                    'answers'    => [
                        ['Kontrak', 'Contract'],
                    ],
                ],
                [
                    'text'       => 'Keyword apa yang digunakan oleh sebuah class untuk menggunakan interface?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Kata ini dalam bahasa Inggris berarti "melaksanakan" atau "menerapkan" janji yang tertulis di interface.',
                    'answers'    => [
                        ['implements', true, 'Benar!'],
                        ['extends', false, 'Untuk class'],
                        ['uses', false, 'Salah'],
                        ['with', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apakah sebuah class dapat menggunakan lebih dari satu interface?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Berbeda dengan class induk, Java memperbolehkan sebuah class memiliki banyak "peran" atau "kemampuan" sekaligus.',
                    'answers'    => [
                        ['Ya (mendukung Multiple Inheritance of Type)', true, 'Benar!'],
                        ['Tidak (maksimal satu)', false, 'Itu aturan extends class'],
                        ['Hanya dua', false, 'Salah'],
                        ['Hanya di bahasa PHP', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Secara default, semua method dalam interface (sebelum Java 8) bersifat?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Karena interface adalah standar publik, ia tidak memiliki rahasia dan tidak memiliki isi implementasi.',
                    'answers'    => [
                        ['public dan abstract', true, 'Benar!'],
                        ['private dan static', false, 'Salah'],
                        ['protected dan final', false, 'Salah'],
                        ['default dan concrete', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dapatkah kita membuat objek dari sebuah Interface?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Interface hanyalah daftar kemampuan atau kontrak, bukan benda nyata yang bisa langsung diciptakan.',
                    'answers'    => [
                        ['Tidak bisa', true, 'Interface harus diimplementasikan oleh class'],
                        ['Bisa', false, 'Salah'],
                        ['Bisa jika tidak ada method-nya', false, 'Salah'],
                        ['Bisa di Java versi 21', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Atribut yang dideklarasikan di dalam interface secara otomatis bersifat?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Variabel di sini dianggap sebagai nilai tetap (konstanta) yang bisa diakses langsung tanpa membuat objek.',
                    'answers'    => [
                        ['public static final (konstanta)', true, 'Benar!'],
                        ['private static', false, 'Salah'],
                        ['protected variable', false, 'Salah'],
                        ['global dynamic', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Tujuan utama Interface adalah untuk mencapai?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Interface memastikan semua class yang membelinya memiliki serangkaian cara kerja yang terstandarisasi.',
                    'answers'    => [
                        ['Abstraksi penuh dan standarisasi perilaku', true, 'Benar!'],
                        ['Kecepatan internet', false, 'Salah'],
                        ['Penghematan jumlah baris kode', false, 'Salah'],
                        ['Penyimpanan data permanen', false, 'Salah'],
                    ],
                ],
                // MEDIUM (7)
                [
                    'text'       => 'Apa perbedaan utama antara Interface dan Abstract Class?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Pikirkan tentang batasan jumlah class induk yang bisa diwarisi versus jumlah peran yang bisa dijalankan.',
                    'answers'    => [
                        ['Interface mendukung multiple implementation, Abstract Class tidak (untuk extends)', true, 'Benar!'],
                        ['Interface lebih lambat', false, 'Salah'],
                        ['Abstract class lebih keren', false, 'Salah'],
                        ['Interface tidak bisa punya method', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Sejak Java 8, Interface diperbolehkan memiliki method dengan body menggunakan keyword?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Keyword ini memberikan nilai atau perilaku "bawaan" agar tidak merusak class-class yang sudah ada sebelumnya.',
                    'answers'    => [
                        ['default', true, 'Benar! (Default Methods)'],
                        ['concrete', false, 'Salah'],
                        ['implement', false, 'Salah'],
                        ['body', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Interface tanpa method sama sekali disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Jenis interface ini hanya digunakan untuk memberi "tanda" atau label khusus pada sebuah class.',
                    'answers'    => [
                        ['Marker Interface (misal: Serializable)', true, 'Benar!'],
                        ['Empty Interface', false, 'Salah'],
                        ['Ghost Interface', false, 'Salah'],
                        ['Abstract Interface', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Kapan sebaiknya kita menggunakan Interface?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Gunakan ini jika kamu ingin mendefinisikan "apa yang bisa dilakukan" (perilaku), bukan "siapa dia" (identitas).',
                    'answers'    => [
                        ['Saat kita ingin mendefinisikan "peran" atau "kemampuan" (misal: Flyable, Runnable)', true, 'Benar!'],
                        ['Saat kita ingin mewarisi atribut dari bapak', false, 'Gunakan Inheritance'],
                        ['Saat kita hanya punya satu class saja', false, 'Salah'],
                        ['Saat kita ingin membuat database', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dapatkah sebuah interface mewarisi (extends) interface lain?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Sama seperti class, standar atau kontrak juga bisa diturunkan menjadi kontrak yang lebih spesifik.',
                    'answers'    => [
                        ['Ya, menggunakan keyword extends', true, 'Benar!'],
                        ['Tidak bisa', false, 'Salah'],
                        ['Bisa tapi pakai keyword implements', false, 'Salah'],
                        ['Hanya di bahasa C#', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Jika sebuah class mengimplementasikan interface, tapi tidak mengimplementasikan salah satu method-nya, maka class tersebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'answers'    => [
                        ['Wajib dideklarasikan sebagai abstract', true, 'Benar!'],
                        ['Akan otomatis menghapus method tersebut', false, 'Salah'],
                        ['Akan memberikan output kosong', false, 'Salah'],
                        ['Tetap bisa jadi concrete class', false, 'Akan error kompilasi'],
                    ],
                ],
                [
                    'text'       => 'Hubungan antara class dan interface sering disebut sebagai relasi ____.',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'answers'    => [
                        ['Can-do', 'Menunjukkan kemampuan'],
                    ],
                ],
                // HARD (6)
                [
                    'text'       => 'Urutkan sejarah evolusi Interface di Java: [blank_1] -> [blank_2] -> [blank_3]',
                    'type'       => QuestionType::DRAG_AND_DROP->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Hanya Abstract Method', '1', 'Awal Java'],
                        ['Default & Static Method', '2', 'Java 8'],
                        ['Private Method', '3', 'Java 9'],
                    ],
                ],
                [
                    'text'       => 'Apa keuntungan menggunakan Interface dalam pengembangan perangkat lunak skala besar?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Memungkinkan tim bekerja secara paralel dengan menyepakati kontrak (interface) terlebih dahulu', true, 'Benar!'],
                        ['Menghilangkan bug secara otomatis', false, 'Salah'],
                        ['Memperkecil ukuran file EXE', false, 'Salah'],
                        ['Menghilangkan kebutuhan akan dokumentasi', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa perbedaan antara implementasi interface dan pewarisan class dalam hal keterikatan (coupling)?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Interface menghasilkan Loose Coupling, Pewarisan menghasilkan Tight Coupling', true, 'Benar!'],
                        ['Keduanya sama-sama tight coupling', false, 'Salah'],
                        ['Interface lebih kaku (tight)', false, 'Salah'],
                        ['Tidak ada perbedaan', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dalam Java, sebuah interface bisa memiliki "Functional Interface". Apa syaratnya?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Hanya memiliki satu abstract method', true, 'Benar! (Digunakan untuk Lambda Expression)'],
                        ['Hanya memiliki satu atribut static', false, 'Salah'],
                        ['Memiliki minimal sepuluh method', false, 'Salah'],
                        ['Tidak memiliki method sama sekali', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dapatkah interface memiliki constructor?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Tidak, Interface tidak boleh memiliki constructor', true, 'Benar! Karena tidak punya state (atribut instansi)'],
                        ['Boleh', false, 'Salah'],
                        ['Hanya jika semua method-nya default', false, 'Salah'],
                        ['Tergantung access modifier-nya', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Mengapa penggunaan Interface sangat krusial dalam Unit Testing?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'answers'    => [
                        ['Memungkinkan pembuatan Mock Object (objek tiruan) untuk isolasi kode', true, 'Benar!'],
                        ['Karena mempercepat jalannya test', false, 'Salah'],
                        ['Karena testing tidak bisa dilakukan di class biasa', false, 'Salah'],
                        ['Interface otomatis menjalankan test', false, 'Salah'],
                    ],
                ],
            ],
            'Mastering Polimorfisme: Fleksibilitas Dewa' => [
                // BEGINNER (7)
                [
                    'text'       => 'Polimorfisme berasal dari bahasa Yunani yang berarti "____".',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => '"Poli" berarti banyak, dan "Morf" berhubungan dengan rupa atau bentuk.',
                    'answers'    => [
                        ['Banyak Bentuk', 'Many forms'],
                    ],
                ],
                [
                    'text'       => 'Kemampuan satu referensi objek untuk menunjuk ke berbagai jenis objek turunan disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Konsep ini memungkinkan satu entitas (referensi) untuk memiliki banyak rupa atau perilaku yang berbeda.',
                    'answers'    => [
                        ['Polimorfisme', true, 'Benar!'],
                        ['Enkapsulasi', false, 'Salah'],
                        ['Inheritance', false, 'Salah'],
                        ['Abstraksi', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Contoh nyata polimorfisme adalah referensi tipe "Hewan" yang bisa berisi objek "Kucing" atau "Anjing". Ini disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Pikirkan tentang mengubah pandangan dari sesuatu yang spesifik (Kucing) ke arah yang lebih umum (Hewan).',
                    'answers'    => [
                        ['Upcasting', true, 'Benar!'],
                        ['Downcasting', false, 'Itu sebaliknya'],
                        ['Overloading', false, 'Salah'],
                        ['Overriding', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Manakah yang merupakan bentuk Polimorfisme?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Polimorfisme mencakup kedua cara kita memberikan banyak versi fungsionalitas pada satu nama method yang sama.',
                    'answers'    => [
                        ['Method Overloading dan Method Overriding', true, 'Benar!'],
                        ['Looping dan Condition', false, 'Salah'],
                        ['Array dan String', false, 'Salah'],
                        ['Public dan Private', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa syarat utama agar polimorfisme (overriding) bisa berjalan?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Harus ada hubungan "kekeluargaan" antar class agar satu tipe bisa menyamar menjadi tipe lainnya.',
                    'answers'    => [
                        ['Harus ada relasi Inheritance (Pewarisan)', true, 'Benar!'],
                        ['Harus menggunakan database', false, 'Salah'],
                        ['Harus ada perulangan (loop)', false, 'Salah'],
                        ['Harus di file yang sama', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dinamis Polimorfisme (Overriding) terjadi pada saat?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Keputusan tentang perilaku mana yang akan diambil baru terjadi saat program sedang "hidup" atau berjalan.',
                    'answers'    => [
                        ['Runtime (saat program dijalankan)', true, 'Benar!'],
                        ['Compile-time (saat kode dikompilasi)', false, 'Itu Statis Polimorfisme'],
                        ['Saat komputer dinyalakan', false, 'Salah'],
                        ['Saat mengetik kode', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Statis Polimorfisme (Overloading) terjadi pada saat?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Keputusan tentang versi method mana yang dipanggil sudah ditentukan sejak kode diperiksa oleh sistem (sebelum dijalankan).',
                    'answers'    => [
                        ['Compile-time (saat kode dikompilasi)', true, 'Benar!'],
                        ['Runtime (saat program dijalankan)', false, 'Itu Dinamis Polimorfisme'],
                        ['Saat mendownload program', false, 'Salah'],
                        ['Saat shutdown', false, 'Salah'],
                    ],
                ],
                // MEDIUM (7)
                [
                    'text'       => 'Apa itu "Upcasting"?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Pikirkan tentang "mengangkat derajat" atau memandang sebuah objek dari sudut pandang induknya yang lebih umum.',
                    'answers'    => [
                        ['Mengubah referensi subclass ke superclass', true, 'Benar! (Otomatis)'],
                        ['Mengubah referensi superclass ke subclass', false, 'Itu Downcasting'],
                        ['Menghapus class', false, 'Salah'],
                        ['Mengirim data ke server', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa itu "Downcasting"?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Mengembalikan pandangan kita terhadap objek dari yang umum kembali ke identitas aslinya yang lebih spesifik.',
                    'answers'    => [
                        ['Mengubah referensi superclass kembali ke subclass aslinya', true, 'Benar! (Butuh casting manual)'],
                        ['Mengubah subclass ke superclass', false, 'Itu Upcasting'],
                        ['Menghapus objek subclass', false, 'Salah'],
                        ['Mengambil data dari internet', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Operator apa yang digunakan untuk mengecek tipe asli sebuah objek di Java?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Nama operator ini dalam bahasa Inggris secara literal berarti "merupakan contoh dari".',
                    'answers'    => [
                        ['instanceof', true, 'Benar!'],
                        ['typeof', false, 'Biasanya di JavaScript'],
                        ['is_a', false, 'Salah'],
                        ['typecheck', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Mengapa polimorfisme sangat berguna dalam pembuatan "List" atau "Array"?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Bayangkan sebuah kotak berlabel "Alat Tulis" yang bisa menampung Pensil, Pulpen, dan Spidol sekaligus karena semuanya adalah Alat Tulis.',
                    'answers'    => [
                        ['Kita bisa menyimpan berbagai jenis objek turunan dalam satu koleksi tipe induk', true, 'Benar!'],
                        ['Agar list bisa menampung ribuan data', false, 'Bukan alasan utama'],
                        ['Agar list tidak bisa dihapus', false, 'Salah'],
                        ['Karena aturan internasional pemrograman', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Late Binding adalah nama lain dari?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Pikirkan tentang keputusan yang diambil "terlambat" atau saat program sedang benar-benar berjalan.',
                    'answers'    => [
                        ['Dynamic Polymorphism (Overriding)', true, 'Benar!'],
                        ['Static Polymorphism (Overloading)', false, 'Salah'],
                        ['Early Binding', false, 'Salah'],
                        ['No Binding', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Early Binding adalah nama lain dari?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Pikirkan tentang keputusan yang diambil "awal" saat kode sedang diperiksa oleh sistem (compiler).',
                    'answers'    => [
                        ['Static Polymorphism (Overloading)', true, 'Benar!'],
                        ['Dynamic Polymorphism (Overriding)', false, 'Salah'],
                        ['Late Binding', false, 'Salah'],
                        ['Slow Binding', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa yang terjadi jika kita melakukan downcasting yang salah (objek asli bukan tipe tersebut)?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Sistem akan mengeluarkan error karena kamu mencoba "memaksa" sebuah benda menjadi sesuatu yang bukan aslinya.',
                    'answers'    => [
                        ['Terjadi ClassCastException saat runtime', true, 'Benar!'],
                        ['Program tetap jalan normal', false, 'Salah'],
                        ['Komputer akan restart', false, 'Salah'],
                        ['Tipe objek akan berubah otomatis', false, 'Salah'],
                    ],
                ],
                // HARD (6)
                [
                    'text'       => 'Urutkan pengecekan tipe sebelum downcasting: [blank_1] -> [blank_2]',
                    'type'       => QuestionType::DRAG_AND_DROP->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Selalu "tanyakan" (validasi) tipe objeknya dulu sebelum "memaksa" (casting) konversi tipenya.',
                    'answers'    => [
                        ['Cek dengan instanceof', '1', 'Validasi dulu'],
                        ['Lakukan Casting manual', '2', 'Konversi aman'],
                    ],
                ],
                [
                    'text'       => 'Dapatkah kita memanggil method unik milik Subclass melalui referensi Superclass?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Superclass hanya tahu apa yang dia miliki; dia tidak tahu "fitur tambahan" yang diciptakan oleh anaknya.',
                    'answers'    => [
                        ['Tidak bisa secara langsung (harus di-downcast dulu)', true, 'Benar!'],
                        ['Bisa tanpa syarat', false, 'Salah'],
                        ['Hanya jika method-nya public', false, 'Tetap butuh casting'],
                        ['Hanya jika Superclass-nya abstract', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Polimorfisme memungkinkan kita menulis kode yang bersifat "____", yaitu kode yang bisa bekerja dengan tipe baru di masa depan tanpa harus diubah.',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Kode ini tidak kaku dan bisa menerima berbagai macam tipe baru di masa depan.',
                    'answers'    => [
                        ['Generic / General', 'Dapat menangani berbagai tipe'],
                    ],
                ],
                [
                    'text'       => 'Manakah yang BENAR tentang keuntungan polimorfisme?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Fokus pada kemudahan dalam menambah fitur atau class baru tanpa harus merombak kode yang sudah ada.',
                    'answers'    => [
                        ['Meningkatkan fleksibilitas dan ekstensibilitas kode', true, 'Benar!'],
                        ['Mengurangi penggunaan memori hingga 50%', false, 'Salah'],
                        ['Menghilangkan kebutuhan akan inheritance', false, 'Salah'],
                        ['Menyembunyikan logic program dari pengembang lain', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Jika class A mewarisi B, dan kita menulis "B obj = new A();", method mana yang akan dijalankan jika dipanggil obj.test() (asumsi test() di-override di A)?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Ingat bahwa identitas asli objek tersebut adalah A, meskipun kita sedang melihatnya sebagai B.',
                    'answers'    => [
                        ['Method milik class A (subclass)', true, 'Benar! (Late Binding)'],
                        ['Method milik class B (superclass)', false, 'Salah'],
                        ['Akan error', false, 'Salah'],
                        ['Keduanya akan dijalankan', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Prinsip desain "Dependency Inversion" (D dari SOLID) sangat bergantung pada?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Prinsip ini menyarankan agar kita bergantung pada "janji" (abstraksi), bukan pada "benda nyata" (konkret).',
                    'answers'    => [
                        ['Polimorfisme dan Interface/Abstraksi', true, 'Benar!'],
                        ['Enkapsulasi dan Private variable', false, 'Salah'],
                        ['Looping dan Recursion', false, 'Salah'],
                        ['Semua jawaban salah', false, 'Salah'],
                    ],
                ],
            ],
            'Proyek Akhir: Arsitektur Sistem Terintegrasi' => [
                // BEGINNER (7)
                [
                    'text'       => 'Langkah pertama dalam membangun sistem besar adalah?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Sebelum menulis kode, kita harus tahu apa yang ingin kita buat dan objek apa saja yang terlibat.',
                    'answers'    => [
                        ['Analisis Kebutuhan dan Identifikasi Objek', true, 'Benar!'],
                        ['Langsung mengetik kode', false, 'Salah'],
                        ['Membeli server mahal', false, 'Salah'],
                        ['Menginstal Windows', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Arsitektur sistem yang baik harus bersifat "____", artinya mudah dikembangkan di masa depan.',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Sistem harus mampu "tumbuh" lebih besar seiring dengan bertambahnya jumlah pengguna atau data.',
                    'answers'    => [
                        ['Scalable / Scalability', 'Skalabilitas'],
                    ],
                ],
                [
                    'text'       => 'Dalam proyek besar, penggunaan ____ sangat membantu dalam mengelola paket dan library.',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Alat ini secara otomatis mengunduh dan mengatur semua perpustakaan kode (library) yang dibutuhkan oleh proyekmu.',
                    'answers'    => [
                        ['Package Manager (Composer/NPM/Maven)', true, 'Benar!'],
                        ['Notepad', false, 'Salah'],
                        ['Kalkulator', false, 'Salah'],
                        ['Browser', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Manakah pilar OOP yang paling krusial untuk menjaga integritas data sistem?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Fokus pada perlindungan data agar tidak bisa diakses atau diubah secara sembarangan dari luar class.',
                    'answers'    => [
                        ['Encapsulation', true, 'Benar!'],
                        ['Inheritance', false, 'Salah'],
                        ['Polymorphism', false, 'Salah'],
                        ['Abstraction', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dokumentasi kode yang baik biasanya menggunakan standar?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Ini adalah format komentar khusus yang dapat secara otomatis diubah menjadi halaman web dokumentasi yang rapi.',
                    'answers'    => [
                        ['Docblock (JavaDoc/PHPDoc)', true, 'Benar!'],
                        ['Microsoft Word', false, 'Salah'],
                        ['Tulisan tangan', false, 'Salah'],
                        ['Status WhatsApp', false, 'Lelucon'],
                    ],
                ],
                [
                    'text'       => 'Versi kontrol (seperti Git) penting untuk?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Pikirkan tentang "mesin waktu" untuk kode dan alat utama untuk bekerja bersama dalam sebuah tim.',
                    'answers'    => [
                        ['Melacak perubahan kode dan kolaborasi tim', true, 'Benar!'],
                        ['Mempercepat loading game', false, 'Salah'],
                        ['Menghapus virus', false, 'Salah'],
                        ['Semua jawaban salah', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Sebuah class yang isinya hanya untuk menyimpan data (POJO/DTO) disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::BEGINNER->value,
                    'hint'       => 'Class ini sangat sederhana, biasanya hanya berisi atribut (field), getter, dan setter saja.',
                    'answers'    => [
                        ['Data Class', true, 'Benar!'],
                        ['Logic Class', false, 'Salah'],
                        ['Utility Class', false, 'Salah'],
                        ['Main Class', false, 'Salah'],
                    ],
                ],
                // MEDIUM (7)
                [
                    'text'       => 'Design Pattern adalah ____.',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Pikirkan tentang "cetak biru solusi" untuk masalah pemrograman yang berulang.',
                    'answers'    => [
                        ['Solusi umum untuk masalah yang sering muncul dalam desain software', true, 'Benar!'],
                        ['Template desain website (HTML/CSS)', false, 'Salah'],
                        ['Algoritma sorting tercepat', false, 'Salah'],
                        ['Gambar skema jaringan', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Pattern yang memastikan sebuah class hanya memiliki SATU instansi disebut?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Namanya berasal dari kata dalam bahasa Inggris yang berarti "sendirian" atau "tunggal".',
                    'answers'    => [
                        ['Singleton', true, 'Benar!'],
                        ['Factory', false, 'Salah'],
                        ['Observer', false, 'Salah'],
                        ['Strategy', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Pattern "Factory" digunakan untuk?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Sesuai namanya (Pabrik), pattern ini bertugas untuk "menciptakan" objek sesuai dengan permintaan.',
                    'answers'    => [
                        ['Membuat objek tanpa harus menentukan class konkretnya secara eksplisit', true, 'Benar!'],
                        ['Mengirim email massal', false, 'Salah'],
                        ['Menghubungkan ke database SQL', false, 'Salah'],
                        ['Mengatur tampilan UI', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Mengapa kita harus memisahkan "Logic" dan "UI" dalam aplikasi?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Jangan mencampurkan "otak" aplikasi dengan "tampilan" aplikasinya agar kode tidak berantakan.',
                    'answers'    => [
                        ['Agar kode lebih mudah ditest dan dipelihara (Separation of Concerns)', true, 'Benar!'],
                        ['Agar program lebih warna-warni', false, 'Salah'],
                        ['Agar ukuran file lebih kecil', false, 'Salah'],
                        ['Karena perintah dosen', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Unit Testing dilakukan untuk?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Tujuannya adalah memastikan setiap "baut" dan "mur" (bagian terkecil) dari kode kita bekerja dengan benar.',
                    'answers'    => [
                        ['Menguji bagian terkecil program (class/method) secara terisolasi', true, 'Benar!'],
                        ['Menguji kesabaran pengguna', false, 'Salah'],
                        ['Menguji kecepatan internet', false, 'Salah'],
                        ['Menguji ketahanan hardware', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'SOLID adalah akronim dari prinsip desain software. S-nya adalah?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Prinsip ini menyatakan bahwa satu class hanya boleh memiliki "satu alasan" untuk diubah atau satu tugas utama.',
                    'answers'    => [
                        ['Single Responsibility Principle', true, 'Satu class hanya punya satu tanggung jawab'],
                        ['Static Return Type', false, 'Salah'],
                        ['Simple Object Logic', false, 'Salah'],
                        ['Superclass Optimization', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Relasi antar modul yang minim (Loose Coupling) sangat baik karena?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::MEDIUM->value,
                    'hint'       => 'Bayangkan modul sebagai kabel yang mudah dicopot dan diganti tanpa harus merusak seluruh perangkat elektronik.',
                    'answers'    => [
                        ['Perubahan di satu modul tidak merusak modul lain', true, 'Benar!'],
                        ['Modul bisa dicuri lebih mudah', false, 'Salah'],
                        ['Program jadi tidak butuh memori', false, 'Salah'],
                        ['Semua jawaban benar', false, 'Salah'],
                    ],
                ],
                // HARD (6)
                [
                    'text'       => 'Urutkan tahapan pengembangan software (SDLC) secara umum: [blank_1] -> [blank_2] -> [blank_3]',
                    'type'       => QuestionType::DRAG_AND_DROP->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Pikirkan urutan logis dari: Berencana (Plan) -> Bertindak (Do) -> Memeriksa (Check).',
                    'answers'    => [
                        ['Perencanaan & Desain', '1', 'Fase awal'],
                        ['Implementasi (Coding)', '2', 'Fase eksekusi'],
                        ['Testing & Deployment', '3', 'Fase akhir'],
                    ],
                ],
                [
                    'text'       => 'Apa itu "Refactoring"?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Membereskan "kamar" (kode) agar lebih rapi dan efisien tanpa mengubah fungsi benda-benda di dalamnya.',
                    'answers'    => [
                        ['Memperbaiki struktur kode tanpa mengubah perilaku eksternalnya', true, 'Benar!'],
                        ['Menambah fitur baru sebanyak mungkin', false, 'Salah'],
                        ['Menghapus semua kode dan mulai dari awal', false, 'Salah'],
                        ['Mengubah bahasa pemrograman tengah jalan', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dependency Injection (DI) bertujuan untuk?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Memasukkan "kebutuhan" sebuah class dari luar, bukan membiarkan class tersebut membuatnya sendiri di dalam.',
                    'answers'    => [
                        ['Menghilangkan ketergantungan keras (hardcoded) antar class', true, 'Benar!'],
                        ['Menyuntikkan virus ke program', false, 'Salah'],
                        ['Menambah jumlah memori', false, 'Salah'],
                        ['Membuat program lebih lambat', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Apa perbedaan antara Monolithic dan Microservices architecture secara singkat?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Bayangkan perbedaan antara satu gedung apartemen raksasa (Monolith) dengan sekumpulan rumah-rumah kecil yang saling terhubung (Microservices).',
                    'answers'    => [
                        ['Monolith satu kesatuan besar, Microservices terbagi-bagi jadi service kecil', true, 'Benar!'],
                        ['Monolith lebih modern', false, 'Salah'],
                        ['Microservices hanya untuk mobile', false, 'Salah'],
                        ['Keduanya sama saja', false, 'Salah'],
                    ],
                ],
                [
                    'text'       => 'Dalam desain OOP, istilah "Composition over Inheritance" menyarankan agar?',
                    'type'       => QuestionType::FILL_IN_THE_BLANK->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Pikirkan tentang fleksibilitas dalam merakit berbagai komponen (has-a) daripada terikat pada silsilah keluarga yang kaku (is-a).',
                    'answers'    => [
                        ['Lebih mengutamakan relasi has-a', 'Untuk fleksibilitas'],
                    ],
                ],
                [
                    'text'       => 'Apa dampak dari "Technical Debt" (Hutang Teknis)?',
                    'type'       => QuestionType::RADIO_BUTTON->value,
                    'difficulty' => QuestionDifficulty::HARD->value,
                    'hint'       => 'Seperti hutang uang, jika desain yang buruk tidak segera "dibayar" (diperbaiki), maka "bunganya" (kerumitan) akan semakin memberatkan di masa depan.',
                    'answers'    => [
                        ['Biaya pemeliharaan yang semakin mahal di masa depan akibat desain buruk', true, 'Benar!'],
                        ['Hutang uang di bank', false, 'Salah'],
                        ['Program jadi semakin cepat', false, 'Salah'],
                        ['Tidak ada dampak apa-apa', false, 'Salah'],
                    ],
                ],
            ],
        ];

        if ($title === 'Inheritance: Pewarisan dan Kata Kunci Super') {
            return $allQuestions['Inheritance (Pewarisan)'] ?? [];
        }

        if ($title === 'Polimorfisme: Override dan Overload') {
            return array_merge(
                $allQuestions['Mastering Polimorfisme: Fleksibilitas Dewa'] ?? [],
                $allQuestions['Overriding dan Overloading']                 ?? [],
            );
        }

        if ($title === 'Struktur Dasar: Class & Object Java') {
            return $allQuestions['Anatomi Class & Object'] ?? [];
        }

        if ($title === 'Abstraksi dan Interface: Kontrak Standarisasi') {
            return array_merge(
                $allQuestions['Abstract Class'] ?? [],
                $allQuestions['Interface']      ?? [],
            );
        }

        if ($title === 'Relasi Antar Class (UML Dasar)') {
            return array_merge(
                $allQuestions['Relasi Antar Class (UML Dasar)']               ?? [],
                $allQuestions['Proyek Akhir: Arsitektur Sistem Terintegrasi'] ?? [],
            );
        }

        return $allQuestions[$title] ?? [];
    }
}
