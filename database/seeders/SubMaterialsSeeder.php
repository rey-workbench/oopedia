<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubMaterial;
use App\Models\Material;

class SubMaterialsSeeder extends Seeder
{
    public function run(): void
    {
        $materialTopicMap = [
            1 => [ // Pengenalan PBO
                [
                    'title' => 'Konsep Paradigma PBO',
                    'jenis' => 'teori',
                    'style' => 'textual',
                    'content' => '<h3>Apa itu PBO?</h3>
                    <p>Pemrograman Berorientasi Objek (PBO) adalah sebuah paradigma pemrograman yang berorientasi kepada objek. Semua data dan fungsi di dalam paradigma ini dibungkus dalam kelas-kelas atau objek-objek. Setiap objek dapat menerima pesan, memproses data, dan mengirim pesan ke objek lainnya.</p>
                    <p>Berbeda dengan pemrograman prosedural yang membagi program menjadi unit-unit kecil fungis, PBO membagi program berdasarkan entitas (Objek) yang memiliki data dan perilaku sendiri.</p>
                    <h4>Analogi Nyata</h4>
                    <p>Bayangkan sebuah mobil. Mobil adalah sebuah objek. Mobil memiliki atribut (warna, merk, kecepatan) dan perilaku (maju, mundur, klakson). Dalam PBO, kita memodelkan sistem perangkat lunak seperti kita melihat benda-benda di dunia nyata.</p>'
                ],
                [
                    'title' => 'Sintaks Dasar Objek',
                    'jenis' => 'sintaks',
                    'style' => 'visual',
                    'content' => '<h3>Mendeklarasikan Kelas dan Objek</h3>
                    <p>Dalam bahasa seperti Java atau PHP, kita mendefinisikan struktur objek menggunakan kata kunci <code>class</code>.</p>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">// Definisi Kelas</div>
                        <div class="ql-code-block">class Manusia {</div>
                        <div class="ql-code-block">    public $nama;</div>
                        <div class="ql-code-block">    </div>
                        <div class="ql-code-block">    public function bicara() {</div>
                        <div class="ql-code-block">        return "Halo, nama saya " . $this->nama;</div>
                        <div class="ql-code-block">    }</div>
                        <div class="ql-code-block">}</div>
                        <div class="ql-code-block"></div>
                        <div class="ql-code-block">// Membuat Objek (Instansiasi)</div>
                        <div class="ql-code-block">$budi = new Manusia();</div>
                        <div class="ql-code-block">$budi->nama = "Budi";</div>
                        <div class="ql-code-block">echo $budi->bicara(); // Output: Halo, nama saya Budi</div>
                    </div>
                    <p>Kata kunci <code>new</code> digunakan untuk mengalokasikan memori bagi objek baru di sistem.</p>'
                ],
                [
                    'title' => 'Analisis Sistem PBO',
                    'jenis' => 'mixed',
                    'style' => 'mixed',
                    'content' => '<h3>Mengidentifikasi Objek</h3>
                    <p>Langkah pertama dalam PBO adalah analisis. Kita harus mampu membedakan mana yang merupakan <strong>Kelas</strong> dan mana yang merupakan <strong>Objek</strong>.</p>
                    <ul>
                        <li><strong>Kelas:</strong> Kategori umum (Contoh: Mahasiswa, Buku, Transaksi).</li>
                        <li><strong>Objek:</strong> Entitas spesifik (Contoh: Budi, Buku "Laskar Pelangi", Transaksi ID #102).</li>
                    </ul>
                    <p><strong>Latihan:</strong> Jika kita membangun sistem ATM, identifikasilah minimal 3 objek yang terlibat! (Nasabah, Kartu, Mesin ATM).</p>'
                ],
            ],
            2 => [ // Kelas dan Objek
                [
                    'title' => 'Teori Blueprint (Kelas)',
                    'jenis' => 'teori',
                    'style' => 'textual',
                    'content' => '<h3>Kelas sebagai Blueprint</h3>
                    <p>Kelas bukan merupakan objek itu sendiri, melainkan sketsa atau cetakan. Kelas menentukan data apa yang akan dimiliki oleh objek (Atribut) dan fungsi apa yang bisa dijalankan (Method).</p>
                    <p>Satu kelas dapat digunakan untuk menciptakan ribuan objek yang berbeda-beda nilainya namun memiliki struktur yang sama.</p>'
                ],
                [
                    'title' => 'Deklarasi Class & Instansiasi',
                    'jenis' => 'sintaks',
                    'style' => 'visual',
                    'content' => '<h3>Struktur Lengkap Kelas</h3>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">class Produk {</div>
                        <div class="ql-code-block">    // Atribut (Properti)</div>
                        <div class="ql-code-block">    public $id;</div>
                        <div class="ql-code-block">    public $harga;</div>
                        <div class="ql-code-block"></div>
                        <div class="ql-code-block">    // Constructor (Dijalankan saat objek dibuat)</div>
                        <div class="ql-code-block">    public function __construct($id, $harga) {</div>
                        <div class="ql-code-block">        $this->id = $id;</div>
                        <div class="ql-code-block">        $this->harga = $harga;</div>
                        <div class="ql-code-block">    }</div>
                        <div class="ql-code-block"></div>
                        <div class="ql-code-block">    // Method</div>
                        <div class="ql-code-block">    public function cekHarga() {</div>
                        <div class="ql-code-block">        return "Harga produk ini adalah: " . $this->harga;</div>
                        <div class="ql-code-block">    }</div>
                        <div class="ql-code-block">}</div>
                        <div class="ql-code-block"></div>
                        <div class="ql-code-block">// Instansiasi dengan parameter</div>
                        <div class="ql-code-block">$laptop = new Produk("LP01", 15000000);</div>
                    </div>'
                ],
                [
                    'title' => 'Latihan Pembuatan Objek',
                    'jenis' => 'mixed',
                    'style' => 'mixed',
                    'content' => '<h3>Membuat Banyak Instance</h3>
                    <p>Mari kita lihat bagaimana satu kelas <code>Mobil</code> bisa menjadi banyak objek berbeda:</p>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">$avanza = new Mobil("Putih", "Toyota");</div>
                        <div class="ql-code-block">$civic = new Mobil("Hitam", "Honda");</div>
                        <div class="ql-code-block">$ferrari = new Mobil("Merah", "Ferrari");</div>
                    </div>
                    <p>Ketiganya berasal dari Blueprint yang sama, namun masing-masing berdiri sendiri di memori komputer dengan data yang berbeda.</p>'
                ],
            ],
            3 => [ // Atribut dan Method
                [
                    'title' => 'State vs Behavior',
                    'jenis' => 'teori',
                    'style' => 'textual',
                    'content' => '<h3>Memahami Karakteristik Objek</h3>
                    <p>Objek memiliki dua hal utama:</p>
                    <ol>
                        <li><strong>State (Atribut):</strong> Apa yang dimiliki atau diketahui objek. Contoh: Saldo bank, Judul buku.</li>
                        <li><strong>Behavior (Method):</strong> Apa yang bisa dilakukan objek. Contoh: Tarik tunai, Baca buku.</li>
                    </ol>
                    <p>State biasanya berupa variabel, sedangkan Behavior berupa fungsi/prosedur.</p>'
                ],
                [
                    'title' => 'Definisi Field & Fungsi',
                    'jenis' => 'sintaks',
                    'style' => 'visual',
                    'content' => '<h3>Menulis State dan Behavior</h3>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">class Player {</div>
                        <div class="ql-code-block">    // State</div>
                        <div class="ql-code-block">    public $health = 100;</div>
                        <div class="ql-code-block">    public $level = 1;</div>
                        <div class="ql-code-block"></div>
                        <div class="ql-code-block">    // Behavior</div>
                        <div class="ql-code-block">    public function takeDamage($amount) {</div>
                        <div class="ql-code-block">        $this->health -= $amount;</div>
                        <div class="ql-code-block">        if($this->health < 0) $this->health = 0;</div>
                        <div class="ql-code-block">    }</div>
                        <div class="ql-code-block">}</div>
                    </div>
                    <p>Method seringkali mengubah nilai dari Atribut (State) objek itu sendiri menggunakan keyword <code>this</code>.</p>'
                ],
                [
                    'title' => 'Interaksi Antar Objek',
                    'jenis' => 'mixed',
                    'style' => 'mixed',
                    'content' => '<h3>Objek Berbicara dengan Objek Lain</h3>
                    <p>Dalam aplikasi nyata, objek jarang bekerja sendirian. Mereka saling memanggil method satu sama lain.</p>
                    <p>Contoh: Objek <code>Penjual</code> memanggil method <code>terimaPesanan()</code> milik objek <code>Kurir</code> untuk mengirimkan barang.</p>'
                ],
            ],
            4 => [ // Enkapsulasi
                [
                    'title' => 'Prinsip Information Hiding',
                    'jenis' => 'teori',
                    'style' => 'textual',
                    'content' => '<h3>Melindungi Integritas Data</h3>
                    <p>Enkapsulasi memastikan bahwa data sensitif di dalam objek tidak bisa diubah secara sembarangan dari luar. Ini disebut <em>Information Hiding</em>.</p>
                    <p>Tanpa enkapsulasi, siapapun bisa mengubah saldo bank seseorang menjadi 1 Milyar tanpa melalui proses validasi yang benar.</p>'
                ],
                [
                    'title' => 'Getter, Setter & Modifier',
                    'jenis' => 'sintaks',
                    'style' => 'visual',
                    'content' => '<h3>Access Modifiers</h3>
                    <ul>
                        <li><strong>Private:</strong> Hanya bisa diakses di dalam kelas itu sendiri.</li>
                        <li><strong>Public:</strong> Bisa diakses dari mana saja.</li>
                        <li><strong>Protected:</strong> Diakses oleh kelas sendiri dan turunannya.</li>
                    </ul>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">class AkunBank {</div>
                        <div class="ql-code-block">    private $saldo; // Tidak bisa diakses langsung via $obj->saldo</div>
                        <div class="ql-code-block"></div>
                        <div class="ql-code-block">    public function setSaldo($jumlah) {</div>
                        <div class="ql-code-block">        if($jumlah > 0) $this->saldo = $jumlah;</div>
                        <div class="ql-code-block">    }</div>
                        <div class="ql-code-block"></div>
                        <div class="ql-code-block">    public function getSaldo() {</div>
                        <div class="ql-code-block">        return "Rp " . number_format($this->saldo);</div>
                        <div class="ql-code-block">    }</div>
                        <div class="ql-code-block">}</div>
                    </div>'
                ],
                [
                    'title' => 'Keamanan Data Objek',
                    'jenis' => 'mixed',
                    'style' => 'mixed',
                    'content' => '<h3>Studi Kasus: Validasi Umur</h3>
                    <p>Dengan enkapsulasi, kita bisa mencegah data yang tidak logis masuk ke sistem. Contoh: mencegah input umur negatif.</p>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">public function setUmur($u) {</div>
                        <div class="ql-code-block">    if($u < 0) throw new Exception("Umur tidak boleh negatif!");</div>
                        <div class="ql-code-block">    $this->umur = $u;</div>
                        <div class="ql-code-block">}</div>
                    </div>'
                ],
            ],
            5 => [ // Pewarisan
                [
                    'title' => 'Hierarki Kelas (Parent/Child)',
                    'jenis' => 'teori',
                    'style' => 'textual',
                    'content' => '<h3>Konsep Reusability</h3>
                    <p>Pewarisan (Inheritance) memungkinkan kita untuk membuat kelas baru berdasarkan kelas yang sudah ada. Kelas yang mewarisi disebut <strong>Child Class</strong>, dan yang diwarisi disebut <strong>Parent Class</strong>.</p>
                    <p>Keuntungan utamanya adalah kita tidak perlu menulis ulang kode yang sama untuk kelas-kelas yang memiliki sifat serupa.</p>'
                ],
                [
                    'title' => 'Keyword Extends & Super',
                    'jenis' => 'sintaks',
                    'style' => 'visual',
                    'content' => '<h3>Menggunakan Inheritance</h3>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">// Parent Class</div>
                        <div class="ql-code-block">class Kendaraan {</div>
                        <div class="ql-code-block">    public $merk;</div>
                        <div class="ql-code-block">    public function klakson() { return "Beep!"; }</div>
                        <div class="ql-code-block">}</div>
                        <div class="ql-code-block"></div>
                        <div class="ql-code-block">// Child Class</div>
                        <div class="ql-code-block">class Motor extends Kendaraan {</div>
                        <div class="ql-code-block">    public $cc;</div>
                        <div class="ql-code-block">}</div>
                        <div class="ql-code-block"></div>
                        <div class="ql-code-block">$mio = new Motor();</div>
                        <div class="ql-code-block">echo $mio->klakson(); // Hasil: Beep! (Warisan dari Kendaraan)</div>
                    </div>'
                ],
                [
                    'title' => 'Reusability dengan Inheritance',
                    'jenis' => 'mixed',
                    'style' => 'mixed',
                    'content' => '<h3>Membangun Struktur Organisasi</h3>
                    <p>Dalam sistem kampus, kita bisa punya Parent Class <code>CivitasAkademika</code>. Lalu Child Class-nya adalah <code>Dosen</code> dan <code>Mahasiswa</code>. Keduanya mewarisi atribut <code>nama</code> dan <code>NIM/NIDN</code> dari parent, tapi punya perilaku unik masing-masing.</p>'
                ],
            ],
            6 => [ // Polimorfisme
                [
                    'title' => 'Konsep Banyak Bentuk',
                    'jenis' => 'teori',
                    'style' => 'textual',
                    'content' => '<h3>Satu Antarmuka, Banyak Aksi</h3>
                    <p>Polimorfisme berasal dari bahasa Yunani yang berarti "banyak bentuk". Dalam PBO, ini berarti satu nama method bisa memiliki cara kerja yang berbeda-beda tergantung objek yang memanggilnya.</p>'
                ],
                [
                    'title' => 'Override vs Overload',
                    'jenis' => 'sintaks',
                    'style' => 'visual',
                    'content' => '<h3>Method Overriding</h3>
                    <p>Terjadi ketika Child Class mendefinisikan ulang method yang sudah ada di Parent Class.</p>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">class Hewan {</div>
                        <div class="ql-code-block">    public function suara() { return "..."; }</div>
                        <div class="ql-code-block">}</div>
                        <div class="ql-code-block"></div>
                        <div class="ql-code-block">class Anjing extends Hewan {</div>
                        <div class="ql-code-block">    public function suara() { return "Guk Guk!"; }</div>
                        <div class="ql-code-block">}</div>
                        <div class="ql-code-block"></div>
                        <div class="ql-code-block">class Kucing extends Hewan {</div>
                        <div class="ql-code-block">    public function suara() { return "Meong!"; }</div>
                        <div class="ql-code-block">}</div>
                    </div>'
                ],
                [
                    'title' => 'Dynamic Dispatching',
                    'jenis' => 'mixed',
                    'style' => 'mixed',
                    'content' => '<h3>Fleksibilitas Runtime</h3>
                    <p>Dengan polimorfisme, kita bisa memproses sekumpulan objek yang berbeda dalam satu loop yang sama:</p>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">$hewan_peliharaan = [$dog, $cat, $bird];</div>
                        <div class="ql-code-block">foreach($hewan_peliharaan as $h) {</div>
                        <div class="ql-code-block">    echo $h->suara(); // Menghasilkan suara yang berbeda sesuai jenisnya</div>
                        <div class="ql-code-block">}</div>
                    </div>'
                ],
            ],
            7 => [ // Abstraksi
                [
                    'title' => 'Penyederhanaan Sistem',
                    'jenis' => 'teori',
                    'style' => 'textual',
                    'content' => '<h3>Fokus pada Esensi</h3>
                    <p>Abstraksi adalah proses menyembunyikan detail implementasi yang rumit dan hanya memperlihatkan fitur penting kepada pengguna. Tujuannya adalah mengurangi kompleksitas.</p>
                    <p>Contoh: Saat menyetir mobil, Anda hanya perlu tahu cara menginjak pedal gas, bukan bagaimana cara bensin terbakar di ruang mesin.</p>'
                ],
                [
                    'title' => 'Abstract Class & Interface',
                    'jenis' => 'sintaks',
                    'style' => 'visual',
                    'content' => '<h3>Membuat Kontrak dengan Interface</h3>
                    <div class="ql-code-block-container" spellcheck="false">
                        <div class="ql-code-block">interface Pembayaran {</div>
                        <div class="ql-code-block">    public function bayar($jumlah);</div>
                        <div class="ql-code-block">}</div>
                        <div class="ql-code-block"></div>
                        <div class="ql-code-block">class DompetDigital implements Pembayaran {</div>
                        <div class="ql-code-block">    public function bayar($jumlah) {</div>
                        <div class="ql-code-block">        // Logika bayar lewat API dompet digital</div>
                        <div class="ql-code-block">    }</div>
                        <div class="ql-code-block">}</div>
                        <div class="ql-code-block"></div>
                        <div class="ql-code-block">class TransferBank implements Pembayaran {</div>
                        <div class="ql-code-block">    public function bayar($jumlah) {</div>
                        <div class="ql-code-block">        // Logika bayar lewat transfer</div>
                        <div class="ql-code-block">    }</div>
                        <div class="ql-code-block">}</div>
                    </div>'
                ],
                [
                    'title' => 'Desain Arsitektur OOP',
                    'jenis' => 'mixed',
                    'style' => 'mixed',
                    'content' => '<h3>Best Practice Abstraksi</h3>
                    <p>Selalulah membuat program yang bergantung pada <strong>Abstraksi</strong> (Interface), bukan pada <strong>Implementasi</strong> (Class konkret). Ini akan membuat kode Anda sangat mudah diganti-ganti teknologinya di masa depan tanpa merusak sistem utama.</p>'
                ],
            ],
        ];

        foreach ($materialTopicMap as $moduleId => $subTopics) {
            $material = Material::where('module_id', $moduleId)->first();
            
            if (!$material) continue;

            foreach ($subTopics as $index => $topic) {
                SubMaterial::updateOrCreate(
                    [
                        'material_id' => $material->id,
                        'title' => $topic['title']
                    ],
                    [
                        'content' => $topic['content'],
                        'jenis_konten' => $topic['jenis'],
                        'learning_style' => $topic['style'],
                        'order' => $index + 1,
                    ]
                );
            }
        }
    }
}
