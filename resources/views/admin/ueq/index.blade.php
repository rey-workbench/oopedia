<x-layouts.app title="UEQ Results" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Analisis Pengalaman Pengguna"
            subtitle="Hasil survei User Experience Questionnaire (UEQ) dari mahasiswa terdaftar."
        >
            <div class="flex items-center gap-3">
                <x-ui.button href="{{ route('admin.ueq.export') }}{{ request('class') ? '?class='.request('class') : '' }}" variant="success" icon="fas fa-file-csv">Ekspor Hasil</x-ui.button>
            </div>
        </x-ui.page-header>

        @if($surveys->isEmpty())
             <x-ui.card padding="p-20" class="text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-slate-200">
                    <i class="fas fa-chart-column text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold tracking-tight text-slate-900 mb-2">Belum Ada Data Survei</h3>
                <p class="text-slate-400 text-sm max-w-xs mx-auto">Mahasiswa belum mengisi survey UEQ. Data akan muncul secara otomatis setelah survey diselesaikan.</p>
            </x-ui.card>
        @else
            {{-- Filter Panel --}}
            <x-ui.card class="bg-slate-900 border-slate-800 shadow-2xl">
                <form action="{{ route('admin.ueq.index') }}" method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-end">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-500 ml-4">Segmentasi Kelas</label>
                            <select 
                                name="class" 
                                class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-2xl text-sm font-bold text-white focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none appearance-none cursor-pointer"
                            >
                                <option value="">Semua Kelas</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class }}" {{ request('class') == $class ? 'selected' : '' }}>
                                        {{ $class }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-4">
                            <x-ui.button type="submit" variant="primary" class="flex-1 py-3.5 shadow-lg shadow-blue-500/20">Terapkan Filter</x-ui.button>
                            <x-ui.button variant="ghost" href="{{ route('admin.ueq.index') }}" class="px-6 py-3.5 text-slate-400">Atur Ulang</x-ui.button>
                        </div>
                    </div>
                </form>
            </x-ui.card>

            {{-- Dimensions Overview --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                @foreach($averages as $dimension => $score)
                    <x-ui.card padding="p-6" class="text-center group hover:border-blue-200 transition-all border-slate-100">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">{{ $dimension }}</span>
                        <div class="text-2xl font-bold tracking-tight text-slate-900 mb-2">{{ number_format($score, 2) }}</div>
                        <x-ui.progress-bar :value="($score/7)*100" size="xs" :showPercentage="false" variant="{{ $score >= 5 ? 'success' : ($score >= 3 ? 'orange' : 'danger') }}" />
                    </x-ui.card>
                @endforeach
            </div>

            {{-- Main Data Table --}}
            <x-ui.card padding="p-0" class="overflow-hidden">
                <x-slot:header>
                    <h6 class="mb-0 font-bold uppercase tracking-widest text-[10px] text-slate-400">Rincian Respon Survei</h6>
                </x-slot:header>
                <x-ui.table>
                    <thead>
                        <tr>
                            <x-ui.th>Responden</x-ui.th>
                            <x-ui.th class="text-center">Kelas</x-ui.th>
                            <x-ui.th class="text-center">Attr</x-ui.th>
                            <x-ui.th class="text-center">Persp</x-ui.th>
                            <x-ui.th class="text-center">Effic</x-ui.th>
                            <x-ui.th class="text-center">Depen</x-ui.th>
                            <x-ui.th class="text-center">Stim</x-ui.th>
                            <x-ui.th class="text-center">Novel</x-ui.th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($surveys as $survey)
                            <tr class="group hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-[10px]">
                                            {{ substr($survey->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-[11px] font-bold text-slate-900 tracking-tight">{{ $survey->user->name }}</div>
                                            <div class="text-[9px] font-medium text-slate-400">{{ $survey->nim }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="text-[10px] font-bold px-2 py-1 bg-slate-900 text-white rounded-md uppercase">{{ $survey->class }}</span>
                                </td>
                                @foreach(['attractiveness', 'perspicuity', 'efficiency', 'dependability', 'stimulation', 'novelty'] as $dim)
                                    @php
                                        $val = 0;
                                        if($dim == 'attractiveness') $val = ($survey->annoying_enjoyable + $survey->good_bad + $survey->unlikable_pleasing + $survey->unpleasant_pleasant + $survey->attractive_unattractive + $survey->friendly_unfriendly) / 6;
                                        elseif($dim == 'perspicuity') $val = ($survey->not_understandable_understandable + $survey->easy_difficult + $survey->complicated_easy + $survey->clear_confusing) / 4;
                                        elseif($dim == 'efficiency') $val = ($survey->fast_slow + $survey->inefficient_efficient + $survey->impractical_practical + $survey->organized_cluttered) / 4;
                                        elseif($dim == 'dependability') $val = ($survey->unpredictable_predictable + $survey->obstructive_supportive + $survey->secure_not_secure + $survey->meets_expectations_does_not_meet) / 4;
                                        elseif($dim == 'stimulation') $val = ($survey->valuable_inferior + $survey->boring_exciting + $survey->not_interesting_interesting + $survey->motivating_demotivating) / 4;
                                        elseif($dim == 'novelty') $val = ($survey->creative_dull + $survey->inventive_conventional + $survey->usual_leading_edge + $survey->conservative_innovative) / 4;
                                    @endphp
                                    <td class="px-6 py-5 text-center font-black italic text-xs {{ $val >= 5 ? 'text-emerald-500' : ($val >= 3 ? 'text-orange-500' : 'text-slate-300') }}">
                                        {{ number_format($val, 1) }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
            </x-ui.card>

            {{-- Feedback Section --}}
            <x-ui.card padding="p-0" class="overflow-hidden">
                <x-slot:header>
                    <h6 class="mb-0 font-bold uppercase tracking-widest text-[10px] text-slate-400">Feedback & Saran Mahasiswa</h6>
                </x-slot:header>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-8">
                    @foreach($surveys->whereNotNull('comments') as $survey)
                        <div class="p-6 rounded-[2rem] bg-slate-50 border border-slate-100 relative group hover:border-blue-200 transition-all">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 rounded-xl bg-white shadow-sm flex items-center justify-center text-blue-600">
                                    <i class="fas fa-comment-dots text-xs"></i>
                                </div>
                                <div class="font-bold text-[11px] tracking-tight text-slate-900">{{ $survey->user->name }}</div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400 block mb-1">Komentar</span>
                                    <p class="text-[11px] font-medium text-slate-600 leading-relaxed">"{{ $survey->comments }}"</p>
                                </div>
                                @if($survey->suggestions)
                                <div>
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-emerald-500 block mb-1">Saran Perbaikan</span>
                                    <p class="text-[11px] font-medium text-slate-600 leading-relaxed">{{ $survey->suggestions }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="mt-6 pt-4 border-t border-slate-200 flex justify-between items-center text-[9px] font-black text-slate-300 uppercase tracking-widest">
                                <span>{{ $survey->created_at->format('d M Y') }}</span>
                                <x-ui.button variant="ghost" size="sm" href="{{ route('admin.ueq.detail', $survey->user_id) }}" icon="fas fa-arrow-right" />
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        @endif
    </div>
</x-layouts.app>