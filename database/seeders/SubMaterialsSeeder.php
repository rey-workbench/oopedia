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
                'title' => 'Apa itu PBO?',
                'content' => 'PBO adalah paradigma pemrograman yang mengorganisir kode di sekitar objek dan data, bukan di sekitar tindakan dan logika.',
                'jenis_konten' => 'teori',
                'order' => 1,
            ],
            [
                'material_id' => 1,
                'title' => 'Prinsip-Prinsip PBO',
                'content' => 'Empat prinsip utama PBO adalah: Enkapsulasi, Abstraksi, Pewarisan (Inheritance), dan Polimorfisme.',
                'jenis_konten' => 'teori',
                'order' => 2,
            ],

            // ==================== Material 2: Kelas dan Objek ====================
            [
                'material_id' => 2,
                'title' => 'Memahami Kelas',
                'content' => 'Kelas adalah cetak biru atau template untuk membuat objek. Kelas mendefinisikan struktur dan perilaku.',
                'jenis_konten' => 'teori',
                'order' => 1,
            ],
            [
                'material_id' => 2,
                'title' => 'Membuat Objek',
                'content' => 'Objek adalah perwujudan dari kelas. Anda membuatnya menggunakan kata kunci "new" di sebagian besar bahasa pemrograman berorientasi objek.',
                'jenis_konten' => 'sintaks',
                'order' => 2,
            ],
            [
                'material_id' => 2,
                'title' => 'Konstruktor dan Metode',
                'content' => 'Konstruktor menginisialisasi objek, sedangkan metode mendefinisikan perilaku objek.',
                'jenis_konten' => 'sintaks',
                'order' => 3,
            ],

            // ==================== Material 3: Pewarisan ====================
            [
                'material_id' => 3,
                'title' => 'Dasar-Dasar Pewarisan',
                'content' => 'Pewarisan memungkinkan sebuah kelas untuk mewarisi properti dan metode dari kelas lain.',
                'jenis_konten' => 'teori',
                'order' => 1,
            ],
            [
                'material_id' => 3,
                'title' => 'Mengimplementasikan Pewarisan',
                'content' => 'Gunakan kata kunci "extends" di Java untuk membuat subclass yang mewarisi dari parent class.',
                'jenis_konten' => 'sintaks',
                'order' => 2,
            ],
            [
                'material_id' => 3,
                'title' => 'Jenis-Jenis Pewarisan',
                'content' => 'Pewarisan tunggal (single), bertingkat (multilevel), dan hierarki. Java tidak mendukung pewarisan berganda secara langsung.',
                'jenis_konten' => 'teori',
                'order' => 3,
            ],

            // ==================== Material 4: Polimorfisme ====================
            [
                'material_id' => 4,
                'title' => 'Konsep Polimorfisme',
                'content' => 'Polimorfisme berarti "banyak bentuk" - objek dapat mengambil berbagai bentuk tergantung pada konteksnya.',
                'jenis_konten' => 'teori',
                'order' => 1,
            ],
            [
                'material_id' => 4,
                'title' => 'Method Overloading',
                'content' => 'Polimorfisme waktu kompilasi (compile-time): beberapa metode dengan nama yang sama tetapi parameter yang berbeda.',
                'jenis_konten' => 'sintaks',
                'order' => 2,
            ],
            [
                'material_id' => 4,
                'title' => 'Method Overriding',
                'content' => 'Polimorfisme waktu jalan (runtime): subclass menyediakan implementasi spesifik dari metode yang ada di parent class.',
                'jenis_konten' => 'sintaks',
                'order' => 3,
            ],

            // ==================== Material 5: Enkapsulasi ====================
            [
                'material_id' => 5,
                'title' => 'Prinsip Enkapsulasi',
                'content' => 'Enkapsulasi membungkus data dan metode menjadi satu, menyembunyikan detail internal dari luar.',
                'jenis_konten' => 'teori',
                'order' => 1,
            ],
            [
                'material_id' => 5,
                'title' => 'Modifier Akses',
                'content' => 'Private, protected, public, dan default mengontrol visibilitas anggota kelas.',
                'jenis_konten' => 'sintaks',
                'order' => 2,
            ],
            [
                'material_id' => 5,
                'title' => 'Getter dan Setter',
                'content' => 'Metode getter dan setter menyediakan akses terkontrol ke variabel privat.',
                'jenis_konten' => 'sintaks',
                'order' => 3,
            ],
        ];

        foreach ($subMaterials as $subMaterial) {
            SubMaterial::updateOrCreate(
            ['material_id' => $subMaterial['material_id'], 'title' => $subMaterial['title']],
                $subMaterial
            );
        }
    }
}
