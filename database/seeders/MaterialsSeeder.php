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
                    <li><strong>Kelemahan:</strong> Jika Anda ingin mengganti "Mie" dengan "Pasta", Anda mungkin harus mengubah seluruh urutan masak karena fungsinya sangat bergantung pada tipe datanya.</li>
                    <li><strong>Maintenance:</strong> Sulit dikelola saat kode sudah mencapai ribuan baris.</li>
                </ul>
                
                <h3>B. Pemrograman Berorientasi Objek (Sistem Berbasis Komponen)</h3>
                <p>OOP memandang dunia sebagai kumpulan <strong>Objek</strong> yang mandiri. Objek memiliki data (Atribut) dan kemampuan (Method) yang dibungkus jadi satu.</p>
                <p>Jika ada perubahan fitur, kita cukup mengubah objek yang bersangkutan tanpa perlu khawatir merusak bagian program yang lain.</p>

                <blockquote>
                    <strong>Analogi Dunia Nyata: Game Sepeda</strong><br>
                    Dalam struktural, Anda membuat variabel <code>kecepatan1</code>, <code>kecepatan2</code>, dst. Dalam OOP, Anda membuat <strong>Class Sepeda</strong>, lalu tinggal "mencetak" objek <code>sepedaBudi</code> atau <code>sepedaIwan</code> dari cetakan tersebut.
                </blockquote>
                
                <h3>Kenapa Developer Digaji Mahal untuk OOP?</h3>
                <p>90% enterprise software (Bank, E-Commerce) menggunakan OOP karena mampu menangani jutaan baris kode tanpa menjadi "Spaghetti Code". Kemampuan untuk membangun sistem yang bisa berkembang (scalable) adalah nilai jual utama Anda sebagai developer.</p>',
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
                <p>Konstruktor adalah method istimewa yang dipanggil tepat saat objek dibuat. Namanya harus <strong>sama persis</strong> dengan nama Class.</p>

                <pre><code>public class Donat {
    public String topping;

    // Ini adalah Konstruktor
    public Donat(String toppingAwal) {
        this.topping = toppingAwal;
        System.out.println("Donat rasa " + topping + " dibuat!");
    }
}</code></pre>
                
                <blockquote>
                    <strong>⚠️ Common Mistake:</strong><br>
                    Pemula sering lupa bahwa <code>Object</code> adalah data nyata di RAM, sedangkan <code>Class</code> hanyalah teks di file .java. Tanpa keyword <code>new</code>, objek tidak akan pernah tercipta.
                </blockquote>',
                'module_id' => '2',
            ],
            [
                'title' => 'Enkapsulasi & Information Hiding',
                'content' => '<h2>3. Prinsip "Bungkus" dan Keamanan Data</h2>
                <p>Pernahkah Anda bertanya kenapa tombol di remote TV tertutup plastik? Itu adalah enkapsulasi. Anda hanya bisa menekan tombol (Interface), tapi tidak bisa menyentuh sirkuit di dalamnya (Implementasi).</p>
                
                <h3>Level Akses (Access Modifiers)</h3>
                <ul class="space-y-2">
                    <li>🔴 <strong>private:</strong> Hanya bisa diakses oleh kode di dalam Class itu sendiri. (Private First Policy)</li>
                    <li>🟡 <strong>protected:</strong> Bisa diakses oleh package yang sama dan subclass.</li>
                    <li>🟢 <strong>public:</strong> Terbuka untuk siapapun di manapun.</li>
                </ul>

                <h3>Kenapa butuh Getter & Setter?</h3>
                <p>Bukan hanya untuk ambil/simpan data, tapi untuk <strong>Validasi</strong>.</p>

                <pre><code>public class AkunBank {
    private double saldo;

    public void setSaldo(double jumlah) {
        if (jumlah >= 0) { // Validasi: Keamanan data!
            this.saldo = jumlah;
        } else {
            System.out.println("Error: Saldo ilegal!");
        }
    }
}</code></pre>',
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
                    Komposisi dilambangkan dengan belah ketupat hitam (full), sedangkan agregasi dilambangkan dengan belah ketupat putih (kosong). Pilihlah Komposisi jika Anda ingin objek anak "mati" bersama induknya.
                </blockquote>',
                'module_id' => '4',
            ],
            [
                'title' => 'Inheritance & Abstraksi: Hierarki dan Kontrak',
                'content' => '<h2>5. Pewarisan dan Standarisasi Kode</h2>
                <p>Jangan mengulang kode yang sama! Inheritance memungkinkan kita mewarisi sifat induk, sementara Abstraksi memberikan aturan main yang jelas.</p>
                
                <h3>A. Inheritance (Pewarisan)</h3>
                <p>Gunakan keyword <code>extends</code> untuk mewarisi dan <code>super</code> untuk merujuk ke induk.</p>
                <pre><code>public class Hewan {
    public void makan() { System.out.println("Sedang makan..."); }
}

public class Kucing extends Hewan {
    public void meong() { 
        super.makan(); 
        System.out.println("Meong!"); 
    }
}</code></pre>

                <h3>B. Abstract Class vs Interface</h3>
                <p>Kapan pakai yang mana? Ini pertanyaan favorit saat interview kerja!</p>
                <ul>
                    <li><strong>Abstract Class:</strong> Gunakan saat kelas-kelas tersebut "Satu Keluarga" (Hierarki).</li>
                    <li><strong>Interface:</strong> Gunakan sebagai "Kontrak" untuk kelas-kelas yang berbeda keluarga tapi punya kemampuan sama.</li>
                </ul>

                <pre><code>public interface Terbangable {
    void terbang();
}

public class Burung implements Terbangable {
    public void terbang() { System.out.println("Mengepak sayap..."); }
}</code></pre>
                
                <blockquote>
                    <strong>🚀 Pro-Tip:</strong><br>
                    Abstract Class menjawab "Siapa kamu?", sedangkan Interface menjawab "Kamu bisa apa?".
                </blockquote>',
                'module_id' => '6',
            ],
            [
                'title' => 'Mastering Polimorfisme: Dari Dasar ke Lanjutan',
                'content' => '<h2>6. Satu Nama, Seribu Bentuk</h2>
                <p>Polimorfisme adalah inti dari fleksibilitas OOP. Kita akan mempelajari dari teknik dasar hingga manipulasi objek tingkat lanjut.</p>
                
                <h3>A. Overloading vs Overriding</h3>
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
                            <td>Lokasi</td>
                            <td>Kelas yang sama</td>
                            <td>Subclass (Anak)</td>
                        </tr>
                        <tr>
                            <td>Parameter</td>
                            <td>Harus Berbeda</td>
                            <td>Harus Sama</td>
                        </tr>
                    </tbody>
                </table>

                <h3>B. Object Casting (Upcasting & Downcasting)</h3>
                <p>Proses mengubah tipe referensi objek demi fleksibilitas koleksi data.</p>
                <pre><code>Pegawai p = new Dosen(); // Upcasting: Aman otomatis

// Downcasting: Harus manual dan hati-hati!
if (p instanceof Dosen) {
    Dosen d = (Dosen) p; 
    d.ajarMatkul();
}</code></pre>

                <h3>C. Heterogenous Collection</h3>
                <p>Menyimpan berbagai objek berbeda (tapi satu induk) dalam satu wadah tunggal.</p>
                <pre><code>Hewan[] kebunBinatang = { new Singa(), new Gajah(), new Kucing() };
for (Hewan h : kebunBinatang) {
    h.bersuara(); // Polimorfisme menentukan suara secara dinamis
}</code></pre>

                <blockquote>
                    <strong>🔥 Interview Question:</strong><br>
                    "Kenapa Polimorfisme penting?". Jawaban: Agar kode kita **Decoupled**. Kita bisa menambah Class baru tanpa mengubah kode utama yang memproses data tersebut.
                </blockquote>',
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
