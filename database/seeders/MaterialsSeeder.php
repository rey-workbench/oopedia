<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialsSeeder extends Seeder
{
    public function run(): void
    {
        Material::query()->delete();

        $materials = [
            [
                'title' => 'Paradigma OOP vs Struktural',
                'content' => '<h2>1. Evolusi Paradigma Pemrograman</h2>
                <p>Dalam dunia rekayasa perangkat lunak, paradigma pemrograman menentukan bagaimana kita berpikir tentang masalah. Mari kita bandingkan dua raksasa: <strong>Struktural</strong> dan <strong>Berorientasi Objek</strong>.</p>
                
                <h3>A. Pemrograman Struktural (Langkah demi Langkah)</h3>
                <p>Bayangkan Anda sedang memasak mie instan. Anda mengikuti langkah 1, lalu 2, lalu 3. Data (Mie, Air, Bumbu) dan Prosedur (Rebus, Tuang) terpisah.</p>
                <ul>
                    <li><strong>Kelemahan:</strong> Jika Anda ingin mengganti Mie dengan Pasta, Anda mungkin harus mengubah seluruh urutan masak karena fungsinya sangat bergantung pada tipe datanya.</li>
                    <li><strong>Maintenance:</strong> Sulit dikelola saat kode sudah mencapai ribuan baris.</li>
                </ul>
                
                <h3>B. Pemrograman Berorientasi Objek (Sistem Berbasis Komponen)</h3>
                <p>OOP memandang dunia sebagai kumpulan <strong>Objek</strong> yang mandiri. Objek memiliki data (Atribut) dan kemampuan (Method) yang dibungkus jadi satu.</p>

                <blockquote>
                    <strong>Analogi Dunia Nyata: Game Sepeda</strong><br>
                    Dalam struktural, Anda membuat variabel kecepatan1, kecepatan2, dst. Dalam OOP, Anda membuat Class Sepeda, lalu tinggal mencetak objek sepedaBudi atau sepedaIwan dari cetakan tersebut.
                </blockquote>
                
                <h3>Checklist Penguasaan</h3>
                <ul>
                    <li>Paham perbedaan mendasar data vs prosedur.</li>
                    <li>Bisa menjelaskan analogi Sepeda ke teman.</li>
                    <li>Tahu alasan kenapa OOP lebih mudah dikelola (maintainable).</li>
                </ul>',
                'module_id' => '1',
            ],
            [
                'title' => 'Anatomi Class & Object',
                'content' => '<h2>2. Blueprint vs Instance</h2>
                <p>Java adalah bahasa yang murni berorientasi objek. Hampir semua kode Anda harus berada di dalam sebuah <strong>Class</strong>.</p>
                
                <h3>Apa itu Class?</h3>
                <p>Class adalah <strong>Template</strong> atau rancangan dasar. Ia belum memiliki wujud nyata di memori komputer sampai ia diinstansiasi menjadi Objek.</p>
                
                <h3>Aturan Penamaan Standar Industri</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Komponen</th>
                            <th>Standar Java (CamelCase)</th>
                            <th>Contoh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Nama Class</td>
                            <td><strong>PascalCase</strong></td>
                            <td><code>PremiumUser</code>, <code>PaymentGateway</code></td>
                        </tr>
                        <tr>
                            <td>Atribut & Method</td>
                            <td><strong>camelCase</strong></td>
                            <td><code>hitungNilai()</code>, <code>processPayment()</code></td>
                        </tr>
                    </tbody>
                </table>

                <h3>Implementasi Konstruktor</h3>
                <pre><code>public class Donat {
    public String topping;
    public Donat(String toppingAwal) {
        this.topping = toppingAwal;
        System.out.println("Donat rasa " + topping + " dibuat!");
    }
}</code></pre>
                
                <blockquote>
                    <strong>Peringatan Pemula:</strong><br>
                    Object adalah data nyata di RAM, sedangkan Class hanyalah teks di file .java. Tanpa keyword <strong>new</strong>, objek tidak akan pernah tercipta.
                </blockquote>

                <h3>Checklist Penguasaan</h3>
                <ul>
                    <li>Bisa membedakan Blueprint (Class) dan Hasil Cetak (Object).</li>
                    <li>Hafal standar penamaan PascalCase dan camelCase.</li>
                    <li>Paham fungsi Konstruktor saat pembuatan objek.</li>
                </ul>',
                'module_id' => '2',
            ],
            [
                'title' => 'Enkapsulasi & Information Hiding',
                'content' => '<h2>3. Prinsip "Bungkus" dan Keamanan Data</h2>
                <p>Pernahkah Anda bertanya kenapa tombol di remote TV tertutup plastik? Itu adalah enkapsulasi. Anda hanya bisa menekan tombol (Interface), tapi tidak bisa menyentuh sirkuit di dalamnya (Implementasi).</p>
                
                <h3>Level Akses (Access Modifiers)</h3>
                <ul>
                    <li>🔴 <strong>private:</strong> Hanya bisa diakses oleh kode di dalam Class itu sendiri.</li>
                    <li>🟡 <strong>protected:</strong> Bisa diakses oleh package yang sama dan subclass.</li>
                    <li>🟢 <strong>public:</strong> Terbuka untuk siapapun di manapun.</li>
                </ul>

                <h3>Kenapa butuh Getter & Setter?</h3>
                <pre><code>public class AkunBank {
    private double saldo;
    public void setSaldo(double jumlah) {
        if (jumlah >= 0) {
            this.saldo = jumlah;
        }
    }
}</code></pre>

                <h3>Checklist Penguasaan</h3>
                <ul>
                    <li>Paham alasan atribut harus private (Data Integrity).</li>
                    <li>Bisa mengimplementasikan Getter dan Setter dengan benar.</li>
                    <li>Tahu perbedaan antara public dan private access.</li>
                </ul>',
                'module_id' => '3',
            ],
            [
                'title' => 'Relasi Antar Class (UML Dasar)',
                'content' => '<h2>4. Bagaimana Objek Berinteraksi?</h2>
                <p>Dalam sistem besar, class tidak berdiri sendiri. Mereka menjalin hubungan atau relasi untuk membentuk satu kesatuan sistem.</p>
                
                <h3>3 Tipe Relasi Utama</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Tipe Relasi</th>
                            <th>Sifat Hubungan</th>
                            <th>Contoh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Association</strong></td>
                            <td>Mandiri (Menggunakan)</td>
                            <td>Dosen mengajar Mahasiswa.</td>
                        </tr>
                        <tr>
                            <td><strong>Aggregation</strong></td>
                            <td>Bagian dari (Lemah)</td>
                            <td>Jurusan memiliki Dosen.</td>
                        </tr>
                        <tr>
                            <td><strong>Composition</strong></td>
                            <td>Kepemilikan Mutlak</td>
                            <td>Manusia memiliki Jantung.</td>
                        </tr>
                    </tbody>
                </table>

                <blockquote>
                    <strong>UML Insight:</strong><br>
                    Komposisi dilambangkan dengan belah ketupat hitam (full), sedangkan agregasi dilambangkan dengan belah ketupat putih (kosong).
                </blockquote>

                <h3>Checklist Penguasaan</h3>
                <ul>
                    <li>Bisa membedakan Agregasi dan Komposisi.</li>
                    <li>Paham arti simbol belah ketupat di UML.</li>
                    <li>Tahu kapan harus menggunakan relasi Komposisi.</li>
                </ul>',
                'module_id' => '4',
            ],
            [
                'title' => 'Inheritance & Abstraksi: Hierarki dan Kontrak',
                'content' => '<h2>5. Pewarisan dan Standarisasi Kode</h2>
                <p>Jangan mengulang kode yang sama! Inheritance memungkinkan kita mewarisi sifat induk, sementara Abstraksi memberikan aturan main yang jelas.</p>
                
                <h3>A. Inheritance (Pewarisan)</h3>
                <pre><code>public class Kucing extends Hewan {
    public void meong() { 
        super.makan(); 
        System.out.println("Meong!"); 
    }
}</code></pre>

                <h3>B. Abstract Class vs Interface</h3>
                <ul>
                    <li><strong>Abstract Class:</strong> Gunakan saat kelas-kelas tersebut "Satu Keluarga" (Hierarki).</li>
                    <li><strong>Interface:</strong> Gunakan sebagai "Kontrak" untuk kelas-kelas yang berbeda keluarga.</li>
                </ul>
                
                <h3>Checklist Penguasaan</h3>
                <ul>
                    <li>Bisa menggunakan keyword extends dan implements.</li>
                    <li>Paham perbedaan filosofis Abstract Class vs Interface.</li>
                    <li>Tahu cara memanggil method induk dengan super.</li>
                </ul>',
                'module_id' => '6',
            ],
            [
                'title' => 'Mastering Polimorfisme: Fleksibilitas Dewa',
                'content' => '<h2>6. Satu Nama, Seribu Bentuk</h2>
                <p>Polimorfisme adalah inti dari fleksibilitas OOP. Kita bisa memproses berbagai objek berbeda melalui satu referensi induk.</p>
                
                <h3>Overloading vs Overriding</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Fitur</th>
                            <th>Overloading (Static)</th>
                            <th>Overriding (Dynamic)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Parameter</td>
                            <td>Harus Berbeda</td>
                            <td>Harus Sama</td>
                        </tr>
                        <tr>
                            <td>Waktu Deteksi</td>
                            <td>Compile-time</td>
                            <td>Run-time</td>
                        </tr>
                    </tbody>
                </table>

                <blockquote>
                    <strong>Interview Question:</strong><br>
                    "Kenapa Polimorfisme penting?". Jawaban: Agar kode kita Decoupled. Kita bisa menambah Class baru tanpa mengubah kode utama.
                </blockquote>

                <h3>Checklist Penguasaan</h3>
                <ul>
                    <li>Bisa menjelaskan perbedaan Overloading dan Overriding.</li>
                    <li>Paham konsep Upcasting dan Downcasting.</li>
                    <li>Bisa membuat koleksi data yang polimorfik.</li>
                </ul>',
                'module_id' => '7',
            ],
        ];

        foreach ($materials as $material) {
            Material::updateOrCreate(
                ['title' => $material['title']],
                [
                    'content'    => $material['content'],
                    'module_id'  => $material['module_id'],
                    'created_by' => '01kqd08mx4rj8z6ergz63k7gfe',
                ]
            );
        }
    }
}
