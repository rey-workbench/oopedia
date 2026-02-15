<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Answer;

class AnswersSeeder extends Seeder
{
    public function run(): void
    {
        $answers = [
            // ==================== QUESTION 1: Apa yang dimaksud dengan PBO? (radio_button) ====================
            ['question_id' => 1, 'is_correct' => true, 'answer_text' => 'Paradigma pemrograman berdasarkan objek', 'explanation' => 'Benar! PBO didasarkan pada konsep objek yang berisi data dan metode.'],
            ['question_id' => 1, 'is_correct' => false, 'answer_text' => 'Jenis database', 'explanation' => 'Salah. PBO adalah paradigma pemrograman, bukan jenis database.'],
            ['question_id' => 1, 'is_correct' => false, 'answer_text' => 'Bahasa markup', 'explanation' => 'Salah. PBO bukan bahasa markup seperti HTML.'],

            // ==================== QUESTION 2: Apa kepanjangan dari OOP? (radio_button) ====================
            ['question_id' => 2, 'is_correct' => true, 'answer_text' => 'Object-Oriented Programming', 'explanation' => 'Benar!'],
            ['question_id' => 2, 'is_correct' => false, 'answer_text' => 'Object-Oriented Process', 'explanation' => 'Salah.'],
            ['question_id' => 2, 'is_correct' => false, 'answer_text' => 'Organized Object Programming', 'explanation' => 'Salah.'],

            // ==================== QUESTION 3: Manakah yang BUKAN prinsip PBO? (radio_button) ====================
            ['question_id' => 3, 'is_correct' => true, 'answer_text' => 'Globalisasi', 'explanation' => 'Benar! Globalisasi bukan prinsip PBO.'],
            ['question_id' => 3, 'is_correct' => false, 'answer_text' => 'Enkapsulasi', 'explanation' => 'Salah. Enkapsulasi adalah prinsip inti PBO.'],
            ['question_id' => 3, 'is_correct' => false, 'answer_text' => 'Pewarisan', 'explanation' => 'Salah. Pewarisan adalah prinsip inti PBO.'],
            ['question_id' => 3, 'is_correct' => false, 'answer_text' => 'Polimorfisme', 'explanation' => 'Salah. Polimorfisme adalah prinsip inti PBO.'],

            // ==================== QUESTION 4: PBO kelompokkan data dan _____ (fill_in_the_blank) ====================
            ['question_id' => 4, 'is_correct' => true, 'answer_text' => 'metode', 'blank_position' => 1, 'explanation' => 'Benar! PBO mengelompokkan data dan metode.'],
            ['question_id' => 4, 'is_correct' => true, 'answer_text' => 'metoda', 'blank_position' => 1, 'explanation' => 'Benar! Ejaan alternatif dari metode.'],
            ['question_id' => 4, 'is_correct' => true, 'answer_text' => 'perilaku', 'blank_position' => 1, 'explanation' => 'Benar! Perilaku adalah istilah lain untuk metode.'],
            ['question_id' => 4, 'is_correct' => true, 'answer_text' => 'fungsi', 'blank_position' => 1, 'explanation' => 'Benar! Fungsi adalah istilah umum untuk metode.'],

            // ==================== QUESTION 5: Bahasa pemrograman yang mendukung PBO (radio_button) ====================
            ['question_id' => 5, 'is_correct' => true, 'answer_text' => 'Java, C++, Python', 'explanation' => 'Benar! Semua bahasa ini mendukung PBO.'],
            ['question_id' => 5, 'is_correct' => false, 'answer_text' => 'Hanya Java', 'explanation' => 'Salah. Banyak bahasa yang mendukung PBO.'],
            ['question_id' => 5, 'is_correct' => false, 'answer_text' => 'HTML dan CSS', 'explanation' => 'Salah. HTML dan CSS bukan bahasa pemrograman.'],

            // ==================== QUESTION 6: Prinsip keempat PBO (fill_in_the_blank) ====================
            ['question_id' => 6, 'is_correct' => true, 'answer_text' => 'Polimorfisme', 'blank_position' => 1, 'explanation' => 'Benar! Empat pilar PBO: Enkapsulasi, Abstraksi, Pewarisan, dan Polimorfisme.'],

            // ==================== QUESTION 7: Apa itu kelas dalam PBO? (radio_button) ====================
            ['question_id' => 7, 'is_correct' => true, 'answer_text' => 'Cetak biru untuk membuat objek', 'explanation' => 'Benar! Kelas mendefinisikan struktur untuk objek.'],
            ['question_id' => 7, 'is_correct' => false, 'answer_text' => 'Perwujudan dari objek', 'explanation' => 'Salah. Objek adalah perwujudan kelas, bukan sebaliknya.'],
            ['question_id' => 7, 'is_correct' => false, 'answer_text' => 'Sebuah fungsi', 'explanation' => 'Salah. Kelas bisa berisi fungsi, tapi bukan fungsi itu sendiri.'],

            // ==================== QUESTION 8: Kelas adalah _____ untuk membuat objek (fill_in_the_blank) ====================
            ['question_id' => 8, 'is_correct' => true, 'answer_text' => 'cetak biru', 'blank_position' => 1, 'explanation' => 'Benar! Kelas adalah cetak biru.'],
            ['question_id' => 8, 'is_correct' => true, 'answer_text' => 'template', 'blank_position' => 1, 'explanation' => 'Benar! Template adalah istilah lain untuk cetak biru.'],
            ['question_id' => 8, 'is_correct' => true, 'answer_text' => 'blueprint', 'blank_position' => 1, 'explanation' => 'Benar! Blueprint (bahasa Inggris) juga diterima.'],

            // ==================== QUESTION 9: Apa itu objek dalam PBO? (radio_button) ====================
            ['question_id' => 9, 'is_correct' => true, 'answer_text' => 'Perwujudan (instance) dari sebuah kelas', 'explanation' => 'Benar! Objek dibuat dari template kelas.'],
            ['question_id' => 9, 'is_correct' => false, 'answer_text' => 'Cetak biru', 'explanation' => 'Salah. Kelas adalah cetak biru, objek adalah perwujudannya.'],
            ['question_id' => 9, 'is_correct' => false, 'answer_text' => 'Sebuah metode', 'explanation' => 'Salah. Metode adalah fungsi dalam kelas.'],

            // ==================== QUESTION 10: Kata kunci untuk membuat objek di Java (radio_button) ====================
            ['question_id' => 10, 'is_correct' => true, 'answer_text' => 'new', 'explanation' => 'Benar! Kata kunci "new" digunakan untuk membuat objek baru.'],
            ['question_id' => 10, 'is_correct' => false, 'answer_text' => 'create', 'explanation' => 'Salah. Java menggunakan kata kunci "new".'],
            ['question_id' => 10, 'is_correct' => false, 'answer_text' => 'object', 'explanation' => 'Salah. "object" bukan kata kunci untuk membuat objek.'],

            // ==================== QUESTION 11: Urutkan langkah membuat dan menggunakan objek (drag_and_drop) ====================
            ['question_id' => 11, 'is_correct' => true, 'answer_text' => 'Definisikan kelas', 'drag_target' => '1', 'explanation' => 'Langkah 1: Pertama definisikan kelas.'],
            ['question_id' => 11, 'is_correct' => true, 'answer_text' => 'Buat objek (instansiasi)', 'drag_target' => '2', 'explanation' => 'Langkah 2: Buat objek dari kelas.'],
            ['question_id' => 11, 'is_correct' => true, 'answer_text' => 'Gunakan metode objek', 'drag_target' => '3', 'explanation' => 'Langkah 3: Gunakan objek yang telah dibuat.'],

            // ==================== QUESTION 12: Dapatkah kelas memiliki banyak objek? (radio_button) ====================
            ['question_id' => 12, 'is_correct' => true, 'answer_text' => 'Ya', 'explanation' => 'Benar! Satu kelas bisa punya banyak objek (instance).'],
            ['question_id' => 12, 'is_correct' => false, 'answer_text' => 'Tidak', 'explanation' => 'Salah. Kelas bisa memiliki banyak objek.'],

            // ==================== QUESTION 13: Apa itu konstruktor? (radio_button) ====================
            ['question_id' => 13, 'is_correct' => true, 'answer_text' => 'Metode khusus untuk inisialisasi objek', 'explanation' => 'Benar! Konstruktor menginisialisasi objek saat dibuat.'],
            ['question_id' => 13, 'is_correct' => false, 'answer_text' => 'Metode biasa', 'explanation' => 'Salah. Konstruktor adalah metode khusus.'],
            ['question_id' => 13, 'is_correct' => false, 'answer_text' => 'Sebuah variabel', 'explanation' => 'Salah. Konstruktor adalah metode, bukan variabel.'],

            // ==================== QUESTION 14: Metode _____ dipanggil otomatis saat objek dibuat (fill_in_the_blank) ====================
            ['question_id' => 14, 'is_correct' => true, 'answer_text' => 'konstruktor', 'blank_position' => 1, 'explanation' => 'Benar! Konstruktor dipanggil otomatis.'],
            ['question_id' => 14, 'is_correct' => true, 'answer_text' => 'constructor', 'blank_position' => 1, 'explanation' => 'Benar! Constructor (bahasa Inggris) juga diterima.'],

            // ==================== QUESTION 15: Apa itu atribut dalam kelas? (radio_button) ====================
            ['question_id' => 15, 'is_correct' => true, 'answer_text' => 'Variabel data dalam sebuah kelas', 'explanation' => 'Benar! Atribut adalah variabel yang menyimpan data objek.'],
            ['question_id' => 15, 'is_correct' => false, 'answer_text' => 'Metode kelas', 'explanation' => 'Salah. Metode adalah fungsi, bukan atribut.'],
            ['question_id' => 15, 'is_correct' => false, 'answer_text' => 'Nama kelas', 'explanation' => 'Salah. Nama kelas berbeda dengan atribut.'],

            // ==================== QUESTION 16: Apa itu pewarisan dalam PBO? (radio_button) ====================
            ['question_id' => 16, 'is_correct' => true, 'answer_text' => 'Memperoleh properti dari kelas induk', 'explanation' => 'Benar! Pewarisan memungkinkan kelas anak mewarisi dari induk.'],
            ['question_id' => 16, 'is_correct' => false, 'answer_text' => 'Membuat objek baru', 'explanation' => 'Salah. Itu adalah instansiasi, bukan pewarisan.'],
            ['question_id' => 16, 'is_correct' => false, 'answer_text' => 'Menyembunyikan data', 'explanation' => 'Salah. Itu adalah enkapsulasi.'],

            // ==================== QUESTION 17: Kelas yang mewarisi disebut kelas _____ (fill_in_the_blank) ====================
            ['question_id' => 17, 'is_correct' => true, 'answer_text' => 'anak', 'blank_position' => 1, 'explanation' => 'Benar! Kelas anak mewarisi dari kelas induk.'],
            ['question_id' => 17, 'is_correct' => true, 'answer_text' => 'turunan', 'blank_position' => 1, 'explanation' => 'Benar! Kelas turunan adalah istilah lain.'],
            ['question_id' => 17, 'is_correct' => true, 'answer_text' => 'child', 'blank_position' => 1, 'explanation' => 'Benar! Child class (bahasa Inggris).'],
            ['question_id' => 17, 'is_correct' => true, 'answer_text' => 'subclass', 'blank_position' => 1, 'explanation' => 'Benar! Subclass adalah istilah teknis.'],

            // ==================== QUESTION 18: Kelas induk juga dikenal sebagai (radio_button) ====================
            ['question_id' => 18, 'is_correct' => true, 'answer_text' => 'Superclass atau Base class', 'explanation' => 'Benar! Kelas induk disebut superclass atau base class.'],
            ['question_id' => 18, 'is_correct' => false, 'answer_text' => 'Subclass', 'explanation' => 'Salah. Subclass adalah kelas anak.'],
            ['question_id' => 18, 'is_correct' => false, 'answer_text' => 'Interface', 'explanation' => 'Salah. Interface berbeda dengan kelas induk.'],

            // ==================== QUESTION 19: Kata kunci untuk pewarisan di Java (fill_in_the_blank) ====================
            ['question_id' => 19, 'is_correct' => true, 'answer_text' => 'extends', 'blank_position' => 1, 'explanation' => 'Benar! Kata kunci "extends" digunakan untuk pewarisan di Java.'],

            // ==================== QUESTION 20: Kata kunci untuk memanggil konstruktor induk (fill_in_the_blank) ====================
            ['question_id' => 20, 'is_correct' => true, 'answer_text' => 'super', 'blank_position' => 1, 'explanation' => 'Benar! Kata kunci "super" memanggil konstruktor induk.'],

            // ==================== QUESTION 21: Dapatkah subclass mengakses anggota privat superclass? (radio_button) ====================
            ['question_id' => 21, 'is_correct' => true, 'answer_text' => 'Tidak', 'explanation' => 'Benar! Anggota privat tidak dapat diakses oleh subclass.'],
            ['question_id' => 21, 'is_correct' => false, 'answer_text' => 'Ya', 'explanation' => 'Salah. Gunakan protected untuk akses subclass.'],

            // ==================== QUESTION 22: Urutkan hierarki pewarisan (drag_and_drop) ====================
            ['question_id' => 22, 'is_correct' => true, 'answer_text' => 'Kelas Induk (Base)', 'drag_target' => '1', 'explanation' => 'Paling atas dalam hierarki.'],
            ['question_id' => 22, 'is_correct' => true, 'answer_text' => 'Kelas Menengah', 'drag_target' => '2', 'explanation' => 'Di tengah hierarki.'],
            ['question_id' => 22, 'is_correct' => true, 'answer_text' => 'Kelas Turunan (Derived)', 'drag_target' => '3', 'explanation' => 'Paling bawah dalam hierarki.'],

            // ==================== QUESTION 23: Jenis pewarisan yang TIDAK didukung Java (radio_button) ====================
            ['question_id' => 23, 'is_correct' => true, 'answer_text' => 'Multiple inheritance (Pewarisan Berganda)', 'explanation' => 'Benar! Java tidak mendukung pewarisan berganda untuk kelas.'],
            ['question_id' => 23, 'is_correct' => false, 'answer_text' => 'Single inheritance', 'explanation' => 'Salah. Java mendukung pewarisan tunggal.'],
            ['question_id' => 23, 'is_correct' => false, 'answer_text' => 'Multilevel inheritance', 'explanation' => 'Salah. Java mendukung pewarisan bertingkat.'],

            // ==================== QUESTION 24: Apa itu polimorfisme? (radio_button) ====================
            ['question_id' => 24, 'is_correct' => true, 'answer_text' => 'Banyak bentuk dari satu entitas', 'explanation' => 'Benar! Polimorfisme = banyak bentuk.'],
            ['question_id' => 24, 'is_correct' => false, 'answer_text' => 'Menyembunyikan data', 'explanation' => 'Salah. Itu adalah enkapsulasi.'],
            ['question_id' => 24, 'is_correct' => false, 'answer_text' => 'Penggunaan kode kembali', 'explanation' => 'Salah. Itu lebih ke pewarisan.'],

            // ==================== QUESTION 25: Polimorfisme memungkinkan objek diperlakukan sebagai kelas _____ (fill_in_the_blank) ====================
            ['question_id' => 25, 'is_correct' => true, 'answer_text' => 'super', 'blank_position' => 1, 'explanation' => 'Benar! Objek bisa diperlakukan sebagai kelas super.'],
            ['question_id' => 25, 'is_correct' => true, 'answer_text' => 'induk', 'blank_position' => 1, 'explanation' => 'Benar! Kelas induk adalah istilah Indonesia.'],
            ['question_id' => 25, 'is_correct' => true, 'answer_text' => 'parent', 'blank_position' => 1, 'explanation' => 'Benar! Parent class juga diterima.'],

            // ==================== QUESTION 26: Jenis polimorfisme yang diselesaikan waktu kompilasi (radio_button) ====================
            ['question_id' => 26, 'is_correct' => true, 'answer_text' => 'Method Overloading', 'explanation' => 'Benar! Overloading adalah polimorfisme waktu kompilasi.'],
            ['question_id' => 26, 'is_correct' => false, 'answer_text' => 'Method Overriding', 'explanation' => 'Salah. Overriding adalah polimorfisme waktu jalan (runtime).'],
            ['question_id' => 26, 'is_correct' => false, 'answer_text' => 'Enkapsulasi', 'explanation' => 'Salah. Enkapsulasi bukan jenis polimorfisme.'],

            // ==================== QUESTION 27: Method _____ memungkinkan nama sama parameter beda (fill_in_the_blank) ====================
            ['question_id' => 27, 'is_correct' => true, 'answer_text' => 'overloading', 'blank_position' => 1, 'explanation' => 'Benar! Method overloading memungkinkan nama sama dengan parameter berbeda.'],

            // ==================== QUESTION 28: Apa itu method overriding? (radio_button) ====================
            ['question_id' => 28, 'is_correct' => true, 'answer_text' => 'Mendefinisikan ulang metode induk di kelas anak', 'explanation' => 'Benar! Overriding adalah redefinisi metode induk.'],
            ['question_id' => 28, 'is_correct' => false, 'answer_text' => 'Nama metode sama parameter beda', 'explanation' => 'Salah. Itu adalah overloading.'],
            ['question_id' => 28, 'is_correct' => false, 'answer_text' => 'Menambah metode baru', 'explanation' => 'Salah. Overriding bukan menambah metode baru.'],

            // ==================== QUESTION 29: Anotasi untuk overriding di Java (radio_button) ====================
            ['question_id' => 29, 'is_correct' => true, 'answer_text' => '@Override', 'explanation' => 'Benar! Anotasi @Override digunakan untuk overriding.'],
            ['question_id' => 29, 'is_correct' => false, 'answer_text' => '@Overload', 'explanation' => 'Salah. Tidak ada anotasi @Overload di Java.'],
            ['question_id' => 29, 'is_correct' => false, 'answer_text' => '@Inherit', 'explanation' => 'Salah. Tidak ada anotasi @Inherit di Java.'],

            // ==================== QUESTION 30: Urutkan konsep polimorfisme berdasarkan waktu eksekusi (drag_and_drop) ====================
            ['question_id' => 30, 'is_correct' => true, 'answer_text' => 'Waktu Kompilasi (Overloading)', 'drag_target' => '1', 'explanation' => 'Terjadi saat kompilasi.'],
            ['question_id' => 30, 'is_correct' => true, 'answer_text' => 'Waktu Jalan (Overriding)', 'drag_target' => '2', 'explanation' => 'Terjadi saat program berjalan.'],

            // ==================== QUESTION 31: Apa itu enkapsulasi? (radio_button) ====================
            ['question_id' => 31, 'is_correct' => true, 'answer_text' => 'Membungkus data dan metode jadi satu', 'explanation' => 'Benar! Enkapsulasi membungkus data dan metode.'],
            ['question_id' => 31, 'is_correct' => false, 'answer_text' => 'Mewarisi sifat', 'explanation' => 'Salah. Itu adalah pewarisan.'],
            ['question_id' => 31, 'is_correct' => false, 'answer_text' => 'Banyak bentuk', 'explanation' => 'Salah. Itu adalah polimorfisme.'],

            // ==================== QUESTION 32: Manfaat utama enkapsulasi (radio_button) ====================
            ['question_id' => 32, 'is_correct' => true, 'answer_text' => 'Penyembunyian data dan keamanan', 'explanation' => 'Benar! Enkapsulasi melindungi data dari akses tidak sah.'],
            ['question_id' => 32, 'is_correct' => false, 'answer_text' => 'Eksekusi lebih cepat', 'explanation' => 'Salah. Enkapsulasi tentang keamanan, bukan kecepatan.'],
            ['question_id' => 32, 'is_correct' => false, 'answer_text' => 'Penggunaan ulang kode', 'explanation' => 'Salah. Itu lebih ke pewarisan.'],

            // ==================== QUESTION 33: Enkapsulasi juga dikenal sebagai data _____ (fill_in_the_blank) ====================
            ['question_id' => 33, 'is_correct' => true, 'answer_text' => 'hiding', 'blank_position' => 1, 'explanation' => 'Benar! Data hiding (penyembunyian data).'],
            ['question_id' => 33, 'is_correct' => true, 'answer_text' => 'tersembunyi', 'blank_position' => 1, 'explanation' => 'Benar! Data tersembunyi dalam bahasa Indonesia.'],
            ['question_id' => 33, 'is_correct' => true, 'answer_text' => 'penyembunyian', 'blank_position' => 1, 'explanation' => 'Benar! Penyembunyian data.'],

            // ==================== QUESTION 34: Modifier akses _____ membuat variabel hanya dapat diakses dalam kelasnya (fill_in_the_blank) ====================
            ['question_id' => 34, 'is_correct' => true, 'answer_text' => 'private', 'blank_position' => 1, 'explanation' => 'Benar! Private adalah modifier paling ketat.'],

            // ==================== QUESTION 35: Modifier akses dengan pembatasan terbesar (radio_button) ====================
            ['question_id' => 35, 'is_correct' => true, 'answer_text' => 'private', 'explanation' => 'Benar! Private memberikan pembatasan paling ketat.'],
            ['question_id' => 35, 'is_correct' => false, 'answer_text' => 'public', 'explanation' => 'Salah. Public adalah yang paling terbuka.'],
            ['question_id' => 35, 'is_correct' => false, 'answer_text' => 'protected', 'explanation' => 'Salah. Protected kurang ketat dari private.'],

            // ==================== QUESTION 36: Urutkan modifier akses dari paling ketat ke longgar (drag_and_drop) ====================
            ['question_id' => 36, 'is_correct' => true, 'answer_text' => 'private', 'drag_target' => '1', 'explanation' => 'Paling ketat - hanya dalam kelas sendiri.'],
            ['question_id' => 36, 'is_correct' => true, 'answer_text' => 'default', 'drag_target' => '2', 'explanation' => 'Hanya dalam package yang sama.'],
            ['question_id' => 36, 'is_correct' => true, 'answer_text' => 'protected', 'drag_target' => '3', 'explanation' => 'Bisa diakses subclass.'],
            ['question_id' => 36, 'is_correct' => true, 'answer_text' => 'public', 'drag_target' => '4', 'explanation' => 'Paling longgar - bisa diakses dari mana saja.'],

            // ==================== QUESTION 37: Metode getter dan setter digunakan untuk apa? (radio_button) ====================
            ['question_id' => 37, 'is_correct' => true, 'answer_text' => 'Mengakses dan mengubah data privat secara aman', 'explanation' => 'Benar! Getter dan setter menyediakan akses terkontrol ke data privat.'],
            ['question_id' => 37, 'is_correct' => false, 'answer_text' => 'Membuat objek', 'explanation' => 'Salah. Itu tugas konstruktor.'],
            ['question_id' => 37, 'is_correct' => false, 'answer_text' => 'Mewarisi kelas', 'explanation' => 'Salah. Itu bukan fungsi getter/setter.'],
        ];

        foreach ($answers as $answer) {
            Answer::updateOrCreate(
            ['question_id' => $answer['question_id'], 'answer_text' => $answer['answer_text']],
                $answer
            );
        }
    }
}
