<x-layouts.app title="UEQ Survey" theme="mahasiswa">
    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header Section --}}
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl mb-6 shadow-inner">
                    <i class="fas fa-poll-h text-2xl"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-4 tracking-widest  uppercase">
                    User Experience <span class="text-blue-600">Questionnaire</span>
                </h1>
                <p class="text-gray-500 text-lg max-w-2xl mx-auto font-medium ">
                    Bantu kami meningkatkan kualitas OOPEDIA dengan memberikan penilaian jujur Anda.
                </p>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-8 md:p-12 border-b border-gray-50 bg-gray-50/50">
                    @if ($errors->any() || session('error'))
                        <div class="mb-10 p-6 bg-rose-50 border border-rose-100 rounded-3xl flex items-start gap-4 animate-in fade-in slide-in-from-top-4 duration-500">
                            <div class="w-12 h-12 bg-rose-500 text-white rounded-2xl flex items-center justify-center shrink-0 shadow-lg shadow-rose-100">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <h5 class="font-bold text-rose-900 uppercase tracking-widest mb-1">Terjadi Kesalahan!</h5>
                                <p class="text-rose-700 font-medium ">
                                    Ada {{ count(session('missingFields', [])) ?: $errors->count() }} pertanyaan yang belum dijawab. Mohon periksa kembali pilihan Anda.
                                </p>
                            </div>
                        </div>
                    @endif

                    <form id="ueqForm" method="POST" action="{{ route('mahasiswa.ueq.store') }}" class="space-y-12">
                        @csrf

                        {{-- Identity Section --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="group">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-blue-600 transition-colors">NIM Mahasiswa <span class="text-rose-500">*</span></label>
                                <x-ui.input type="text" id="nim" name="nim" :value="old('nim')" class="rounded-2xl border-gray-200 bg-white py-4 font-bold focus:ring-blue-500/20" placeholder="Masukkan NIM Anda" required />
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Nama Lengkap</label>
                                <div class="w-full px-5 py-4 border border-gray-200 rounded-2xl bg-gray-100 font-bold text-gray-500 ">
                                    {{ auth()->check() ? auth()->user()->name : 'Guest User' }}
                                </div>
                            </div>
                            <div class="group">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 group-focus-within:text-blue-600 transition-colors">Kelas <span class="text-rose-500">*</span></label>
                                <x-ui.input type="text" id="class" name="class" :value="old('class')" class="rounded-2xl border-gray-200 bg-white py-4 font-bold focus:ring-blue-500/20" placeholder="Contoh: SIB2A" required />
                            </div>
                        </div>

                        {{-- Questionnaire Section --}}
                        <div class="space-y-6 pt-6 border-t border-gray-100">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 shadow-inner">
                                    <i class="fas fa-list-ol text-sm"></i>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900  tracking-widest uppercase">Penilaian Skala UEQ</h4>
                            </div>

                            <div class="overflow-x-auto -mx-8 px-8">
                                <div class="min-w-[800px] pb-4">
                                    <div class="flex items-center text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6 px-4">
                                        <div class="w-1/4">Negatif</div>
                                        <div class="w-2/4 flex justify-between px-12">
                                            @for($i=1; $i<=7; $i++)
                                                <div class="w-8 text-center">{{ $i }}</div>
                                            @endfor
                                        </div>
                                        <div class="w-1/4 text-right">Positif</div>
                                    </div>

                                    <div class="space-y-3">
                                        @foreach([
                                            ['name' => 'annoying_enjoyable', 'left' => 'Menyebalkan', 'right' => 'Menyenangkan'],
                                            ['name' => 'not_understandable_understandable', 'left' => 'Sulit dipahami', 'right' => 'Mudah dipahami'],
                                            ['name' => 'creative_dull', 'left' => 'Kreatif', 'right' => 'Monoton'],
                                            ['name' => 'easy_difficult', 'left' => 'Mudah', 'right' => 'Sulit'],
                                            ['name' => 'valuable_inferior', 'left' => 'Bermanfaat', 'right' => 'Kurang bermanfaat'],
                                            ['name' => 'boring_exciting', 'left' => 'Membosankan', 'right' => 'Menarik'],
                                            ['name' => 'not_interesting_interesting', 'left' => 'Tidak menarik', 'right' => 'Menarik'],
                                            ['name' => 'unpredictable_predictable', 'left' => 'Sulit diprediksi', 'right' => 'Dapat diprediksi'],
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
                                            ['name' => 'meets_expectations_does_not_meet', 'left' => 'Sesuai ekspektasi', 'right' => 'Tidak sesuai'],
                                            ['name' => 'inefficient_efficient', 'left' => 'Tidak efisien', 'right' => 'Efisien'],
                                            ['name' => 'clear_confusing', 'left' => 'Jelas', 'right' => 'Membingungkan'],
                                            ['name' => 'impractical_practical', 'left' => 'Tidak praktis', 'right' => 'Praktis'],
                                            ['name' => 'organized_cluttered', 'left' => 'Terorganisir', 'right' => 'Berantakan'],
                                            ['name' => 'attractive_unattractive', 'left' => 'Menarik', 'right' => 'Tidak menarik'],
                                            ['name' => 'friendly_unfriendly', 'left' => 'Ramah', 'right' => 'Tidak ramah'],
                                            ['name' => 'conservative_innovative', 'left' => 'Konservatif', 'right' => 'Inovatif'],
                                        ] as $question)
                                        <div class="ueq-row group p-4 rounded-2xl transition-all duration-300 flex items-center gap-2 {{ in_array($question['name'], session('missingFields', [])) || $errors->has($question['name']) ? 'bg-rose-50 ring-2 ring-rose-200' : 'bg-white hover:bg-blue-50/50' }}">
                                            <div class="w-1/4 text-sm font-bold text-gray-500 ">{{ $question['left'] }}</div>
                                            <div class="w-2/4 flex justify-between px-10">
                                                @for ($i = 1; $i <= 7; $i++)
                                                    <label class="cursor-pointer relative flex flex-col items-center group/radio">
                                                        <input type="radio" name="{{ $question['name'] }}" value="{{ $i }}" {{ old($question['name']) == $i ? 'checked' : '' }} class="peer hidden" required>
                                                        <div class="w-8 h-8 rounded-full border-2 border-gray-200 bg-white peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:shadow-lg peer-checked:shadow-blue-200 transition-all group-hover/radio:border-blue-300"></div>
                                                        <div class="absolute inset-x-0 -bottom-1 h-0.5 bg-blue-600 opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                                    </label>
                                                @endfor
                                            </div>
                                            <div class="w-1/4 text-sm font-bold text-gray-500  text-right">{{ $question['right'] }}</div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Feedback Section --}}
                        <div class="pt-12 border-t border-gray-100 space-y-8">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 shadow-inner">
                                    <i class="fas fa-comment-alt text-sm"></i>
                                </div>
                                <h4 class="text-lg font-bold text-gray-900  tracking-widest uppercase">Tanggapan Kualitatif</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <x-forms.form-group label="Komentar Anda" name="comments" required="true">
                                    <textarea class="w-full px-5 py-4 border border-gray-200 rounded-2xl bg-gray-50/50 font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all placeholder:text-gray-300  min-h-[120px]"
                                        id="comments" name="comments" required placeholder="Ceritakan pengalaman Anda menggunakan OOPEDIA...">{{ old('comments') }}</textarea>
                                </x-forms.form-group>

                                <x-forms.form-group label="Saran Pengembangan" name="suggestions" required="true">
                                    <textarea class="w-full px-5 py-4 border border-gray-200 rounded-2xl bg-gray-50/50 font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all placeholder:text-gray-300  min-h-[120px]"
                                        id="suggestions" name="suggestions" required placeholder="Apa yang sebaiknya kami tingkatkan selanjutnya?">{{ old('suggestions') }}</textarea>
                                </x-forms.form-group>
                            </div>
                        </div>

                        <div class="pt-8 flex justify-center">
                            <button type="submit" class="group relative px-16 py-5 bg-gray-900 text-white rounded-[2rem] font-bold  tracking-[0.2em] uppercase overflow-hidden hover:bg-blue-600 transition-all shadow-2xl hover:shadow-blue-200 scale-100 active:scale-95">
                                <span class="relative z-10 flex items-center gap-3">
                                    Kirim Kuesioner
                                    <i class="fas fa-paper-plane group-hover:translate-x-2 group-hover:-translate-y-2 transition-transform"></i>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-slot:scripts>
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
