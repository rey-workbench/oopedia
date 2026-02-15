<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubMaterial;

class SubMaterialsSeeder extends Seeder
{
    public function run(): void
    {
        $subMaterials = [
            // ==================== Material 1: Pengenalan PBO ====================
            [
                'material_id' => 1,
                'title' => 'Konsep Dasar PBO',
                'content' => 'PBO adalah paradigma pemrograman yang mengorganisir kode di sekitar objek dan data. (Konten Teori)',
                'jenis_konten' => 'teori',
                'learning_style' => 'textual',
                'order' => 1,
            ],
            [
                'material_id' => 1,
                'title' => 'Struktur Kode PBO',
                'content' => 'Contoh struktur dasar kelas dan objek dalam kode Java/PHP. (Konten Sintaks)',
                'jenis_konten' => 'sintaks',
                'learning_style' => 'visual',
                'order' => 2,
            ],
            [
                'material_id' => 1,
                'title' => 'Rangkuman Pengenalan PBO',
                'content' => 'Gabungan pemahaman konsep dan penerapannya dalam kasus sederhana. (Konten Mixed)',
                'jenis_konten' => 'mixed',
                'learning_style' => 'mixed',
                'order' => 3,
            ],

            // ==================== Material 2: Kelas dan Objek ====================
            [
                'material_id' => 2,
                'title' => 'Teori Kelas & Objek',
                'content' => 'Kelas adalah cetak biru, Objek adalah instansiasi. Memahami hubungan keduanya. (Konten Teori)',
                'jenis_konten' => 'teori',
                'learning_style' => 'textual',
                'order' => 1,
            ],
            [
                'material_id' => 2,
                'title' => 'Sintaks Pembuatan Kelas',
                'content' => 'Cara mendeklarasikan class, property, dan method secara sintaksis. (Konten Sintaks)',
                'jenis_konten' => 'sintaks',
                'learning_style' => 'visual',
                'order' => 2,
            ],
            [
                'material_id' => 2,
                'title' => 'Studi Kasus Kelas & Objek',
                'content' => 'Analisis kasus penggunaan nyata dan implementasi kodenya. (Konten Mixed)',
                'jenis_konten' => 'mixed',
                'learning_style' => 'mixed',
                'order' => 3,
            ],

            // ==================== Material 3: Pewarisan (Inheritance) ====================
            [
                'material_id' => 3,
                'title' => 'Konsep Pewarisan',
                'content' => 'Memahami hubungan parent-child class dan keuntungan reusability. (Konten Teori)',
                'jenis_konten' => 'teori',
                'learning_style' => 'textual',
                'order' => 1,
            ],
            [
                'material_id' => 3,
                'title' => 'Implementasi Extends',
                'content' => 'Menggunakan keyword extends dan super dalam kode. (Konten Sintaks)',
                'jenis_konten' => 'sintaks',
                'learning_style' => 'visual',
                'order' => 2,
            ],
            [
                'material_id' => 3,
                'title' => 'Latihan Pewarisan Lengkap',
                'content' => 'Membangun hierarki kelas hewan dengan properti unik. (Konten Mixed)',
                'jenis_konten' => 'mixed',
                'learning_style' => 'mixed',
                'order' => 3,
            ],

            // ==================== Material 4: Polimorfisme ====================
            [
                'material_id' => 4,
                'title' => 'Teori Polimorfisme',
                'content' => 'Banyak bentuk dalam satu antarmuka. Overloading vs Overriding. (Konten Teori)',
                'jenis_konten' => 'teori',
                'learning_style' => 'textual',
                'order' => 1,
            ],
            [
                'material_id' => 4,
                'title' => 'Kode Overriding & Overloading',
                'content' => 'Contoh sintaks method overloading dan overriding yang benar. (Konten Sintaks)',
                'jenis_konten' => 'sintaks',
                'learning_style' => 'visual',
                'order' => 2,
            ],
            [
                'material_id' => 4,
                'title' => 'Aplikasi Polimorfisme',
                'content' => 'Menerapkan polimorfisme dalam sistem pembayaran. (Konten Mixed)',
                'jenis_konten' => 'mixed',
                'learning_style' => 'mixed',
                'order' => 3,
            ],

            // ==================== Material 5: Enkapsulasi ====================
            [
                'material_id' => 5,
                'title' => 'Pentingnya Enkapsulasi',
                'content' => 'Data hiding dan proteksi state objek. (Konten Teori)',
                'jenis_konten' => 'teori',
                'learning_style' => 'textual',
                'order' => 1,
            ],
            [
                'material_id' => 5,
                'title' => 'Access Modifiers & Getter/Setter',
                'content' => 'Penggunaan private, public, protected, dan pembuatan method akses. (Konten Sintaks)',
                'jenis_konten' => 'sintaks',
                'learning_style' => 'visual',
                'order' => 2,
            ],
            [
                'material_id' => 5,
                'title' => 'Keamanan Data Bank',
                'content' => 'Simulasi sistem bank dengan enkapsulasi ketat. (Konten Mixed)',
                'jenis_konten' => 'mixed',
                'learning_style' => 'mixed',
                'order' => 3,
            ],
        ];

        foreach ($subMaterials as $subMaterial) {
            try {
                SubMaterial::updateOrCreate(
                ['material_id' => $subMaterial['material_id'], 'title' => $subMaterial['title']],
                    $subMaterial
                );
            }
            catch (\Exception $e) {
                dump($e->getMessage());
                dump($subMaterial);
            }
        }
    }
}
