<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialsSeeder extends Seeder
{
    public function run(): void
    {
        Material::query()->delete();
        // never use custom styling like tailwind etc only use basic html tag!
        $materials = [
            [
                'title'     => 'Paradigma OOP vs Struktural',
                'cover_url' => 'materials/fd155d81676941892f9105cd2fe67127.png',
                'content'   => '
                <h2>Paradigma Prosedural VS Object Oriented Programming (OOP)</h2>
                
                <iframe 
                    src="https://www.youtube.com/embed/bxOPd_b0rg4" 
                    title="YouTube video player" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    allowfullscreen
                ></iframe>

                <h3>1. Paradigma Prosedural (Imperatif)</h3>
                <p>Paradigma Prosedural atau dikenal juga dengan paradigma imperatif menggunakan metode pemrograman dengan mengeluarkan perintah yang akan dieksekusi oleh komputer. Baris demi baris dieksekusi secara berurutan mulai dari baris atas hingga bawah, dimana semua data dan kode digabung menjadi satu bagian dalam satu program.</p>
                
                <blockquote>
                    <strong>Kata kunci dalam paradigma ini adalah:</strong><br>
                    Algoritma + Struktur Data = Program
                </blockquote>

                <p>Contoh bahasa pemrogaman yang menggunakan paradigma ini adalah bahasa-bahasa pemrograman tingkat tinggi seperti <strong>Cobol, Basic, Pascal, Fortran, dan C</strong>.</p>
                
                <ul>
                    <li><strong>Keuntungan:</strong> Kesederhanaan, efisiensi, dan keefektifan eksekusi barisan perintah program karena sangat dekat dengan bahasa mesin. Struktur logikanya benar dan mudah dipahami karena hanya memiliki 3 struktur dasar (berurutan, seleksi, perulangan).</li>
                    <li><strong>Kekurangan:</strong> Cara penulisan program jauh dari "kebiasaan manusia" dan tidak alamiah. Program cukup sulit untuk dirawat karena susah untuk diubah tanpa harus mempengaruhi fungsi sistem secara keseluruhan.</li>
                </ul>

                <img src="https://miro.medium.com/v2/resize:fit:1336/1*Iomd50CfA4SmDWEPiTaAaA.png" alt="Programming Evolution" />

                <h3>2. Paradigma Object Oriented Programming (OOP)</h3>
                <p>Paradigma OOP berdasarkan pada kelas dan objek dengan cara memodelkan semua hal seperti dalam dunia nyata. Paradigma ini menawarkan konsep modular, kemudahan untuk digunakan kembali, dan kemudahan modifikasi.</p>
                
                <ul>
                    <li><strong>Kelebihan:</strong> Cukup mendefinisikan class sekali (reusable), dapat menambahkan fitur tanpa mengedit class asal (extensible), data dapat diatur secara private (encapsulation), dan memudahkan pembangunan sistem informasi melalui library-library.</li>
                    <li><strong>Kelemahan:</strong> Membutuhkan ruang memori yang lebih besar dibandingkan dengan pemrograman terstruktur, dan karena sangat responsive maka program dapat dengan mudah diurai (risiko security).</li>
                </ul>

                <p>Banyak bahasa pemrograman yang menggunakan paradigma OOP, yaitu bahasa pemrograman <strong>Java, C++, Ruby, Python, PHP, Perl,</strong> dan lain-lain.</p>

                <h3>Checklist Penguasaan</h3>
                <ul>
                    <li>Paham perbedaan mendasar data vs prosedur.</li>
                    <li>Tahu alasan kenapa OOP lebih modular dan mudah digunakan kembali.</li>
                    <li>Bisa menyebutkan contoh bahasa pemrograman untuk masing-masing paradigma.</li>
                </ul>
                
                <hr>
                <p><strong>Referensi:</strong></p>
                <ol>
                    <li>Ndower. <em>Paradigma Pemrograman</em>. <a href="https://ndoware.com/paradigma-pemrograman.html" target="_blank">https://ndoware.com/paradigma-pemrograman.html</a></li>
                    <li>Soshace. <em>Functional vs Object-Oriented Programming</em>. <a href="https://developer.mozilla.org/en-US/docs/multiparadigmlanguage.html" target="_blank">https://developer.mozilla.org/en-US/docs/multiparadigmlanguage.html</a></li>
                    <li>Kursuswebsite. <em>Perbedaan Antara Procedural Programming dengan Object Oriented Programming</em>. <a href="https://developer.mozilla.org/en-US/docs/multiparadigmlanguage.html" target="_blank">https://developer.mozilla.org/en-US/docs/multiparadigmlanguage.html</a></li>
                </ol>',
                'module_id' => '1',
            ],
            [
                'title'     => 'Struktur Dasar: Class & Object Java',
                'cover_url' => 'materials/22acc243206cd9073603ff88f8a9a46b.png',
                'content'   => '<h2>Konsep Class dan Objek dalam Pemrograman Java</h2>
                <p>Pemrograman Berorientasi Objek atau <em>Object-Oriented Programming</em> (OOP) merupakan paradigma utama dalam Java. Dua konsep ini menjadi fondasi dasar yang memungkinkan kita membangun sistem yang terstruktur, modular, dan mudah dikembangkan.</p>

                <iframe 
                    src="https://www.youtube.com/embed/aQRemTq6Two" 
                    title="YouTube video player" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    allowfullscreen
                ></iframe>

                <h3>1. Mengapa Class dan Objek Penting?</h3>
                <p>Saat kita memandang sebuah sistem kompleks, cara paling mudah untuk memahaminya adalah dengan memecahnya menjadi entitas kecil. Misalnya, dalam sistem perpustakaan terdapat entitas seperti <strong>Buku, Anggota, dan Pustakawan</strong>. Inilah inti OOP: merepresentasikan dunia nyata dalam kode.</p>
                
                <ul>
                    <li><strong>Organisasi:</strong> Kode lebih rapi dan terkelompok.</li>
                    <li><strong>Reusability:</strong> Class bisa digunakan berulang kali.</li>
                    <li><strong>Scalability:</strong> Sistem lebih mudah diperbesar di masa depan.</li>
                </ul>

                <hr>

                <h3>2. Apa Itu Class?</h3>
                <p><strong>Class</strong> adalah blueprint, template, atau cetakan untuk membuat objek. Class mendeskripsikan apa yang dimiliki (atribut) dan apa yang dapat dilakukan (method) oleh objek.</p>

                <blockquote>
                    <strong>Analogi Dunia Nyata 🏎️</strong><br>
                    Blueprint <strong>"Mobil"</strong> mencakup atribut (warna, merek) dan metode (berjalan, berhenti). Class bukan mobil itu sendiri, melainkan <strong>rancangan</strong> untuk membuatnya.
                </blockquote>

                <p><strong>Struktur Dasar di Java:</strong></p>
                <pre><code>public class Mobil {
    String merk;
    String warna;

    void berjalan() {
        System.out.println(merk + " sedang berjalan...");
    }
}</code></pre>

                <hr>

                <h3>3. Apa Itu Objek?</h3>
                <p><strong>Objek</strong> adalah <em>instance</em> (perwujudan nyata) dari sebuah class. Jika class adalah cetakannya, objek adalah produk nyata yang dihasilkan.</p>
                
                <p><strong>Contoh Instansiasi Objek:</strong></p>
                <pre><code>Mobil mobil1 = new Mobil();
mobil1.merk = "Toyota";
mobil1.warna = "Merah";

Mobil mobil2 = new Mobil();
mobil2.merk = "Honda";
mobil2.warna = "Hitam";</code></pre>

                <hr>

                <h3>4. Elemen Penting dalam Class</h3>
                <ul>
                    <li><strong>Atribut (Fields):</strong> Menyimpan data yang melekat pada objek (misal: <code>kecepatan</code>, <code>merk</code>).</li>
                    <li><strong>Method:</strong> Fungsi yang menggambarkan perilaku objek (misal: <code>berhenti()</code>, <code>belok()</code>).</li>
                    <li><strong>Constructor:</strong> Metode khusus untuk menginisialisasi objek saat pertama kali dibuat (menggunakan keyword <code>new</code>).</li>
                </ul>

                <hr>

                <h3>5. Contoh Implementasi Lengkap</h3>
                <p>Mari kita buat class <code>Mahasiswa</code> yang merepresentasikan entitas nyata di kampus:</p>
                
                <pre><code>public class Mahasiswa {
    String nama;
    String nim;
    String jurusan;

    // Constructor
    public Mahasiswa(String nama, String nim, String jurusan) {
        this.nama = nama;
        this.nim = nim;
        this.jurusan = jurusan;
    }

    public void perkenalan() {
        System.out.println("Halo, saya " + nama + " dari jurusan " + jurusan);
    }
}</code></pre>

                <p><strong>Cara Penggunaan:</strong></p>
                <pre><code>Mahasiswa m1 = new Mahasiswa("Andi", "12345", "Informatika");
m1.perkenalan(); // Output: Halo, saya Andi dari jurusan Informatika</code></pre>

                <h3>Checklist Penguasaan</h3>
                <ul>
                    <li>Bisa menjelaskan perbedaan Blueprint (Class) dan Realisasi (Object).</li>
                    <li>Paham peran Atribut dan Method dalam sebuah Class Java.</li>
                    <li>Bisa melakukan instansiasi objek menggunakan keyword <code>new</code>.</li>
                    <li>Tahu cara menggunakan Constructor untuk inisialisasi data awal.</li>
                </ul>',
                'module_id' => '2',
            ],
            [
                'title'     => 'Enkapsulasi & Information Hiding',
                'cover_url' => 'materials/67d38a5cb641fc35e70c53310c4b634d.png',
                'content'   => '<h2>Enkapsulasi: Melindungi dan Memaketkan Data</h2>
                <p>Enkapsulasi adalah proses pemaketan atau penyatuan data bersama metode-metodenya, di mana hal ini bermanfaat untuk menyembunyikan rincian implementasi dari pemakai.</p>

                <blockquote>
                    <strong>Tujuan Utama:</strong> Menjaga suatu proses program agar tidak dapat diakses secara sembarangan atau diintervensi oleh program lain secara ilegal.
                </blockquote>

                <h3>Analogi Dunia Nyata: Generator Listrik ⚡</h3>
                <p>Bayangkan arus listrik pada generator dan sistem perputarannya. Kerja arus listrik tidak mempengaruhi sistem perputaran generator secara langsung, dan sebaliknya. Sebagai pengguna, kita tidak perlu tahu detail mekanis generator (apakah berputar ke depan atau belakang), kita hanya perlu tahu cara mengambil arus listriknya. Itulah enkapsulasi: <strong>menyembunyikan kerumitan internal.</strong></p>

                <hr>

                <h3>Manfaat Utama Enkapsulasi</h3>
                <ul>
                    <li><strong>Penyembunyian Informasi (Information Hiding):</strong> Melindungi implementasi internal objek. Bagian internal dapat berubah tanpa mempengaruhi bagian program lain selama interface-nya tetap.</li>
                    <li><strong>Modularitas:</strong> Objek dapat dikelola secara independen. Modifikasi internal tidak akan menyebabkan masalah pada sistem lain (decoupling).</li>
                </ul>

                <hr>

                <h3>Video Tutorial: Enkapsulasi</h3>
                <iframe 
                    src="https://www.youtube.com/embed/R3_M3U3O1zQ" 
                    title="Tutorial Enkapsulasi Java" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>

                <h3>Kendali Akses (Access Modifiers)</h3>
                <p>Dalam Java, pengkapsulan dilakukan dengan kelas dan pengendalian akses menggunakan keyword modifier. Berikut adalah tabel karakteristik jangkauan aksesnya:</p>
                
                <img src="https://media.geeksforgeeks.org/wp-content/uploads/20230222165010/encapsulation-in-java.png" alt="Encapsulation Diagram">
                <p><em>Visualisasi: Data (atribut) dibungkus oleh metode sebagai gerbang akses.</em></p>

                <table>
                    <thead>
                        <tr>
                            <th>Modifier</th>
                            <th>Class & Interface</th>
                            <th>Method & Variabel</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Default (Friendly)</strong></td>
                            <td>Dikenali di paketnya saja.</td>
                            <td>Dapat diakses oleh class yang sepaket.</td>
                        </tr>
                        <tr>
                            <td><strong>Public</strong></td>
                            <td>Dikenali di manapun.</td>
                            <td>Dapat diakses di manapun oleh semua class.</td>
                        </tr>
                        <tr>
                            <td><strong>Protected</strong></td>
                            <td>Tidak dapat diterapkan.</td>
                            <td>Diwarisi oleh semua subclass dan dapat diakses class sepaket.</td>
                        </tr>
                        <tr>
                            <td><strong>Private</strong></td>
                            <td>Tidak dapat diterapkan.</td>
                            <td>Hanya dapat diakses oleh class yang memilikinya saja.</td>
                        </tr>
                    </tbody>
                </table>

                <p><strong>Contoh Perbedaan Public vs Private:</strong></p>
                <pre><code>class Belajar {
    public String x = "Pintar";
    private String y = "Java";
}

public class Main {
    public static void main(String[] args) {
        Belajar panggil = new Belajar();
        System.out.println(panggil.x); // Berhasil
        // System.out.println(panggil.y); // ERROR: y bersifat private
    }
}</code></pre>

                <hr>

                <h3>Implementasi Getter dan Setter</h3>
                <p>Untuk mengakses atribut <code>private</code> secara aman (Information Hiding), kita menggunakan metode <strong>Getter</strong> (mengambil data) dan <strong>Setter</strong> (mengubah data).</p>
                
                <pre><code>public class Mahasiswa {
    private String nama;

    public void setNama(String newValue) {
        this.nama = newValue;
    }

    public String getNama() {
        return nama;
    }
}</code></pre>

                <h3>Checklist Penguasaan</h3>
                <ul>
                    <li>Paham definisi dan manfaat Information Hiding.</li>
                    <li>Bisa membedakan jangkauan akses Public, Private, Protected, dan Default.</li>
                    <li>Mampu membuat metode Getter dan Setter untuk melindungi atribut.</li>
                    <li>Paham kenapa modularitas mempermudah pemeliharaan program.</li>
                </ul>',
                'module_id' => '3',
            ],
            [
                'title'     => 'Inheritance: Pewarisan dan Kata Kunci Super',
                'cover_url' => 'materials/b5d3f604a2a25a860792161323183781.png',
                'content'   => '<h2>Pewarisan (Inheritance)</h2>
                <p>Pewarisan merupakan proses penciptaan kelas baru dengan mewarisi karakteristik kelas yang sudah ada (Superclass), ditambah dengan karakteristik unik kelas baru tersebut (Subclass).</p>
                
                <blockquote>
                    <strong>Analogi:</strong> Kelas Hewan (Induk) memiliki atribut ukuran dan sifat. Kelas Karnivora (Anak) mewarisi ukuran dan sifat tersebut, namun menambahkan atribut jenis makanan.
                </blockquote>

                <h3>Video Tutorial: Inheritance</h3>
                <iframe 
                    src="https://www.youtube.com/embed/1F268114l6E" 
                    title="Tutorial Inheritance Java" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>

                <img src="https://blog.visual-paradigm.com/wp-content/uploads/2021/04/Generalization-Relationship.png" alt="Inheritance Diagram">
                <p><em>UML: Panah berujung segitiga kosong menunjukkan hubungan Generalisasi (Inheritance).</em></p>

                <h3>Format Penulisan</h3>
                <pre><code>class Hewan { 
    // body kelas induk
}

class Karnivora extends Hewan {
    // body kelas turunan
}</code></pre>

                <hr>

                <h3>Penggunaan Kata Kunci "Super"</h3>
                <p>Keyword <code>super</code> digunakan untuk mengakses anggota (atribut atau method) milik superclass dari dalam subclass. Paling sering digunakan dalam constructor:</p>
                
                <pre><code>class Kotak {
    double p, l, t;
    Kotak(double p, double l, double t) {
        this.p = p; this.l = l; this.t = t;
    }
}

class KotakPejal extends Kotak {
    double berat;
    KotakPejal(double p, double l, double t, double b) {
        super(p, l, t); // Memanggil constructor Kotak
        this.berat = b;
    }
}</code></pre>

                <h3>Checklist Penguasaan</h3>
                <ul>
                    <li>Bisa menggunakan keyword <code>extends</code> untuk membuat hierarki kelas.</li>
                    <li>Tahu cara memanggil constructor induk menggunakan <code>super()</code>.</li>
                    <li>Paham bahwa subclass mewarisi atribut <code>public</code> dan <code>protected</code> dari induknya.</li>
                </ul>',
                'module_id' => '4',
            ],
            [
                'title'     => 'Polimorfisme: Override dan Overload',
                'cover_url' => 'materials/1dd45384507ccbd8996101ecbfe1055c.png',
                'content'   => '<h2>Polimorfisme (Banyak Bentuk)</h2>
                <p>Polimorfisme adalah kemampuan suatu objek untuk mengungkapkan banyak hal melalui satu cara yang sama. Dalam Java, hal ini diwujudkan melalui Overloading dan Overriding.</p>
                
                <h3>Video Tutorial: Polimorfisme</h3>
                <iframe 
                    src="https://www.youtube.com/embed/R9K1u9ZtN-M" 
                    title="Tutorial Polimorfisme Java" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>

                <h3>1. Overriding (Dynamic Polymorphism)</h3>
                <p>Mendefinisikan kembali method yang sama persis (nama dan parameter) di subclass untuk menggantikan fungsi di superclass.</p>
                <pre><code>class Induk {
    void panggil() { System.out.println("Induk dipanggil"); }
}

class Anak extends Induk {
    @Override
    void panggil() { System.out.println("Anak dipanggil"); }
}</code></pre>

                <h3>2. Overloading (Static Polymorphism)</h3>
                <p>Membuat beberapa method dengan nama yang sama dalam satu kelas, namun dengan jumlah atau tipe parameter yang berbeda.</p>

                <hr>

                <h3>Perbedaan Overload vs Override</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Karakteristik</th>
                            <th>Overload</th>
                            <th>Override</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Lokasi</strong></td>
                            <td>Dalam satu kelas yang sama.</td>
                            <td>Antara Superclass and Subclass.</td>
                        </tr>
                        <tr>
                            <td><strong>Nama Method</strong></td>
                            <td>Harus Sama.</td>
                            <td>Harus Sama.</td>
                        </tr>
                        <tr>
                            <td><strong>Parameter</strong></td>
                            <td>Harus Berbeda.</td>
                            <td>Harus Sama Persis.</td>
                        </tr>
                    </tbody>
                </table>

                <h3>Checklist Penguasaan</h3>
                <ul>
                    <li>Bisa membedakan Overloading (compile-time) dan Overriding (run-time).</li>
                    <li>Paham kapan harus menggunakan <code>@Override</code> annotation.</li>
                    <li>Bisa mengimplementasikan polimorfisme untuk meningkatkan fleksibilitas kode.</li>
                </ul>',
                'module_id' => '5',
            ],
            [
                'title'     => 'Abstraksi dan Interface: Kontrak Standarisasi',
                'cover_url' => 'materials/7b30791a2a7c4aa3defe0be90d1ce81d.png',
                'content'   => '<h2>Abstraksi: Penyembunyian Kerumitan</h2>
                <p>Abstraksi digunakan untuk menyembunyikan detail proses dan hanya menampilkan fungsi penting. Kelas abstrak tidak dapat diinstansiasi (dibuat objeknya secara langsung).</p>
                
                <pre><code>abstract class Hewan {
    String nama;
    abstract void bersuara(); // Method tanpa body (hanya prototype)
    
    void tidur() {
        System.out.println("Zzz...");
    }
}</code></pre>

                <h3>Video Tutorial: Abstraksi & Interface</h3>
                <iframe 
                    src="https://www.youtube.com/embed/fW77nI873cM" 
                    title="Tutorial Abstraksi Java" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen>
                </iframe>

                <h3>Interface: Kontrak Perilaku</h3>
                <p>Interface adalah "kontrak" yang harus dipenuhi oleh kelas yang mengimplementasikannya. Semua method di dalam interface secara implisit bersifat <code>public</code> dan <code>abstract</code>.</p>
                
                <pre><code>interface AlatTerbang {
    void terbang();
}

class Merpati extends Hewan implements AlatTerbang {
    void bersuara() { System.out.println("Cuit!"); }
    public void terbang() { System.out.println("Mengepakkan sayap"); }
}</code></pre>

                <hr>

                <h3>Checklist Penguasaan</h3>
                <ul>
                    <li>Bisa membuat <code>abstract class</code> untuk mendefinisikan kerangka umum.</li>
                    <li>Paham bahwa kelas turunan wajib meng-override method abstract.</li>
                    <li>Tahu cara menggunakan keyword <code>implements</code> untuk menerapkan interface.</li>
                    <li>Bisa membedakan kapan menggunakan Inheritance vs Implementation.</li>
                </ul>',
                'module_id' => '6',
            ],
            [
                'title'     => 'Relasi Antar Class (UML Dasar)',
                'cover_url' => 'materials/11a4db745936d3e71464f8bdb7f263f5.png',
                'content'   => '<h2>Memahami Class Diagram: Struktur dan Relasi</h2>
                <p>Class diagram atau diagram kelas adalah salah satu jenis diagram struktur pada UML yang menggambarkan dengan jelas struktur serta deskripsi class, atribut, metode, dan hubungan dari setiap objek.</p>

                <blockquote>
                    <strong>Sifat Statis:</strong> Diagram kelas bukan menjelaskan apa yang terjadi jika kelas-kelasnya berhubungan (dinamis), melainkan menjelaskan hubungan apa yang terjalin antar kelas tersebut secara struktural.
                </blockquote>

                <hr>

                <h3>Fungsi Class Diagram</h3>
                <ul>
                    <li>Menunjukan struktur dari suatu sistem dengan jelas.</li>
                    <li>Meningkatkan pemahaman tentang skema atau arsitektur program.</li>
                    <li>Memberikan gambaran mengenai relasi-relasi antar objek dalam perangkat lunak.</li>
                    <li>Digunakan sebagai blueprint untuk proses coding (khususnya paradigma OOP).</li>
                </ul>

                <h3>Komponen Penyusun Class Diagram</h3>
                <p>Setiap kotak class dalam diagram biasanya dibagi menjadi tiga bagian utama:</p>
                
                <img src="https://dicoding-assets.sgp1.cdn.digitaloceanspaces.com/blog/wp-content/uploads/2021/09/Contoh_komponen_class_diagrampng-1024x538.jpg" alt="Komponen Class Diagram" />

                <ol>
                    <li><strong>Bagian Atas:</strong> Berisi Nama Class (Simple Name).</li>
                    <li><strong>Bagian Tengah:</strong> Berisi Atribut (Properties) yang menjelaskan kualitas atau data dari class tersebut.</li>
                    <li><strong>Bagian Bawah:</strong> Berisi Operasi atau Metode yang menggambarkan bagaimana class berinteraksi dengan data.</li>
                </ol>

                <hr>

                <h3>Jenis Hubungan Antar Kelas</h3>
                <p>Ada tiga hubungan fundamental yang sering muncul dalam diagram kelas:</p>

                <ul>
                    <li>
                        <strong>Asosiasi (Association)</strong><br>
                        Hubungan statis antara dua class. Biasanya menunjukkan bahwa satu class memiliki atribut yang tipenya adalah class lain.
                        <img src="https://dicoding-assets.sgp1.cdn.digitaloceanspaces.com/blog/wp-content/uploads/2021/09/Contoh_Asosiasi-1024x538.jpg" alt="Contoh Asosiasi" />
                    </li>
                    <li>
                        <strong>Agregasi (Aggregation)</strong><br>
                        Hubungan "bagian dari" di mana dua class dapat berdiri sendiri-sendiri secara independen. Simbolnya adalah berlian kosong.
                        <img src="https://dicoding-assets.sgp1.cdn.digitaloceanspaces.com/blog/wp-content/uploads/2021/09/Contoh_agregasi-1024x538.jpg" alt="Contoh Agregasi" />
                    </li>
                    <li>
                        <strong>Pewarisan (Inheritance/Generalization)</strong><br>
                        Kemampuan subclass untuk mewarisi seluruh atribut dan metode dari superclass-nya. Simbolnya adalah panah dengan ujung segitiga kosong.
                        <img src="https://dicoding-assets.sgp1.cdn.digitaloceanspaces.com/blog/wp-content/uploads/2021/09/Contoh_pewarisan-1024x538.jpg" alt="Contoh Pewarisan" />
                    </li>
                </ul>

                <hr>

                <h3>Contoh Penerapan: Sistem Perpustakaan</h3>
                <p>Berikut adalah gambaran bagaimana berbagai class (Buku, Anggota, Peminjaman) saling terhubung dalam sebuah sistem nyata:</p>
                <img src="https://dicoding-assets.sgp1.cdn.digitaloceanspaces.com/blog/wp-content/uploads/2021/09/Contoh_class_diagram-1024x538.jpg" alt="Class Diagram Perpustakaan" />

                <h3>Checklist Penguasaan</h3>
                <ul>
                    <li>Bisa menyebutkan 3 komponen utama dalam sebuah kotak class.</li>
                    <li>Paham perbedaan visual antara panah Inheritance, Agregasi, dan Asosiasi.</li>
                    <li>Bisa menjelaskan kenapa class diagram bersifat statis.</li>
                </ul>',
                'module_id' => '7',
            ],
        ];

        foreach ($materials as $material) {
            Material::updateOrCreate(
                ['title' => $material['title']],
                [
                    'cover_url'  => $material['cover_url'] ?? null,
                    'content'    => $material['content'],
                    'module_id'  => $material['module_id'],
                    'created_by' => '01kqd08mx4rj8z6ergz63k7gfe',
                ],
            );
        }
    }
}
