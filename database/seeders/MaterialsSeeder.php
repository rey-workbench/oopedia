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
                    <strong>Analogi Game Sepeda:</strong><br>
                    Dalam struktural, Anda membuat variabel <code>kecepatan1</code>, <code>kecepatan2</code>, dst. Dalam OOP, Anda membuat <strong>Class Sepeda</strong>, lalu tinggal "mencetak" objek <code>sepedaBudi</code> atau <code>sepedaIwan</code> dari cetakan tersebut.
                </blockquote>',
                'module_id' => '1',
            ],
            [
                'title' => 'Anatomi Class & Object',
                'content' => '<h2>2. Blueprint vs Instance</h2>
                <p>Java adalah bahasa yang murni berorientasi objek. Hampir semua kode Anda harus berada di dalam sebuah <strong>Class</strong>.</p>
                
                <h3>Apa itu Class?</h3>
                <p>Class adalah <strong>Template</strong> atau rancangan dasar. Ia belum memiliki wujud nyata di memori komputer sampai ia diinstansiasi menjadi Objek.</p>
                
                <h3>Aturan Penamaan (Best Practices)</h3>
                <table>
                    <thead>
                        <tr>
                            <th class="p-3 text-left">Komponen</th>
                            <th class="p-3 text-left">Standar Java (CamelCase)</th>
                            <th class="p-3 text-left">Contoh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-3">Nama Class</td>
                            <td class="p-3 font-mono">PascalCase</td>
                            <td class="p-3"><code>MahasiswaAktif</code></td>
                        </tr>
                        <tr>
                            <td class="p-3">Atribut & Method</td>
                            <td class="p-3 font-mono">camelCase</td>
                            <td class="p-3"><code>hitungNilai()</code></td>
                        </tr>
                    </tbody>
                </table>

                <h3>Implementasi Konstruktor</h3>
                <p>Konstruktor adalah method istimewa yang dipanggil tepat saat objek dibuat. Namanya harus <strong>sama persis</strong> dengan nama Class dan tidak punya return type.</p>

                <pre><code>public class Donat {
    public String topping;

    // Ini adalah Konstruktor
    public Donat(String toppingAwal) {
        this.topping = toppingAwal;
        System.out.println("Donat rasa " + topping + " dibuat!");
    }
}</code></pre>',
                'module_id' => '2',
            ],
            [
                'title' => 'Enkapsulasi & Information Hiding',
                'content' => '<h2>3. Prinsip "Bungkus" dan Keamanan</h2>
                <p>Pernahkah Anda bertanya kenapa tombol di remote TV tertutup plastik? Itu adalah enkapsulasi. Anda hanya bisa menekan tombol (Interface), tapi tidak bisa menyentuh sirkuit di dalamnya (Implementasi).</p>
                
                <h3>Information Hiding</h3>
                <p>Tujuannya adalah menyembunyikan detail kompleks agar pengguna objek tidak melakukan kesalahan yang merusak integritas data.</p>
                
                <h3>Level Akses (Access Modifiers)</h3>
                <ul class="space-y-2">
                    <li>🔴 <strong>private:</strong> Tingkat paling ketat. Hanya bisa diakses oleh kode di dalam Class itu sendiri.</li>
                    <li>🟡 <strong>protected:</strong> Bisa diakses oleh kelas di package yang sama dan semua subclass (anaknya).</li>
                    <li>🟢 <strong>public:</strong> Terbuka untuk siapapun di manapun.</li>
                </ul>

                <h3>Kenapa butuh Getter & Setter?</h3>
                <p>Dengan Getter/Setter, kita bisa melakukan validasi sebelum data diubah.</p>

                <pre><code>public class AkunBank {
    private double saldo; // Disembunyikan

    public void setSaldo(double jumlah) {
        if (jumlah >= 0) { // Validasi: Saldo tak boleh minus
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
                <p>Dalam sistem besar, class tidak berdiri sendiri. Mereka menjalin hubungan atau relasi.</p>
                
                <h3>A. Association (Asosiasi)</h3>
                <p>Hubungan "menggunakan" atau "bekerja sama". Contoh: <code>Dosen</code> mengajar <code>Mahasiswa</code>. Keduanya mandiri.</p>
                
                <h3>B. Aggregation (Agregasi)</h3>
                <p>Hubungan "bagian dari" (Has-a) yang lemah. Contoh: <code>Jurusan</code> memiliki <code>Dosen</code>. Jika Jurusan dihapus, Dosen masih bisa tetap ada.</p>
                
                <h3>C. Composition (Komposisi)</h3>
                <p>Hubungan "kepemilikan mutlak". Contoh: <code>Manusia</code> memiliki <code>Jantung</code>. Jika Manusia mati, Jantung tidak bisa berdiri sendiri.</p>

                <blockquote>
                    <strong>UML Insight:</strong><br>
                    Dalam diagram UML, komposisi dilambangkan dengan belah ketupat hitam (full), sedangkan agregasi dilambangkan dengan belah ketupat putih (kosong).
                </blockquote>',
                'module_id' => '4',
            ],
            [
                'title' => 'Inheritance: Pewarisan Kode',
                'content' => '<h2>5. Efisiensi dengan Konsep extends</h2>
                <p>Jangan mengulang kode yang sama! Inheritance memungkinkan kita membuat kelas baru berdasarkan kelas yang sudah ada.</p>
                
                <ul>
                    <li><strong>Superclass:</strong> Induk (misal: <code>Kendaraan</code>).</li>
                    <li><strong>Subclass:</strong> Anak (misal: <code>Mobil</code>, <code>Motor</code>).</li>
                </ul>

                <h3>Keyword Utama: extends & super</h3>
                <p>Keyword <code>extends</code> digunakan untuk mewarisi, sedangkan <code>super</code> digunakan untuk merujuk ke induk.</p>

                <pre><code>public class Hewan {
    public void makan() { System.out.println("Sedang makan..."); }
}

public class Kucing extends Hewan {
    public void meong() { 
        super.makan(); // Panggil method induk
        System.out.println("Meong!"); 
    }
}</code></pre>

                <p><strong>Catatan Penting:</strong> Java tidak mendukung <em>Multiple Inheritance</em> (satu anak punya dua bapak kandung) untuk menghindari ambiguitas kode.</p>',
                'module_id' => '6',
            ],
            [
                'title' => 'Overloading vs Overriding',
                'content' => '<h2>6. Memahami Polimorfisme</h2>
                <p>Polimorfisme (Banyak Bentuk) adalah inti dari fleksibilitas OOP. Mari kita bedah dua teknik utamanya.</p>
                
                <h3>A. Method Overloading (Static Binding)</h3>
                <p>Terjadi di <strong>kelas yang sama</strong>. Method punya nama sama tapi parameter berbeda (jumlah, tipe, atau susunannya).</p>
                
                <h3>B. Method Overriding (Dynamic Binding)</h3>
                <p>Terjadi di <strong>subclass</strong>. Method punya nama dan parameter yang <strong>persis sama</strong> dengan induknya, tapi isinya diubah.</p>

                <table>
                    <thead>
                        <tr>
                            <th class="p-3 text-left">Fitur</th>
                            <th class="p-3 text-left">Overloading</th>
                            <th class="p-3 text-left">Overriding</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-3">Lokasi</td>
                            <td class="p-3">Kelas yang sama</td>
                            <td class="p-3">Subclass (Anak)</td>
                        </tr>
                        <tr>
                            <td class="p-3">Parameter</td>
                            <td class="p-3">Harus Berbeda</td>
                            <td class="p-3">Harus Sama</td>
                        </tr>
                        <tr>
                            <td class="p-3">Kapan Ditentukan</td>
                            <td class="p-3">Compile-time</td>
                            <td class="p-3">Run-time</td>
                        </tr>
                    </tbody>
                </table>',
                'module_id' => '7',
            ],
            [
                'title' => 'Abstract Class & Method',
                'content' => '<h2>7. Mendefinisikan Guideline Hirarki</h2>
                <p>Terkadang kita tahu sebuah Class <em>harus</em> punya kemampuan tertentu, tapi kita belum tahu detail cara melakukannya. Di sinilah <strong>Abstract Class</strong> berperan.</p>
                
                <h3>Karakteristik Abstract Class</h3>
                <ul>
                    <li>Tidak bisa dibuat objeknya langsung (cannot be instantiated).</li>
                    <li>Bisa punya method biasa (concrete) dan method abstrak.</li>
                    <li>Memaksa subclass untuk mengimplementasikan method abstrak tersebut.</li>
                </ul>

                <pre><code>public abstract class BangunDatar {
    // Kita tahu bangun datar punya luas, tapi rumusnya beda-beda
    public abstract double hitungLuas(); 
    
    public void sapa() { System.out.println("Halo!"); }
}</code></pre>

                <p>Gunakan Abstract Class saat Anda ingin membuat template dasar untuk kelas-kelas yang masih satu keluarga (hierarki).</p>',
                'module_id' => '9',
            ],
            [
                'title' => 'Interface: Kontrak Perilaku',
                'content' => '<h2>8. Kapabilitas Lintas Hierarki</h2>
                <p>Interface adalah "kontrak" murni. Ia hanya berisi daftar kemampuan (apa yang bisa dilakukan) tanpa detail cara melakukannya.</p>
                
                <h3>Kenapa butuh Interface?</h3>
                <ol>
                    <li><strong>Multiple Inheritance:</strong> Satu kelas bisa mengimplementasi banyak interface sekaligus.</li>
                    <li><strong>Decoupling:</strong> Memisahkan "apa yang dilakukan" dari "siapa yang melakukan".</li>
                </ol>

                <pre><code>public interface Terbangable {
    void terbang(); // Secara otomatis public abstract
}

// Burung dan Pesawat tidak satu keluarga, tapi sama-sama bisa terbang
public class Burung implements Terbangable {
    public void terbang() { System.out.println("Mengepakkan sayap..."); }
}</code></pre>

                <blockquote>
                    <strong>Pro-Tip:</strong> Abstract Class menjawab "Siapa kamu?", sedangkan Interface menjawab "Kamu bisa apa?".
                </blockquote>',
                'module_id' => '10',
            ],
            [
                'title' => 'Polimorfisme Lanjutan & Casting',
                'content' => '<h2>9. Fleksibilitas Level Tinggi</h2>
                <p>Konsep polimorfisme memungkinkan kita memperlakukan objek anak sebagai objek induk, memberikan fleksibilitas luar biasa pada arsitektur kode.</p>
                
                <h3>Object Casting</h3>
                <p>Ini adalah proses mengubah tipe referensi objek.</p>
                <ul>
                    <li><strong>Upcasting:</strong> Mengubah <code>Dosen</code> menjadi <code>Pegawai</code>. Ini aman dan otomatis.</li>
                    <li><strong>Downcasting:</strong> Mengubah <code>Pegawai</code> kembali menjadi <code>Dosen</code>. Ini harus dilakukan manual dan hati-hati.</li>
                </ul>

                <pre><code>Pegawai p = new Dosen(); // Upcasting

// Cek dulu sebelum downcast agar tidak error ClassCastException
if (p instanceof Dosen) {
    Dosen d = (Dosen) p; 
    d.ajarMatkul();
}</code></pre>

                <h3>Heterogenous Collection</h3>
                <p>Berkat polimorfisme, kita bisa menyimpan berbagai objek yang berbeda (tapi satu induk) dalam satu array tunggal.</p>
                <pre><code>Hewan[] kebunBinatang = { new Singa(), new Gajah(), new Kucing() };
for (Hewan h : kebunBinatang) {
    h.bersuara(); // Setiap hewan bersuara dengan caranya sendiri
}</code></pre>',
                'module_id' => '11',
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
