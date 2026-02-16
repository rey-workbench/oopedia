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
                'content' => '<h2>Mengenal Paradigma PBO</h2>
                <p>Pemrograman Berorientasi Objek (PBO) adalah paradigma pemrograman yang berfokus pada "objek" sebagai unit dasar dari program. Berbeda dengan pemrograman prosedural yang berfokus pada urutan fungsi, PBO melihat sistem sebagai kumpulan objek yang saling berinteraksi.</p>
                <h3>Konsep Dasar</h3>
                <ul>
                    <li><strong>Objek:</strong> Entitas yang memiliki identitas, data (state), dan perilaku (behavior).</li>
                    <li><strong>Kelas:</strong> Template atau blueprint yang mendefinisikan struktur dan perilaku objek.</li>
                </ul>',
                'module_id' => 1,
                'created_by' => 2,
            ],
            [
                'title' => 'Kelas dan Objek',
                'content' => '<h2>Membangun Blueprint (Kelas) dan Wujudnya (Objek)</h2>
                <p>Kelas adalah definisi abstrak dari sebuah entitas, sementara Objek adalah perwujudan nyata dari definisi tersebut. Bayangkan Kelas sebagai resep masakan, dan Objek adalah makanan yang dimasak berdasarkan resep tersebut.</p>
                <pre><code>class Mobil {
    String merk;
    void jalan() { }
}
// Instansiasi
Mobil mobilSaya = new Mobil();</code></pre>',
                'module_id' => 2,
                'created_by' => 2,
            ],
            [
                'title' => 'Atribut dan Method',
                'content' => '<h2>State (Atribut) dan Behavior (Method)</h2>
                <p>Objek memiliki dua karakteristik utama: apa yang mereka ketahui (Atribut) dan apa yang bisa mereka lakukan (Method).</p>
                <p>Atribut menyimpan data tentang objek, sedangkan Method berisi logika atau aksi yang dapat dilakukan oleh objek tersebut.</p>',
                'module_id' => 3,
                'created_by' => 2,
            ],
            [
                'title' => 'Enkapsulasi',
                'content' => '<h2>Menyembunyikan Detail dengan Enkapsulasi</h2>
                <p>Enkapsulasi adalah teknik menyembunyikan detail implementasi dan melindungi data di dalam objek dari akses langsung dari luar. Akses data biasanya dibatasi lewat metode khusus seperti Getter dan Setter.</p>
                <h3>Access Modifiers</h3>
                <ul>
                    <li><strong>Private:</strong> Hanya bisa diakses internal kelas.</li>
                    <li><strong>Public:</strong> Bisa diakses bebas dari luar.</li>
                </ul>',
                'module_id' => 4,
                'created_by' => 2,
            ],
            [
                'title' => 'Pewarisan (Inheritance)',
                'content' => '<h2>Mewarisi Sifat dengan Inheritance</h2>
                <p>Inheritance memungkinkan sebuah kelas (Subclass) mewarisi atribut dan method dari kelas lain (Superclass). Ini mendorong penggunaan kembali kode (reuseability) dan membentuk hierarki yang logis.</p>
                <pre><code>class Kucing extends Hewan {
    // Kucing otomatis punya sifat Hewan
}</code></pre>',
                'module_id' => 5,
                'created_by' => 2,
            ],
            [
                'title' => 'Polimorfisme',
                'content' => '<h2>Satu Bentuk, Banyak Versi (Polimorfisme)</h2>
                <p>Polimorfisme memungkinkan satu nama metode memiliki perilaku yang berbeda-beda tergantung objek yang memanggilnya. Ini terdiri dari:
                <ul>
                    <li><strong>Overriding:</strong> Mengganti method induk di kelas anak.</li>
                    <li><strong>Overloading:</strong> Nama method sama, parameter berbeda.</li>
                </ul>',
                'module_id' => 6,
                'created_by' => 2,
            ],
            [
                'title' => 'Abstraksi',
                'content' => '<h2>Menyederhanakan Kompleksitas dengan Abstraksi</h2>
                <p>Abstraksi menyembunyikan detail rumit dan hanya menampilkan fungsionalitas utama kepada pengguna. Ini dicapai menggunakan Abstract Class dan Interface.</p>
                <p>Abstract class adalah kelas yang belum lengkap (tidak bisa di-instansiasi), sedangkan Interface adalah kontrak yang harus dipenuhi oleh kelas yang mengimplementasikannya.</p>',
                'module_id' => 7,
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

