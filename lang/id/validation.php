<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Baris Bahasa Validasi
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut berisi pesan kesalahan default yang digunakan oleh
    | kelas validator. Beberapa aturan memiliki banyak versi seperti aturan ukuran.
    | Jangan ragu untuk mengubah setiap pesan ini di sini.
    |
    */

    'accepted'             => ':attribute harus disetujui.',
    'accepted_if'          => ':attribute harus disetujui ketika :other adalah :value.',
    'active_url'           => ':attribute bukan URL yang valid.',
    'after'                => ':attribute harus berisi tanggal setelah :date.',
    'after_or_equal'       => ':attribute harus berisi tanggal setelah atau sama dengan :date.',
    'alpha'                => ':attribute hanya boleh berisi huruf.',
    'alpha_dash'           => ':attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'alpha_num'            => ':attribute hanya boleh berisi huruf dan angka.',
    'array'                => ':attribute harus berupa sebuah array.',
    'ascii'                => ':attribute hanya boleh berisi karakter alfanumerik dan simbol single-byte.',
    'before'               => ':attribute harus berisi tanggal sebelum :date.',
    'before_or_equal'      => ':attribute harus berisi tanggal sebelum atau sama dengan :date.',
    'between'              => [
        'array'   => ':attribute harus memiliki antara :min dan :max item.',
        'file'    => ':attribute harus berukuran antara :min dan :max kilobita.',
        'numeric' => ':attribute harus bernilai antara :min dan :max.',
        'string'  => ':attribute harus berisi antara :min dan :max karakter.',
    ],
    'boolean'              => ':attribute harus bernilai true atau false.',
    'can'                  => ':attribute berisi nilai yang tidak diizinkan.',
    'confirmed'            => 'Konfirmasi :attribute tidak cocok.',
    'contains'             => ':attribute tidak memiliki nilai yang dibutuhkan.',
    'current_password'     => 'Kata sandi salah.',
    'date'                 => ':attribute bukan tanggal yang valid.',
    'date_equals'          => ':attribute harus berisi tanggal yang sama dengan :date.',
    'date_format'          => ':attribute tidak cocok dengan format :format.',
    'decimal'              => ':attribute harus memiliki :decimal tempat desimal.',
    'declined'             => ':attribute ini harus ditolak.',
    'declined_if'          => ':attribute ini harus ditolak ketika :other bernilai :value.',
    'different'            => ':attribute dan :other harus berbeda.',
    'digits'               => ':attribute harus terdiri dari :digits angka.',
    'digits_between'       => ':attribute harus terdiri dari :min hingga :max angka.',
    'dimensions'           => ':attribute tidak memiliki dimensi gambar yang valid.',
    'distinct'             => ':attribute memiliki nilai yang duplikat.',
    'doesnt_contain'       => ':attribute tidak boleh berisi salah satu dari berikut ini: :values.',
    'doesnt_end_with'      => ':attribute tidak boleh diakhiri dengan salah satu dari berikut ini: :values.',
    'doesnt_start_with'    => ':attribute tidak boleh dimulai dengan salah satu dari berikut ini: :values.',
    'email'                => ':attribute harus berupa alamat email yang valid.',
    'encoding'             => ':attribute harus di-encode dengan :encoding.',
    'ends_with'            => ':attribute harus diakhiri salah satu dari berikut: :values.',
    'enum'                 => ':attribute yang dipilih tidak valid.',
    'exists'               => ':attribute yang dipilih tidak valid.',
    'extensions'           => ':attribute harus memiliki salah satu ekstensi berikut: :values.',
    'file'                 => ':attribute harus berupa sebuah berkas.',
    'filled'               => ':attribute harus memiliki nilai.',
    'gt'                   => [
        'array'   => ':attribute harus memiliki lebih dari :value item.',
        'file'    => ':attribute harus berukuran lebih besar dari :value kilobita.',
        'numeric' => ':attribute harus bernilai lebih besar dari :value.',
        'string'  => ':attribute harus berisi lebih besar dari :value karakter.',
    ],
    'gte'                  => [
        'array'   => ':attribute harus terdiri dari :value item atau lebih.',
        'file'    => ':attribute harus berukuran lebih besar dari atau sama dengan :value kilobita.',
        'numeric' => ':attribute harus bernilai lebih besar dari atau sama dengan :value.',
        'string'  => ':attribute harus berisi lebih besar dari atau sama dengan :value karakter.',
    ],
    'hex_color'            => ':attribute harus berupa warna heksadesimal yang valid.',
    'image'                => ':attribute harus berupa gambar.',
    'in'                   => ':attribute yang dipilih tidak valid.',
    'in_array'             => ':attribute tidak ada di dalam :other.',
    'in_array_keys'        => ':attribute harus berisi setidaknya satu dari kunci berikut: :values.',
    'integer'              => ':attribute harus berupa bilangan bulat (angka).',
    'ip'                   => ':attribute harus berupa alamat IP yang valid.',
    'ipv4'                 => ':attribute harus berupa alamat IPv4 yang valid.',
    'ipv6'                 => ':attribute harus berupa alamat IPv6 yang valid.',
    'json'                 => ':attribute harus berupa string JSON yang valid.',
    'list'                 => ':attribute harus berupa sebuah list.',
    'lowercase'            => ':attribute harus berupa huruf kecil.',
    'lt'                   => [
        'array'   => ':attribute harus memiliki kurang dari :value item.',
        'file'    => ':attribute harus berukuran kurang dari :value kilobita.',
        'numeric' => ':attribute harus bernilai kurang dari :value.',
        'string'  => ':attribute harus berisi kurang dari :value karakter.',
    ],
    'lte'                  => [
        'array'   => ':attribute harus tidak lebih dari :value item.',
        'file'    => ':attribute harus berukuran kurang dari atau sama dengan :value kilobita.',
        'numeric' => ':attribute harus bernilai kurang dari atau sama dengan :value.',
        'string'  => ':attribute harus berisi kurang dari atau sama dengan :value karakter.',
    ],
    'mac_address'          => ':attribute harus berupa alamat MAC yang valid.',
    'max'                  => [
        'array'   => ':attribute maksimal terdiri dari :max item.',
        'file'    => ':attribute maksimal berukuran :max kilobita.',
        'numeric' => ':attribute maksimal bernilai :max.',
        'string'  => ':attribute tidak boleh lebih dari :max karakter.',
    ],
    'max_digits'           => ':attribute tidak boleh memiliki lebih dari :max digit.',
    'mimes'                => ':attribute harus berformat: :values.',
    'mimetypes'            => ':attribute harus berformat: :values.',
    'min'                  => [
        'array'   => ':attribute minimal terdiri dari :min item.',
        'file'    => ':attribute minimal berukuran :min kilobita.',
        'numeric' => ':attribute minimal bernilai :min.',
        'string'  => ':attribute harus minimal :min karakter.',
    ],
    'min_digits'           => ':attribute tidak boleh memiliki kurang dari :min digit.',
    'missing'              => ':attribute harus hilang.',
    'missing_if'           => ':attribute harus hilang ketika :other adalah :value.',
    'missing_unless'       => ':attribute harus hilang kecuali :other ada di :values.',
    'missing_with'         => ':attribute harus hilang ketika :values ada.',
    'missing_with_all'     => ':attribute harus hilang ketika semua :values ada.',
    'multiple_of'          => ':attribute harus merupakan kelipatan dari :value.',
    'not_in'               => ':attribute yang dipilih tidak valid.',
    'not_regex'            => 'Format :attribute tidak valid.',
    'numeric'              => ':attribute harus berupa angka.',
    'password'             => [
        'letters'       => ':attribute harus berisi setidaknya satu huruf.',
        'mixed'         => ':attribute harus berisi setidaknya satu huruf besar dan satu huruf kecil.',
        'numbers'       => ':attribute harus berisi setidaknya satu angka.',
        'symbols'       => ':attribute harus berisi setidaknya satu simbol.',
        'uncompromised' => ':attribute telah muncul dalam kebocoran data. Silakan pilih :attribute yang berbeda.',
    ],
    'present'              => ':attribute wajib ada.',
    'present_if'           => ':attribute wajib ada ketika :other bernilai :value.',
    'present_unless'       => ':attribute wajib ada kecuali :other bernilai :value.',
    'present_with'         => ':attribute wajib ada ketika :values ada.',
    'present_with_all'     => ':attribute wajib ada ketika semua :values ada.',
    'prohibited'           => ':attribute tidak boleh ada.',
    'prohibited_if'        => ':attribute tidak boleh ada bila :other adalah :value.',
    'prohibited_if_accepted' => ':attribute tidak boleh ada bila :other disetujui.',
    'prohibited_if_declined' => ':attribute tidak boleh ada bila :other ditolak.',
    'prohibited_unless'    => ':attribute tidak boleh ada kecuali :other memiliki nilai :values.',
    'prohibits'            => ':attribute melarang isian :other untuk ditampilkan.',
    'regex'                => 'Format :attribute tidak valid.',
    'required'             => ':attribute wajib diisi.',
    'required_array_keys'  => ':attribute wajib berisi entri untuk: :values.',
    'required_if'          => ':attribute wajib diisi bila :other adalah :value.',
    'required_if_accepted' => ':attribute wajib diisi bila :other disetujui.',
    'required_if_declined' => ':attribute wajib diisi bila :other ditolak.',
    'required_unless'      => ':attribute wajib diisi kecuali :other memiliki nilai :values.',
    'required_with'        => ':attribute wajib diisi bila terdapat :values.',
    'required_with_all'    => ':attribute wajib diisi bila terdapat :values.',
    'required_without'     => ':attribute wajib diisi bila tidak terdapat :values.',
    'required_without_all' => ':attribute wajib diisi bila sama sekali tidak terdapat :values.',
    'same'                 => ':attribute dan :other harus sama.',
    'size'                 => [
        'array'   => ':attribute harus mengandung :size item.',
        'file'    => ':attribute harus berukuran :size kilobita.',
        'numeric' => ':attribute harus berukuran :size.',
        'string'  => ':attribute harus berukuran :size karakter.',
    ],
    'starts_with'          => ':attribute harus diawali salah satu dari berikut: :values.',
    'string'               => ':attribute harus berupa teks.',
    'timezone'             => ':attribute harus berisi zona waktu yang valid.',
    'unique'               => ':attribute sudah digunakan.',
    'uploaded'             => ':attribute gagal diunggah.',
    'uppercase'            => ':attribute harus berupa huruf kapital.',
    'url'                  => 'Format :attribute tidak valid.',
    'ulid'                 => ':attribute harus merupakan ULID yang valid.',
    'uuid'                 => ':attribute harus merupakan UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'indisposable' => ':attribute tidak boleh berupa email sekali pakai.',

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'email' => 'alamat email',
        'password' => 'kata sandi',
        'name' => 'nama',
        'title' => 'judul',
        'content' => 'konten',
        'question_text' => 'teks pertanyaan',
        'answers' => 'jawaban'
    ],

];
