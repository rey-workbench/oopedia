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
                'content' => '<h2>Pengenalan PBO</h2>
                <p>Pemrograman Berorientasi Objek (PBO) atau Object-Oriented Programming (OOP) adalah paradigma pemrograman yang didasarkan pada konsep "objek". Objek dapat berisi data dalam bentuk field (atribut atau properti) dan kode dalam bentuk prosedur (metode).</p>
                <h3>Mengapa Belajar PBO?</h3>
                <ul>
                    <li><strong>Modularitas:</strong> Kode terorganisir dalam objek-objek mandiri</li>
                    <li><strong>Reusability:</strong> Kode dapat digunakan kembali</li>
                    <li><strong>Maintainability:</strong> Lebih mudah dirawat dan dimodifikasi</li>
                    <li><strong>Scalability:</strong> Mudah diperluas untuk proyek besar</li>
                </ul>
                <h3>Prinsip Dasar PBO</h3>
                <p>PBO memiliki 4 prinsip utama: Enkapsulasi, Abstraksi, Pewarisan (Inheritance), dan Polimorfisme. Prinsip-prinsip ini akan membantu Anda menulis kode yang lebih bersih dan terstruktur.</p>',
                'module_id' => 1, // Foundation
                'created_by' => 2,
            ],
            [
                'title' => 'Kelas dan Objek',
                'content' => '<h2>Kelas dan Objek</h2>
                <p>Kelas adalah cetak biru (blueprint) atau template untuk membuat objek. Objek adalah perwujudan (instance) konkret dari sebuah kelas.</p>
                <h3>Apa itu Kelas?</h3>
                <p>Kelas mendefinisikan struktur data (atribut) dan perilaku (metode) yang akan dimiliki oleh objek-objek yang dibuat darinya.</p>
                <pre><code>class Mobil {
    String warna;
    int kecepatanMaks;
    
    void klakson() {
        System.out.println("Tin tin!");
    }
}</code></pre>
                <h3>Apa itu Objek?</h3>
                <p>Objek adalah instance dari kelas. Satu kelas dapat memiliki banyak objek dengan nilai atribut yang berbeda.</p>
                <pre><code>Mobil mobilSaya = new Mobil();
mobilSaya.warna = "Merah";
mobilSaya.klakson(); // Output: Tin tin!</code></pre>',
                'module_id' => 1, // Foundation
                'created_by' => 2,
            ],
            [
                'title' => 'Enkapsulasi',
                'content' => '<h2>Enkapsulasi</h2>
                <p>Enkapsulasi adalah prinsip menyembunyikan detail implementasi internal objek dan hanya mengekspos interface yang diperlukan.</p>
                <h3>Access Modifiers</h3>
                <ul>
                    <li><strong>private:</strong> Hanya dapat diakses dalam kelas itu sendiri</li>
                    <li><strong>protected:</strong> Dapat diakses dalam kelas dan subclass</li>
                    <li><strong>public:</strong> Dapat diakses dari mana saja</li>
                </ul>
                <h3>Getter dan Setter</h3>
                <p>Menggunakan getter dan setter untuk mengakses dan memodifikasi atribut private:</p>
                <pre><code>class Mahasiswa {
    private String nama;
    private int umur;
    
    // Getter
    public String getNama() {
        return nama;
    }
    
    // Setter
    public void setNama(String nama) {
        this.nama = nama;
    }
}</code></pre>
                <h3>Manfaat Enkapsulasi</h3>
                <p>Melindungi data dari akses tidak sah, validasi data sebelum diset, dan fleksibilitas untuk mengubah implementasi tanpa mempengaruhi kode lain.</p>',
                'module_id' => 2, // Encapsulation
                'created_by' => 2,
            ],
            [
                'title' => 'Pewarisan (Inheritance)',
                'content' => '<h2>Pewarisan (Inheritance)</h2>
                <p>Pewarisan adalah mekanisme di mana satu kelas (kelas anak/subclass) mewarisi properti dan metode dari kelas lain (kelas induk/superclass).</p>
                <h3>Konsep Dasar</h3>
                <p>Pewarisan memungkinkan reusability kode dan membentuk hierarki kelas yang logis.</p>
                <pre><code>// Kelas Induk
class Hewan {
    String nama;
    
    void bersuara() {
        System.out.println("Hewan bersuara");
    }
}

// Kelas Anak
class Kucing extends Hewan {
    @Override
    void bersuara() {
        System.out.println("Meow!");
    }
}</code></pre>
                <h3>Jenis Pewarisan</h3>
                <ul>
                    <li><strong>Single Inheritance:</strong> Satu anak dari satu induk</li>
                    <li><strong>Multilevel Inheritance:</strong> Bertingkat (A → B → C)</li>
                    <li><strong>Hierarchical Inheritance:</strong> Banyak anak dari satu induk</li>
                </ul>
                <h3>Kata Kunci Penting</h3>
                <ul>
                    <li><strong>extends:</strong> Untuk mewarisi dari kelas induk</li>
                    <li><strong>super:</strong> Untuk memanggil konstruktor/metode induk</li>
                </ul>',
                'module_id' => 3, // Inheritance
                'created_by' => 2,
            ],
            [
                'title' => 'Polimorfisme',
                'content' => '<h2>Polimorfisme</h2>
                <p>Polimorfisme berarti "banyak bentuk". Dalam PBO, polimorfisme memungkinkan objek diperlakukan sebagai instance dari kelas induknya.</p>
                <h3>Jenis Polimorfisme</h3>
                <h4>1. Compile-time Polymorphism (Method Overloading)</h4>
                <p>Metode dengan nama sama tapi parameter berbeda:</p>
                <pre><code>class Kalkulator {
    int tambah(int a, int b) {
        return a + b;
    }
    
    double tambah(double a, double b) {
        return a + b;
    }
    
    int tambah(int a, int b, int c) {
        return a + b + c;
    }
}</code></pre>
                <h4>2. Runtime Polymorphism (Method Overriding)</h4>
                <p>Subclass mendefinisikan ulang metode dari superclass:</p>
                <pre><code>class Bentuk {
    void gambar() {
        System.out.println("Menggambar bentuk");
    }
}

class Lingkaran extends Bentuk {
    @Override
    void gambar() {
        System.out.println("Menggambar lingkaran");
    }
}

// Penggunaan
Bentuk b = new Lingkaran();
b.gambar(); // Output: Menggambar lingkaran</code></pre>
                <h3>Manfaat Polimorfisme</h3>
                <ul>
                    <li>Fleksibilitas dalam desain kode</li>
                    <li>Memudahkan ekstensibilitas</li>
                    <li>Code reusability yang lebih baik</li>
                </ul>',
                'module_id' => 4, // Polymorphism
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

