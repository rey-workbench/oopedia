<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\SubMaterial;
use Illuminate\Database\Seeder;

class SubMaterialsSeeder extends Seeder
{
    public function run(): void
    {
        $materialTopicMap = [
            1 => [ // Pengantar Konsep Dasar OOP
                [
                    'title' => 'Paradigma PBO vs Struktural: Analisis Mendalam (Teori)',
                    'jenis' => 'teori',
                    'style' => 'textual',
                    'content' => '<h3>Evolusi Pemecahan Masalah Perangkat Lunak</h3>
                    <p>Pemrograman Struktural (atau Prosedural) telah lama menjadi fondasi pengembangan perangkat lunak, dengan fokus pada pengolahan data melalui serangkaian fungsi atau prosedur. Namun, seiring dengan meningkatnya kompleksitas sistem—seperti aplikasi perbankan atau sistem manajemen rumah sakit—pendekatan ini mulai menemui batasnya. Masalah utamanya adalah ketergantungan yang terlalu kuat antara data dan fungsi (<em>Tight Coupling</em>), di mana perubahan kecil pada struktur data dapat merusak fungsi-fungsi yang tersebar di seluruh sistem.</p>
                    <p>Pemrograman Berorientasi Objek (PBO) mentransformasi pendekatan ini dengan memperkenalkan konsep <strong>Objek</strong>. Dalam PBO, data tidak lagi diproses secara bebas di luar, melainkan menjadi bagian internal dari objek itu sendiri. Perubahan pada struktur data internal sebuah objek tidak akan merusak bagian luar sistem selama antarmuka komunikasinya tetap sama.</p>
                    <p>Keunggulan utama PBO meliputi:</p>
                    <ul>
                        <li><strong>Modularity:</strong> Program dibagi menjadi modul-modul yang mandiri dan dapat diuji secara terpisah.</li>
                        <li><strong>Maintainability:</strong> Kode lebih mudah diperbaiki karena lokasi kesalahan biasanya terlokalisasi di objek tertentu.</li>
                        <li><strong>Scalability:</strong> Memudahkan penambahan fitur baru dengan sistem yang sudah ada melalui ekstensibilitas objek.</li>
                    </ul>
                    <p>Dengan menguasai PBO, seorang pengembang beralih dari sekadar menulis instruksi ke membangun arsitektur sistem yang elastis dan adaptif terhadap perubahan kebutuhan bisnis.</p>',
                ],
                [
                    'title' => 'Struktur Teknis dan Konvensi Java (Sintaks)',
                    'jenis' => 'sintaks',
                    'style' => 'visual',
                    'content' => '<h3>Anatomi dan Tata Tulis Kelas Profesional</h3>
                    <p>Dalam ekosistem Java, penulisan kode harus mengikuti standar industri untuk memudahkan kolaborasi. Nama kelas selalu menggunakan <strong>PascalCase</strong> (contoh: <code>SistemAkademik</code>), sedangkan metode dan atribut menggunakan <strong>camelCase</strong> (contoh: <code>hitungTotalHarga</code>).</p>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">/* Struktur Kelas Sederhana */</div>
                        <div class="ql-code-block">public class Karyawan {</div>
                        <div class="ql-code-block">    // Atribut/Fields</div>
                        <div class="ql-code-block">    private String nama;</div>
                        <div class="ql-code-block">    private double gaji;</div>
                        <div class="ql-code-block"></div>
                        <div class="ql-code-block">    // Konstruktor</div>
                        <div class="ql-code-block">    public Karyawan(String nama, double gaji) {</div>
                        <div class="ql-code-block">        this.nama = nama;</div>
                        <div class="ql-code-block">        this.gaji = gaji;</div>
                        <div class="ql-code-block">    }</div>
                        <div class="ql-code-block"></div>
                        <div class="ql-code-block">    // Metode</div>
                        <div class="ql-code-block">    public void tampilkanProfil() {</div>
                        <div class="ql-code-block">        System.out.println("Nama: " + nama + ", Gaji: " + gaji);</div>
                        <div class="ql-code-block">    }</div>
                        <div class="ql-code-block">}</div>
                    </div>',
                ],
                [
                    'title' => 'Visualisasi Konsep Dasar OOP (Visual)',
                    'jenis' => 'mixed',
                    'style' => 'visual',
                    'content' => '<h3>Memahami OOP Secara Visual</h3>
                    <p>Simak penjelasan interaktif mengenai dasar-dasar pemrograman berorientasi objek dalam video dari Kelas Terbuka di bawah ini untuk mendapatkan gambaran menyeluruh.</p>
                    <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); margin: 2rem 0;">
                        <iframe src="https://www.youtube.com/embed/bxOPd_b0rg4" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                    <p>Video ini menjelaskan filosofi dan alasan mengapa kita beralih ke paradigma bebasis objek dalam pengembangan aplikasi skala menengah hingga besar.</p>',
                ],
            ],
            2 => [ // Class dan Object
                [
                    'title' => 'Manajemen Memori: Stack vs Heap (Teori)',
                    'jenis' => 'teori',
                    'style' => 'textual',
                    'content' => '<h3>Bagaimana Objek Hidup di Komputer Anda</h3>
                    <p>Saat Anda melakukan instansiasi sebuah objek di Java, referensi (nama variabel) disimpan di memori <strong>Stack</strong>, namun isi data objek disimpan di memori <strong>Heap</strong>.</p>
                    <p>Konsekuensi dari arsitektur ini adalah:</p>
                    <ul>
                        <li>Beberapa variabel bisa merujuk ke satu objek fisik yang sama di Heap.</li>
                        <li>Perubahan pada satu referensi akan terlihat oleh referensi lain yang menunjuk ke objek yang sama.</li>
                        <li>Garbage Collector akan membersihkan objek di Heap jika sudah tidak ada referensi lagi yang menunjuk ke objek tersebut.</li>
                    </ul>',
                ],
                [
                    'title' => 'Siklus Instansiasi dan Konstruktor (Sintaks)',
                    'jenis' => 'sintaks',
                    'style' => 'visual',
                    'content' => '<h3>Penciptaan Objek dengan Kata Kunci New</h3>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">public class Produk {</div>
                        <div class="ql-code-block">    String id;</div>
                        <div class="ql-code-block">    public Produk(String id) { this.id = id; }</div>
                        <div class="ql-code-block">}</div>
                        <div class="ql-code-block"></div>
                        <div class="ql-code-block">// Membuat instance di memory</div>
                        <div class="ql-code-block">Produk p1 = new Produk("A01");</div>
                    </div>',
                ],
                [
                    'title' => 'Visualisasi Class dan Object (Visual)',
                    'jenis' => 'mixed',
                    'style' => 'visual',
                    'content' => '<h3>Analogi Blueprint dan Produk Nyata</h3>
                    <p>Tonton video berikut untuk memahami perbedaan antara "cetakan" (Class) dan "hasil cetakan" (Object) melalui demonstrasi langsung.</p>
                    <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); margin: 2rem 0;">
                        <iframe src="https://www.youtube.com/embed/aQRemTq6Two" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>',
                ],
            ],
            3 => [ // Enkapsulasi
                [
                    'title' => 'Prinsip Black Box dan Keamanan Data (Teori)',
                    'jenis' => 'teori',
                    'style' => 'textual',
                    'content' => '<h3>Menyembunyikan Kompleksitas Internal</h3>
                    <p>Enkapsulasi membungkus data sensitif di dalam objek dan menutup akses langsung dari luar. Ini menciptakan keamanan data dan mencegah korupsi state objek oleh bagian program lain yang tidak sah.</p>
                    <p>Manfaat utamanya adalah kita bisa mengubah logika internal kelas tanpa merusak bagian program lain yang menggunakannya, selama antarmuka publiknya tetap konsisten.</p>',
                ],
                [
                    'title' => 'Implementasi Modifier dan Getter/Setter (Sintaks)',
                    'jenis' => 'sintaks',
                    'style' => 'visual',
                    'content' => '<h3>Mekanisme Access Control</h3>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">public class Rekening {</div>
                        <div class="ql-code-block">    private double saldo; // Data tersembunyi</div>
                        <div class="ql-code-block"></div>
                        <div class="ql-code-block">    public double getSaldo() { return saldo; }</div>
                        <div class="ql-code-block">    public void setSaldo(double s) { if(s >= 0) saldo = s; }</div>
                        <div class="ql-code-block">}</div>
                    </div>',
                ],
                [
                    'title' => 'Visualisasi Enkapsulasi (Visual)',
                    'jenis' => 'mixed',
                    'style' => 'visual',
                    'content' => '<h3>Kenapa Kita Butuh Enkapsulasi?</h3>
                    <p>Video ini memberikan visualisasi mendalam tentang bagaimana Access Modifier dan Getter/Setter bekerja melindungi data.</p>
                    <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); margin: 2rem 0;">
                        <iframe src="https://www.youtube.com/embed/zwDMHJzTUzs" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>',
                ],
            ],
            4 => [ // Relasi Class
                [
                    'title' => 'Referensi dan Struktur Hubungan Objek (Teori)',
                    'jenis' => 'teori',
                    'style' => 'textual',
                    'content' => '<h3>Bagaimana Objek Saling Berkomunikasi</h3>
                    <p>Sistem OOP yang kompleks terdiri dari banyak objek yang saling merujuk. Penting untuk memahami bagaimana "Reference" bekerja agar tidak terjadi kesalahan memori atau data yang tidak sinkron saat satu objek digunakan oleh objek lainnya.</p>',
                ],
                [
                    'title' => 'Implementasi Relasi Has-A (Sintaks)',
                    'jenis' => 'sintaks',
                    'style' => 'visual',
                    'content' => '<h3>Struktur Referensi Objek</h3>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">public class Mobil {</div>
                        <div class="ql-code-block">    private Mesin mesin; // Relasi</div>
                        <div class="ql-code-block">    public Mobil(Mesin m) { this.mesin = m; }</div>
                        <div class="ql-code-block">}</div>
                    </div>',
                ],
                [
                    'title' => 'Visualisasi Reference dan Relasi (Visual)',
                    'jenis' => 'mixed',
                    'style' => 'visual',
                    'content' => '<h3>Memahami Reference pada Object</h3>
                    <p>Sebelum masuk ke relasi yang lebih kompleks seperti Agregasi dan Komposisi, penting untuk memahami bagaimana variabel menyimpan referensi ke objek di memori Heap.</p>
                    <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); margin: 2rem 0;">
                        <iframe src="https://www.youtube.com/embed/iLyKXuH5xis" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>',
                ],
            ],
            5 => [ // Inheritance
                [
                    'title' => 'Hierarki Kelas dan Pewarisan Sifat (Teori)',
                    'jenis' => 'teori',
                    'style' => 'textual',
                    'content' => '<h3>Konsep Is-A dalam Pemrograman</h3>
                    <p>Inheritance memungkinkan sebuah kelas (Subclass) mewarisi semua sifat public dan protected dari kelas induk (Superclass). Ini sangat efektif untuk menghindari pengulangan kode (DRY Principle).</p>',
                ],
                [
                    'title' => 'Keyword Extends dan Super (Sintaks)',
                    'jenis' => 'sintaks',
                    'style' => 'visual',
                    'content' => '<h3>Membangun Koneksi Antar Kelas</h3>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">public class Hewan {</div>
                        <div class="ql-code-block">    public void makan() { System.out.println("Makan..."); }</div>
                        <div class="ql-code-block">}</div>
                        <div class="ql-code-block">public class Kucing extends Hewan {</div>
                        <div class="ql-code-block">    public void meong() { System.out.println("Meong!"); }</div>
                        <div class="ql-code-block">}</div>
                    </div>',
                ],
                [
                    'title' => 'Visualisasi Inheritance (Visual)',
                    'jenis' => 'mixed',
                    'style' => 'visual',
                    'content' => '<h3>Hierarki Pewarisan di Java</h3>
                    <p>Tonton video ini untuk melihat bagaimana struktur pewarisan dibangun langkah demi langkah beserta aturan-aturannya.</p>
                    <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); margin: 2rem 0;">
                        <iframe src="https://www.youtube.com/embed/CXqOqqe7zjo" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>',
                ],
            ],
            6 => [ // Overriding dan Overloading
                [
                    'title' => 'Polimorfisme Statis vs Dinamis (Teori)',
                    'jenis' => 'teori',
                    'style' => 'textual',
                    'content' => '<h3>Modifikasi Perilaku Metode</h3>
                    <p><strong>Overloading</strong> memungkinkan satu nama metode memiliki banyak variasi parameter (Polimorfisme Statis). <strong>Overriding</strong> memungkinkan kelas anak untuk mengganti perilaku metode yang diwarisi dari induknya.</p>',
                ],
                [
                    'title' => 'Anotasi @Override dan Signature (Sintaks)',
                    'jenis' => 'sintaks',
                    'style' => 'visual',
                    'content' => '<h3>Koding Metode Adaptif</h3>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">@Override</div>
                        <div class="ql-code-block">public void bersuara() {</div>
                        <div class="ql-code-block">    System.out.println("Guk guk!"); </div>
                        <div class="ql-code-block">}</div>
                    </div>',
                ],
                [
                    'title' => 'Video: Overriding Methods (Visual)',
                    'jenis' => 'mixed',
                    'style' => 'visual',
                    'content' => '<h3>Mengganti Perilaku di Kelas Anak</h3>
                    <p>Video ini menjelaskan bagaimana Overriding bekerja saat sebuah subclass ingin menyesuaikan perilaku yang sudah ada di superclassnya.</p>
                    <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); margin: 2rem 0;">
                        <iframe src="https://www.youtube.com/embed/7okH5nc2LEc" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>',
                ],
            ],
            7 => [ // Abstract Class
                [
                    'title' => 'Template dan Abstraksi Struktur (Teori)',
                    'jenis' => 'teori',
                    'style' => 'textual',
                    'content' => '<h3>Membangun Pondasi Logika</h3>
                    <p>Abstract Class adalah kelas yang tidak bisa dibuat objeknya secara langsung. Ia berfungsi sebagai blueprint dasar yang WAJIB dilengkapi oleh kelas-kelas turunannya.</p>',
                ],
                [
                    'title' => 'Sintaks Abstract Method (Sintaks)',
                    'jenis' => 'sintaks',
                    'style' => 'visual',
                    'content' => '<h3>Mendefinisikan Kontrak di Kelas Induk</h3>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">public abstract class BangunDatar {</div>
                        <div class="ql-code-block">    public abstract double hitungLuas(); </div>
                        <div class="ql-code-block">}</div>
                    </div>',
                ],
                [
                    'title' => 'Visualisasi Abstract Class (Visual)',
                    'jenis' => 'mixed',
                    'style' => 'visual',
                    'content' => '<h3>Konsep Kelas yang Belum Selesai</h3>
                    <p>Video ini menjelaskan kenapa kita membutuhkan Abstract Class untuk menciptakan struktur sistem yang lebih terorganisir.</p>
                    <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); margin: 2rem 0;">
                        <iframe src="https://www.youtube.com/embed/7N3lxM05cw0" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>',
                ],
            ],
            8 => [ // Interface
                [
                    'title' => 'Kontrak Sifat dan Abstraksi Murni (Teori)',
                    'jenis' => 'teori',
                    'style' => 'textual',
                    'content' => '<h3>Definisi Kemampuan Suatu Objek</h3>
                    <p>Interface adalah kontrak yang mendefinisikan "apa yang bisa dilakukan" oleh sebuah objek. Ini memungkinkan Multiple Inheritance di Java.</p>',
                ],
                [
                    'title' => 'Implement Kontrak Interface (Sintaks)',
                    'jenis' => 'sintaks',
                    'style' => 'visual',
                    'content' => '<h3>Penulisan Implements di Java</h3>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">public interface CanFly { void fly(); }</div>
                        <div class="ql-code-block">public class Plane implements CanFly {</div>
                        <div class="ql-code-block">    public void fly() { System.out.println("Terbang..."); }</div>
                        <div class="ql-code-block">}</div>
                    </div>',
                ],
                [
                    'title' => 'Visualisasi Interface (Visual)',
                    'jenis' => 'mixed',
                    'style' => 'visual',
                    'content' => '<h3>Interface sebagai Standarisasi</h3>
                    <p>Mempelajari Interface membantu kita memahami bagaimana standarisasi kode dilakukan dalam proyek skala besar.</p>
                    <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); margin: 2rem 0;">
                        <iframe src="https://www.youtube.com/embed/ztQPFMFEItI" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>',
                ],
            ],
            9 => [ // Polimorfisme
                [
                    'title' => 'Dynamic Binding dan Satu Nama Banyak Wujud (Teori)',
                    'jenis' => 'teori',
                    'style' => 'textual',
                    'content' => '<h3>Level Tertinggi Abstraksi Objek</h3>
                    <p>Polimorfisme memungkinkan sebuah variabel induk menampung objek dari turunan manapun. JVM akan menentukan metode mana yang dipanggil saat runtime.</p>',
                ],
                [
                    'title' => 'Contoh Polimorfisme (Sintaks)',
                    'jenis' => 'sintaks',
                    'style' => 'visual',
                    'content' => '<h3>Implementasi Polimorfisme</h3>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">Hewan h = new Kucing(); // Polymorphic call</div>
                        <div class="ql-code-block">h.bersuara(); // Memanggil meong()</div>
                    </div>',
                ],
                [
                    'title' => 'Visualisasi Polimorfisme (Visual)',
                    'jenis' => 'mixed',
                    'style' => 'visual',
                    'content' => '<h3>Kekuatan Sebenarnya dari Polimorfisme</h3>
                    <p>Video ini menjelaskan bagaimana polimorfisme membuat kode kita sangat fleksibel dan mudah untuk dikembangkan di masa depan.</p>
                    <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); margin: 2rem 0;">
                        <iframe src="https://www.youtube.com/embed/hvrS5b0k4Jk" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>',
                ],
            ],
        ];

        foreach ($materialTopicMap as $moduleId => $subTopics) {
            $material = Material::where('module_id', $moduleId)->first();

            if (!$material) {
                continue;
            }

            // Clear existing sub-materials for this material to ensure clean update
            $material->subMaterials()->delete();

            foreach ($subTopics as $index => $topic) {
                SubMaterial::create([
                    'material_id' => $material->id,
                    'title' => $topic['title'],
                    'content' => $topic['content'],
                    'jenis_konten' => $topic['jenis'] ?? 'teori',
                    'learning_style' => $topic['style'] ?? 'textual',
                    'order' => $index + 1,
                ]);
            }
        }
    }
}
