<x-layouts.app title="Detail UEQ Survey" theme="admin">
    <div class="max-w-6xl mx-auto space-y-12">
        <x-ui.page-header
            title="Dokumen Riset UEQ"
            subtitle="Analisis psikometrik pengalaman pengguna untuk entitas {{ $user->name }}."
        >
            <x-ui.button href="{{ route('admin.ueq.index') }}" variant="ghost" icon="fas fa-arrow-left">KEMBALI KE DAFTAR</x-ui.button>
        </x-ui.page-header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            {{-- Profile Column --}}
            <div class="space-y-8">
                <x-ui.card class="border-slate-100 shadow-xl overflow-hidden">
                    <x-slot:header>
                        <div class="flex items-center gap-4">
                            <div class="w-1.5 h-8 bg-blue-600 rounded-full"></div>
                            <h6 class="mb-0  font-bold uppercase tracking-widest text-xs text-slate-400">Profil Subjek</h6>
                        </div>
                    </x-slot:header>
                    
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-[2rem] bg-slate-900 text-white flex items-center justify-center text-xl font-bold shadow-2xl">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-lg font-bold tracking-widest text-slate-900 uppercase leading-none mb-1">{{ $user->name }}</h4>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">ID: {{ $survey->nim }}</p>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-100 grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-1">Kelas</p>
                                <p class="text-xs font-bold text-slate-900">{{ $survey->class }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-1">Status</p>
                                <x-ui.badge variant="success" size="xs">STABIL</x-ui.badge>
                            </div>
                        </div>

                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <p class="text-[8px] font-bold uppercase tracking-widest text-slate-400 mb-2">Timestamp Log</p>
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-600">
                                <i class="fas fa-calendar-check text-blue-500"></i>
                                {{ $survey->created_at->format('d M Y') }}
                                <span class="mx-1 text-slate-300">@</span>
                                {{ $survey->created_at->format('H:i') }}
                            </div>
                        </div>
                    </div>
                </x-ui.card>

                {{-- Dimension Summary --}}
                <x-ui.card padding="p-0" class="border-slate-100 shadow-xl overflow-hidden">
                    <x-slot:header>
                        <div class="flex items-center gap-4">
                            <div class="w-1.5 h-8 bg-emerald-500 rounded-full"></div>
                            <h6 class="mb-0  font-bold uppercase tracking-widest text-xs text-slate-400">Agregasi Metrik</h6>
                        </div>
                    </x-slot:header>
                    @php
                        $dimensions = [
                            'Daya Tarik' => ($survey->annoying_enjoyable + $survey->good_bad + $survey->unlikable_pleasing + $survey->unpleasant_pleasant + $survey->attractive_unattractive + $survey->friendly_unfriendly) / 6,
                            'Kejelasan' => ($survey->not_understandable_understandable + $survey->easy_difficult + $survey->complicated_easy + $survey->clear_confusing) / 4,
                            'Efisiensi' => ($survey->fast_slow + $survey->inefficient_efficient + $survey->impractical_practical + $survey->organized_cluttered) / 4,
                            'Keandalan' => ($survey->unpredictable_predictable + $survey->obstructive_supportive + $survey->secure_not_secure + $survey->meets_expectations_does_not_meet) / 4,
                            'Stimulasi' => ($survey->valuable_inferior + $survey->boring_exciting + $survey->not_interesting_interesting + $survey->motivating_demotivating) / 4,
                            'Kebaruan' => ($survey->creative_dull + $survey->inventive_conventional + $survey->usual_leading_edge + $survey->conservative_innovative) / 4,
                        ];
                    @endphp
                    <div class="divide-y divide-slate-100">
                        @foreach($dimensions as $name => $score)
                            <div class="px-6 py-4 flex items-center justify-between group hover:bg-slate-50 transition-colors">
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $name }}</span>
                                <div class="flex items-center gap-3">
                                    <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-blue-600 rounded-full" style="width: {{ ($score/7)*100 }}%"></div>
                                    </div>
                                    <span class="text-sm font-bold text-slate-900">{{ number_format($score, 2) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            </div>

            {{-- Main Content Column --}}
            <div class="lg:col-span-2 space-y-12">
                {{-- Qualitative Feedback --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <x-ui.card class="border-amber-100 bg-amber-50/10">
                        <h6 class="text-[10px] font-bold uppercase tracking-widest text-amber-600 mb-4 flex items-center gap-2 ">
                            <i class="fas fa-comment-nodes"></i> Komentar Kualitatif
                        </h6>
                        <p class="text-xs font-bold text-slate-700  leading-relaxed">
                            "{{ $survey->comments ?: 'Tidak ada masukan kualitatif yang tercatat untuk subjek ini.' }}"
                        </p>
                    </x-ui.card>
                    <x-ui.card class="border-blue-100 bg-blue-50/10">
                        <h6 class="text-[10px] font-bold uppercase tracking-widest text-blue-600 mb-4 flex items-center gap-2 ">
                            <i class="fas fa-lightbulb"></i> Proposal Optimalisasi
                        </h6>
                        <p class="text-xs font-bold text-slate-700  leading-relaxed">
                            "{{ $survey->suggestions ?: 'Subjek tidak memberikan proposal optimalisasi.' }}"
                        </p>
                    </x-ui.card>
                </div>

                {{-- Detailed Aspects Table --}}
                <x-ui.card padding="p-0" class="border-slate-100 shadow-2xl overflow-hidden">
                    <x-slot:header>
                        <div class="flex items-center gap-4">
                            <div class="w-1.5 h-8 bg-slate-900 rounded-full"></div>
                            <h6 class="mb-0  font-bold uppercase tracking-widest text-xs text-slate-400">Matriks Distribusi Psikometrik</h6>
                        </div>
                    </x-slot:header>
                    @php
                        $aspects = [
                            ['name' => 'annoying_enjoyable', 'left' => 'Menyebalkan', 'right' => 'Menyenangkan'],
                            ['name' => 'not_understandable_understandable', 'left' => 'Tidak dipahami', 'right' => 'Dapat dipahami'],
                            ['name' => 'creative_dull', 'left' => 'Kreatif', 'right' => 'Monoton'],
                            ['name' => 'easy_difficult', 'left' => 'Mudah', 'right' => 'Sulit'],
                            ['name' => 'valuable_inferior', 'left' => 'Bermanfaat', 'right' => 'Inferior'],
                            ['name' => 'boring_exciting', 'left' => 'Membosankan', 'right' => 'Menarik'],
                            ['name' => 'not_interesting_interesting', 'left' => 'Tidak menarik', 'right' => 'Menarik'],
                            ['name' => 'unpredictable_predictable', 'left' => 'Unpredictable', 'right' => 'Predictable'],
                            ['name' => 'fast_slow', 'left' => 'Cepat', 'right' => 'Lambat'],
                            ['name' => 'inventive_conventional', 'left' => 'Inovatif', 'right' => 'Konvensional'],
                            ['name' => 'obstructive_supportive', 'left' => 'Menghambat', 'right' => 'Mendukung'],
                            ['name' => 'good_bad', 'left' => 'Baik', 'right' => 'Buruk'],
                            ['name' => 'complicated_easy', 'left' => 'Rumit', 'right' => 'Sederhana'],
                            ['name' => 'unlikable_pleasing', 'left' => 'Unlikable', 'right' => 'Pleasing'],
                            ['name' => 'usual_leading_edge', 'left' => 'Biasa saja', 'right' => 'Terdepan'],
                            ['name' => 'unpleasant_pleasant', 'left' => 'Unpleasant', 'right' => 'Pleasant'],
                            ['name' => 'secure_not_secure', 'left' => 'Aman', 'right' => 'Tidak aman'],
                            ['name' => 'motivating_demotivating', 'left' => 'Memotivasi', 'right' => 'Demotivating'],
                            ['name' => 'meets_expectations_does_not_meet', 'left' => 'Meets Expect.', 'right' => 'Doesn\'t Meet'],
                            ['name' => 'inefficient_efficient', 'left' => 'Tidak efisien', 'right' => 'Efisien'],
                            ['name' => 'clear_confusing', 'left' => 'Jelas', 'right' => 'Membingungkan'],
                            ['name' => 'impractical_practical', 'left' => 'Tidak praktis', 'right' => 'Praktis'],
                            ['name' => 'organized_cluttered', 'left' => 'Terorganisir', 'right' => 'Berantakan'],
                            ['name' => 'attractive_unattractive', 'left' => 'Menarik', 'right' => 'Tidak menarik'],
                            ['name' => 'friendly_unfriendly', 'left' => 'Ramah', 'right' => 'Tidak ramah'],
                            ['name' => 'conservative_innovative', 'left' => 'Konservatif', 'right' => 'Inovatif'],
                        ];
                    @endphp
                    <x-ui.table>
                        <x-slot:thead>
                            <tr>
                                <x-ui.th>Identifikasi Aspek</x-ui.th>
                                <x-ui.th class="text-center">Peta Skalar (1-7)</x-ui.th>
                                <x-ui.th class="text-right">Rentang Semantik</x-ui.th>
                            </tr>
                        </x-slot:thead>
                        @foreach($aspects as $aspect)
                            @php $val = $survey->{$aspect['name']}; @endphp
                            <tr class="group hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="text-[10px] font-bold text-slate-900 uppercase tracking-widest">{{ str_replace('_', ' ', $aspect['name']) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        @for($i=1; $i<=7; $i++)
                                            <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-bold transition-all
                                                {{ $val == $i ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30 scale-110' : 'bg-slate-100 text-slate-300' }}">
                                                {{ $i }}
                                            </div>
                                        @endfor
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex flex-col items-end">
                                        <span class="text-[8px] font-bold uppercase text-slate-400  mb-1">Pasangan Semantik:</span>
                                        <div class="text-[10px] font-bold text-slate-600">
                                            <span class="{{ $val <= 3 ? 'text-blue-600' : '' }}">{{ $aspect['left'] }}</span>
                                            <span class="mx-2 text-slate-200">|</span>
                                            <span class="{{ $val >= 5 ? 'text-blue-600' : '' }}">{{ $aspect['right'] }}</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                </x-ui.card>
            </div>
        </div>
    </div>
    <x-admin.tutorial />
</x-layouts.app>