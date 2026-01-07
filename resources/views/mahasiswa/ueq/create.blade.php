<x-layouts.app title="UEQ Survey" theme="mahasiswa">
    <x-slot:styles>

        <link href="{{ asset('css/mahasiswa/ueq/create.css') }}" rel="stylesheet">
    </x-slot:styles>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <x-ui.card>
                    <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-0">
                        <h4 class="mb-0">User Experience Questionnaire (UEQ)</h4>
                    </div>
                    <div class="card-body p-4">
                        @if ($errors->any() || session('error'))
                            <x-ui.alert variant="danger" dismissible="true">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Perhatian!</h5>
                                        <p class="mb-0">
                                            Ada {{ count(session('missingFields', [])) ?: $errors->count() }} pertanyaan yang belum dijawab. Silakan isi semua pertanyaan.
                                        </p>
                                    </div>
                                </div>
                            </x-ui.alert>
                        @endif
    
                        <p class="mb-4">Silakan berikan penilaian Anda terhadap aplikasi pembelajaran OOPEDIA dengan memilih nilai pada skala berikut:</p>
                        
                        <form id="ueqForm" method="POST" action="{{ route('mahasiswa.ueq.store') }}">
                            @csrf
                            
                            <!-- Tambahkan bagian form identitas mahasiswa -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="nim" class="form-label">NIM <span class="text-danger fw-bold">*</span></label>
                                        <input type="text" class="form-control @error('nim') is-invalid @enderror" 
                                            id="nim" name="nim" value="{{ old('nim') }}" required>
                                        @error('nim')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger fw-bold">*</span></label>
                                        <input type="text" class="form-control bg-light"
                                            id="name" value="{{ auth()->check() ? auth()->user()->name : '' }}" readonly
                                            style="cursor: not-allowed; opacity: 0.7;">
                                        <small class="text-muted">Nama diambil dari data profil</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="class" class="form-label">Kelas <span class="text-danger fw-bold">*</span></label>
                                        <input type="text" class="form-control @error('class') is-invalid @enderror" 
                                            id="class" name="class" value="{{ old('class') }}" 
                                            placeholder="contoh: SIB2A" required>
                                        @error('class')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <!-- Header table tidak berubah -->
                                    <thead>
                                        <tr>
                                            <th width="30%">Aspek</th>
                                            <th colspan="7" class="text-center">Penilaian <span class="text-danger fw-bold">*</span></th>
                                            <th width="30%">Aspek</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Untuk masing-masing baris pertanyaan, tambahkan logika untuk memarkah pertanyaan yang belum dijawab -->
                                        @foreach([
                                            ['name' => 'annoying_enjoyable', 'left' => 'Menyebalkan', 'right' => 'Menyenangkan'],
                                            ['name' => 'not_understandable_understandable', 'left' => 'Tidak dapat dipahami', 'right' => 'Dapat dipahami'],
                                            ['name' => 'creative_dull', 'left' => 'Kreatif', 'right' => 'Monoton'],
                                            ['name' => 'easy_difficult', 'left' => 'Mudah', 'right' => 'Sulit'],
                                            ['name' => 'valuable_inferior', 'left' => 'Bermanfaat', 'right' => 'Kurang bermanfaat'],
                                            ['name' => 'boring_exciting', 'left' => 'Membosankan', 'right' => 'Menarik'],
                                            ['name' => 'not_interesting_interesting', 'left' => 'Tidak menarik', 'right' => 'Menarik'],
                                            ['name' => 'unpredictable_predictable', 'left' => 'Tidak dapat diprediksi', 'right' => 'Dapat diprediksi'],
                                            ['name' => 'fast_slow', 'left' => 'Cepat', 'right' => 'Lambat'],
                                            ['name' => 'inventive_conventional', 'left' => 'Inovatif', 'right' => 'Konvensional'],
                                            ['name' => 'obstructive_supportive', 'left' => 'Menghambat', 'right' => 'Mendukung'],
                                            ['name' => 'good_bad', 'left' => 'Baik', 'right' => 'Buruk'],
                                            ['name' => 'complicated_easy', 'left' => 'Rumit', 'right' => 'Sederhana'],
                                            ['name' => 'unlikable_pleasing', 'left' => 'Tidak disukai', 'right' => 'Menyenangkan'],
                                            ['name' => 'usual_leading_edge', 'left' => 'Biasa saja', 'right' => 'Terdepan'],
                                            ['name' => 'unpleasant_pleasant', 'left' => 'Tidak menyenangkan', 'right' => 'Menyenangkan'],
                                            ['name' => 'secure_not_secure', 'left' => 'Aman', 'right' => 'Tidak aman'],
                                            ['name' => 'motivating_demotivating', 'left' => 'Memotivasi', 'right' => 'Tidak memotivasi'],
                                            ['name' => 'meets_expectations_does_not_meet', 'left' => 'Memenuhi ekspektasi', 'right' => 'Tidak memenuhi ekspektasi'],
                                            ['name' => 'inefficient_efficient', 'left' => 'Tidak efisien', 'right' => 'Efisien'],
                                            ['name' => 'clear_confusing', 'left' => 'Jelas', 'right' => 'Membingungkan'],
                                            ['name' => 'impractical_practical', 'left' => 'Tidak praktis', 'right' => 'Praktis'],
                                            ['name' => 'organized_cluttered', 'left' => 'Terorganisir', 'right' => 'Berantakan'],
                                            ['name' => 'attractive_unattractive', 'left' => 'Menarik', 'right' => 'Tidak menarik'],
                                            ['name' => 'friendly_unfriendly', 'left' => 'Ramah', 'right' => 'Tidak ramah'],
                                            ['name' => 'conservative_innovative', 'left' => 'Konservatif', 'right' => 'Inovatif'],
                                        ] as $question)
                                        <tr class="ueq-row {{ in_array($question['name'], session('missingFields', [])) || $errors->has($question['name']) ? 'unanswered' : '' }}">
                                            <td class="aspect-left">{{ $question['left'] }}</td>
                                            @for ($i = 1; $i <= 7; $i++)
                                                <td class="text-center radio-cell">
                                                    <div class="radio-wrapper">
                                                        <input type="radio" 
                                                            name="{{ $question['name'] }}" 
                                                            value="{{ $i }}" 
                                                            {{ old($question['name']) == $i ? 'checked' : '' }} 
                                                            required>
                                                        <label>{{ $i }}</label>
                                                    </div>
                                                </td>
                                            @endfor
                                            <td class="aspect-right">{{ $question['right'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
    
                            <!-- Bagian komentar dan saran -->
                            <div class="mb-3 mt-4">
                                <label for="comments" class="form-label">Komentar <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('comments') is-invalid @enderror" 
                                    id="comments" 
                                    name="comments" 
                                    rows="3" 
                                    required
                                    placeholder="Tulis komentar Anda mengenai pengalaman menggunakan web ini...">{{ old('comments') }}</textarea>
                                @error('comments')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="suggestions" class="form-label">Saran <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('suggestions') is-invalid @enderror" 
                                    id="suggestions" 
                                    name="suggestions" 
                                    rows="3" 
                                    required
                                    placeholder="Tulis saran Anda untuk pengembangan atau perbaikan web ini...">{{ old('suggestions') }}</textarea>
                                @error('suggestions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="d-grid gap-2 mt-4">
                                <x-ui.button type="submit" variant="primary" id="submitButton" class="w-100">
                                    Kirim
                                </x-ui.button>
                            </div>
                        </form>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>

    <x-slot:scripts>
        <script src="{{ asset('js/mahasiswa/ueq/create.js') }}"></script>
        <script>
            // Handle error scrolling
            document.addEventListener('DOMContentLoaded', function() {
                @if($errors->any() || session('missingFields'))
                    setTimeout(function() {
                        const firstUnanswered = document.querySelector('.unanswered');
                        if (firstUnanswered) {
                            firstUnanswered.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstUnanswered.classList.add('flash-highlight');
                            setTimeout(() => {
                                firstUnanswered.classList.remove('flash-highlight');
                            }, 2000);
                        }
                    }, 500);
                @endif
            });
        </script>
    </x-slot:scripts>
</x-layouts.app>