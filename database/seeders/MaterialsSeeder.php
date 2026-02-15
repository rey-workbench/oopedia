<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;

class MaterialsSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            [
                'title' => 'Pengenalan Pemrograman Berorientasi Objek',
                'content' => 'Pemrograman berorientasi objek (PBO/OOP) adalah paradigma pemrograman yang didasarkan pada konsep "objek", yang dapat berisi data dan kode: data dalam bentuk field (sering dikenal sebagai atribut atau properti), dan kode, dalam bentuk prosedur (sering dikenal sebagai metode atau method).',
                'module_id' => 1, // Foundation
                'created_by' => 2,
            ],
            [
                'title' => 'Kelas dan Objek',
                'content' => 'Kelas adalah cetak biru (blueprint) untuk membuat objek. Objek adalah perwujudan (instance) dari sebuah kelas. Kelas mendefinisikan properti dan perilaku yang akan dimiliki oleh objek dari kelas tersebut.',
                'module_id' => 1, // Foundation
                'created_by' => 2,
            ],
            [
                'title' => 'Pewarisan (Inheritance)',
                'content' => 'Pewarisan adalah mekanisme di mana satu kelas memperoleh properti dari kelas lain. Contohnya, kelas anak (child class) mewarisi properti dan metode dari kelas induknya (parent class).',
                'module_id' => 3, // Inheritance
                'created_by' => 2,
            ],
            [
                'title' => 'Polimorfisme',
                'content' => 'Polimorfisme memungkinkan objek dari kelas yang berbeda diperlakukan sebagai objek dari kelas super yang sama. Ini adalah kemampuan suatu objek untuk mengambil banyak bentuk.',
                'module_id' => 4, // Polymorphism
                'created_by' => 2,
            ],
            [
                'title' => 'Enkapsulasi',
                'content' => 'Enkapsulasi adalah penggabungan data dan metode yang beroperasi pada data tersebut dalam satu unit tunggal atau objek, serta membatasi akses ke beberapa komponen objek tersebut.',
                'module_id' => 2, // Encapsulation
                'created_by' => 2,
            ],
        ];

        foreach ($materials as $material) {
            Material::updateOrCreate(
            ['title' => $material['title']],
                $material
            );
        }
    }
}
