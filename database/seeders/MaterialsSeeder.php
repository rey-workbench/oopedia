<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\User;
use Illuminate\Database\Seeder;

class MaterialsSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            [
                'title'   => 'Pengantar Konsep Dasar OOP',
                'content' => '<h2>Transformasi Paradigma: Dari Prosedural ke Objek</h2>
                <p>Pemrograman Berorientasi Objek (Object-Oriented Programming/OOP) bukan sekadar teknik menulis kode, melainkan sebuah cara pandang (paradigma) dalam memecahkan masalah kompleks. Dalam sistem tradisional (prosedural), fokus utama adalah pada urutan langkah-langkah atau instruksi. Namun, seiring bertambahnya kompleksitas perangkat lunak, paradigma prosedural seringkali menyebabkan "spaghetti code" yang sulit dipelihara.</p>
                <p>OOP hadir untuk mengatasi masalah tersebut dengan mengelompokkan data (atribut) dan perilaku (method) ke dalam satu kesatuan yang disebut <strong>Objek</strong>. Dengan memodelkan sistem sebagai kumpulan objek yang saling berinteraksi, kita dapat mencapai tingkat <em>modularity</em> yang tinggi. Hal ini memungkinkan pengembang untuk fokus pada bagian-bagian kecil sistem tanpa harus memahami seluruh isi kode secara mendalam.</p>
                <p>Penerapan OOP di industri modern menjadi standar karena mendukung prinsip-prinsip pengembangan yang tangguh, seperti <em>Code Reusability</em> (penggunaan kembali kode) dan <em>Extensibility</em> (kemudahan pengembangan fitur baru). Memahami dasar OOP adalah langkah krusial bagi setiap pengembang yang ingin membangun sistem skala besar yang efisien dan mudah dúvah.</p>',
                'module_id'  => '1',
                'created_by' => 2,
            ],
            [
                'title'   => 'Class dan Object',
                'content' => '<h2>Blueprint vs State: Memahami Entitas dan Instansiasi</h2>
                <p>Dalam dunia pengembangan perangkat lunak, seringkali kita perlu membuat banyak entitas yang memiliki jenis yang sama namun data yang berbeda. Di sinilah peran <strong>Class</strong> sebagai <em>blueprint</em> atau cetakan. Sebuah Class mendefinisikan struktur data dan kemampuan apa saja yang akan dimiliki oleh objek masa depan, namun Class itu sendiri tidak menyimpan data spesifik dan tidak menempati memori operasional secara aktif untuk data objek.</p>
                <p><strong>Object</strong> adalah wujud nyata atau <em>instance</em> dari sebuah Class. Ketika sebuah objek dibuat, sistem akan mengalokasikan ruang di memori (biasanya di area <em>Heap</em>) untuk menyimpan status atau data unik milik objek tersebut. Misalnya, jika Class adalah "MobilePhone", maka Object-nya adalah ponsel fisik yang Anda genggam, lengkap dengan nomor seri, warna, dan level baterai yang spesifik.</p>
                <p>Memahami siklus hidup objek (Object Lifecycle)—mulai dari pembuatan (instansiasi) hingga penghancuran (garbage collection)—adalah kunci untuk mengelola performa aplikasi. Penggunaan Class yang efisien memastikan bahwa aplikasi kita memiliki struktur yang rapi, sementara pemanfaatan Object yang tepat memungkinkan manajemen data yang dinamis dan terisolasi dengan baik.</p>',
                'module_id'  => '2',
                'created_by' => 2,
            ],
            [
                'title'   => 'Enkapsulasi (Encapsulation)',
                'content' => '<h2>Mekanisme Perlindungan Data dan Abstraksi Internal</h2>
                <p>Enkapsulasi sering disebut sebagai prinsip "Information Hiding". Inti dari konsep ini adalah membungkus data sensitif di dalam objek dan menutup akses langsung dari luar. Mengapa ini penting? Tanpa enkapsulasi, variabel internal sebuah objek bisa diubah secara ilegal oleh bagian kode lain, yang berpotensi menyebabkan bug yang sulit dilacak atau korupsi data.</p>
                <p>Melalui penggunaan <strong>Access Modifiers</strong> seperti <code>private</code>, <code>protected</code>, dan <code>public</code>, kita dapat mengontrol siapa saja yang boleh melihat atau memodifikasi atribut objek. Sebagai gantinya, kita menyediakan metode publik yang disebut <em>Getter</em> dan <em>Setter</em>. Metode ini bertindak sebagai "penjaga gerbang" yang dapat melakukan validasi data sebelum perubahan benar-benar disimpan ke dalam atribut internal.</p>
                <p>Dengan menerapkan enkapsulasi yang ketat, kita menciptakan sistem yang lebih aman dan terstruktur. Objek menjadi sebuah "Black Box" di mana pengguna hanya perlu tahu cara menggunakannya melalui antarmuka (interface) publik yang disediakan, tanpa perlu tahu kerumitan logika di dalamnya. Hal ini sangat mendukung prinsip <em>Loosely Coupled</em>, di mana perubahan internal di satu kelas tidak akan merusak kelas lain yang menggunakannya.</p>',
                'module_id'  => '3',
                'created_by' => 2,
            ],
            [
                'title'   => 'Relasi Class (Class Relations)',
                'content' => '<h2>Membangun Arsitektur Lewat Komposisi dan Asosiasi</h2>
                <p>Sistem perangkat lunak yang kompleks tidak mungkin dibangun hanya dengan satu kelas raksasa. Sebaliknya, aplikasi yang baik dibangun dari banyak kelas kecil yang fokus pada tugas tertentu dan saling bekerja sama. Relasi antar kelas mendefinisikan bagaimana satu entitas berhubungan, menggunakan, atau memiliki entitas lain untuk mencapai fungsionalitas yang lebih besar.</p>
                <p>Terdapat beberapa tingkatan relasi yang harus dipahami oleh developer profesional. <strong>Association</strong> adalah hubungan fungsional yang paling umum, di mana satu objek menggunakan jasa objek lain. Namun, ketika bicara tentang kepemilikan, kita mengenal <strong>Aggregation</strong> (hubungan "has-a" yang lemah, di mana bagian bisa hidup tanpa induk) dan <strong>Composition</strong> (hubungan "has-a" yang kuat, di mana bagian akan hancur jika induknya hancur).</p>
                <p>Memilih jenis relasi yang tepat sangat berpengaruh pada fleksibilitas kode. Desain modern sangat menyarankan prinsip <em>"Favor composition over inheritance"</em>. Dengan menggunakan komposisi, kita dapat merakit objek-objek kecil dengan cara yang lebih dinamis dan fleksibel saat runtime, dibandingkan dengan inheritance yang bersifat kaku dan terikat pada hierarki statis sejak waktu kompilasi.</p>',
                'module_id'  => '4',
                'created_by' => 2,
            ],
            [
                'title'   => 'Inheritance (Pewarisan)',
                'content' => '<h2>Hierarki dan Penggunaan Kembali Kode Secara Terstruktur</h2>
                <p>Inheritance atau pewarisan adalah mekanisme yang memungkinkan sebuah kelas (Subclass) untuk mewarisi sifat dan perilaku dari kelas lain (Superclass). Konsep ini sangat efektif untuk menghindari duplikasi kode (DRY - Don\'t Repeat Yourself). Dengan meletakkan logika umum di Superclass, semua kelas turunan secara otomatis akan memiliki kemampuan tersebut tanpa perlu menulis ulang kodenya.</p>
                <p>Pewarisan menciptakan hubungan logis <strong>"Is-A"</strong>. Misalnya, "Manager is a Karyawan". Namun, penggunaan inheritance harus dilakukan dengan hati-hati untuk menghindari "Deep Class Hierarchies" yang terlalu rumit. Jika hierarki terlalu dalam, perubahan kecil di kelas induk paling atas dapat menyebabkan efek domino yang merusak banyak kelas di bawahnya—masalah ini sering dikenal sebagai <em>Fragile Base Class</em>.</p>
                <p>Dalam pengembangan profesional, inheritance digunakan untuk menentukan kategori umum dan memungkinkan spesialisasi di kelas anak. Namun, developer yang bijak akan selalu memastikan bahwa relasi inheritance benar-benar mencerminkan hubungan identitas yang logis, bukan sekadar cara cepat untuk membagikan kode antar kelas yang tidak berhubungan.</p>',
                'module_id'  => '5',
                'created_by' => 2,
            ],
            [
                'title'   => 'Overriding dan Overloading',
                'content' => '<h2>Adaptasi Perilaku: Polimorfisme Statis dan Dinamis</h2>
                <p>Kemampuan untuk memberikan banyak bentuk pada satu nama metode adalah esensi dari pemrosesan yang fleksibel. <strong>Method Overloading</strong> (Polimorfisme Statis) memungkinkan satu kelas memiliki beberapa metode dengan nama yang sama, asalkan jumlah atau tipe parameternya berbeda. Hal ini memberikan kenyamanan bagi pengguna kode karena mereka tidak perlu mengingat banyak nama fungsi untuk tugas yang serupa.</p>
                <p>Di sisi lain, <strong>Method Overriding</strong> (Polimorfisme Dinamis) terjadi ketika kelas turunan memberikan implementasi spesifik untuk metode yang sudah ada di kelas induknya. Ini memungkinkan objek anak untuk menyesuaikan perilakunya dengan tetap mempertahankan antarmuka yang sama dengan induknya. Misalnya, setiap "Hewan" bisa "Bersuara", tetapi "Anjing" melakukan override untuk "Menggonggong" sementara "Kucing" melakukan override untuk "Mengeong".</p>
                <p>Memahami perbedaan antara kedua konsep ini sangat penting dalam perancangan API dan kerangka kerja (framework). Overloading memudahkan variasi input, sementara Overriding memungkinkan sistem kita untuk mendukung perilaku kustom yang baru tanpa harus mengubah logika pemanggil aslinya di tingkat abstraksi yang lebih tinggi.</p>',
                'module_id'  => '6',
                'created_by' => 2,
            ],
            [
                'title'   => 'Abstract Class',
                'content' => '<h2>Mendefinisikan Template dan Kontrak Implementasi</h2>
                <p>Abstract Class adalah "kelas setengah jadi" yang tidak dimaksudkan untuk diciptakan objeknya secara langsung. Fungsinya adalah sebagai cetakan dasar (template) yang menyediakan kerangka umum bagi kelas-kelas turunannya. Penggunaan keyword <code>abstract</code> pada level kelas memberikan indikasi kuat kepada pengembang lain bahwa kelas ini hanya ada sebagai abstraksi untuk dikembangkan lebih lanjut.</p>
                <p>Keunikan dari Abstract Class adalah kemampuannya menyimpan <strong>Abstract Method</strong>—metode tanpa isi yang WAJIB diimplementasikan oleh kelas turunan yang konkrit. Hal ini menjamin bahwa setiap anak pasti akan memiliki fungsionalitas tersebut, namun dengan cara mereka masing-masing. Di saat yang sama, Abstract Class juga boleh memiliki metode biasa dengan implementasi penuh untuk membagikan logika yang sama ke semua anak.</p>
                <p>Secara arsitektural, Abstract Class sangat berguna ketika kita ingin mendefinisikan identitas bersama. Misalnya, dalam sistem perbankan, kita mungkin memiliki Abstract Class "Account" yang mendefinisikan perilaku dasar seperti cek saldo, namun menyerahkannya pada "SavingsAccount" atau "CreditAccount" untuk menentukan bagaimana detail perhitungan bunga dilakukan.</p>',
                'module_id'  => '7',
                'created_by' => 2,
            ],
            [
                'title'   => 'Interface',
                'content' => '<h2>Standarisasi Kemampuan dan Pemutusan Ketergantungan</h2>
                <p>Jika Abstract Class adalah tentang "Apa Anda?" (Identitas), maka <strong>Interface</strong> adalah tentang "Apa yang Bisa Anda Lakukan?" (Perilaku/Kemampuan). Interface adalah bentuk abstraksi paling murni karena ia hanya berisi tanda tangan metode tanpa ada data atau implementasi sama sekali. Sebuah kelas yang mengimplementasikan Interface berarti kelas tersebut "berjanji" untuk menyediakan fungsionalitas tertentu sesuai kontrak yang disepakati.</p>
                <p>Interface adalah alat utama untuk mencapai <em>Decoupling</em> dalam arsitektur perangkat lunak. Dengan mendesain sistem berdasarkan Interface (<em>Programming to an Interface, not an Implementation</em>), kita dapat menukar satu modul dengan modul lain tanpa merusak sistem secara keseluruhan, asalkan keduanya mematuhi kontrak Interface yang sama.</p>
                <p>Selain itu, Interface memungkinkan satu kelas untuk memiliki banyak kemampuan sekaligus (Multiple Inheritance melalui Interface), sesuatu yang tidak bisa dilakukan dengan kelas biasa di banyak bahasa seperti Java. Hal ini memberikan fleksibilitas luar biasa dalam merakit objek yang kompleks dengan berbagai kemampuan yang saling lepas, menjadikannya fondasi utama dalam pola desain (Design Patterns) modern.</p>',
                'module_id'  => '8',
                'created_by' => 2,
            ],
            [
                'title'   => 'Polimorfisme (Polymorphism)',
                'content' => '<h2>Satu Antarmuka, Banyak Wujud: Level Tertinggi Abstraksi</h2>
                <p>Polimorfisme adalah puncak dari pilar-pilar OOP yang memungkinkan sebuah objek untuk mengambil banyak bentuk. Dalam praktik pemrograman, hal ini berarti sebuah variabel dengan tipe referensi kelas induk atau interface dapat menampung objek dari kelas turunan mana pun. Hal ini menciptakan kode yang sangat umum dan serbaguna, yang dapat menangani berbagai jenis data secara konsisten.</p>
                <p>Konsep yang sangat krusial di sini adalah <strong>Dynamic Binding</strong> atau Late Binding. Saat aplikasi berjalan, sistem secara otomatis akan menentukan metode mana yang harus dipanggil berdasarkan objek aslinya, bukan berdasarkan tipe variabelnya. Ini memungkinkan kita membuat fungsi yang memproses daftar "Shape" (bentuk) dan memanggil metode <code>draw()</code>, di mana sistem akan tahu sendiri kapan harus menggambar lingkaran, persegi, atau segitiga saat runtime.</p>
                <p>Polimorfisme membuat aplikasi kita menjadi sangat <em>Extensible</em>. Kita bisa menambahkan ribuan kelas baru ke dalam sistem di masa depan, dan selama kelas-kelas tersebut mengikuti hierarki atau interface yang sudah ada, kode lama kita akan otomatis bisa bekerja dengan kelas-kelas baru tersebut tanpa perlu diubah sedikit pun. Inilah rahasia di balik sistem yang elastis dan tahan lama.</p>',
                'module_id'  => '9',
                'created_by' => 2,
            ],
        ];

        $dosen = User::whereHas('role', function ($q) {
            $q->where('role_name', 'dosen');
        })->first();
        $dosenId = $dosen ? $dosen->id : null;

        foreach ($materials as $material) {
            $material['created_by'] = $dosenId;
            Material::updateOrCreate(
                ['title' => $material['title']],
                $material,
            );
        }
    }
}
