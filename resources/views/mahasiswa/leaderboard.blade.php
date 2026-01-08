<x-layouts.app title="Leaderboard" theme="mahasiswa">
    <div class="space-y-12">
        <x-ui.page-header
            title="Leaderboard"
            subtitle="Peringkat Terbaik Mahasiswa Berdasarkan Progres Pembelajaran"
        />

        <div class="space-y-12">
            <x-ui.card padding="p-0" :hover="false" class="overflow-hidden">
                {{-- Podium Section --}}
                <div class="bg-gradient-to-b from-slate-50 to-white pt-20 pb-12 px-8">
                    <div class="flex justify-center items-end gap-4 md:gap-12 max-w-5xl mx-auto">
                        {{-- Rank 2 --}}
                        <div class="flex-1 flex flex-col items-center order-1">
                            @if(isset($leaderboardData[1]) && $leaderboardData[1]->total_correct_questions > 0)
                                <div class="text-center mb-8 group">
                                    <div class="relative mb-6">
                                        <div class="w-20 h-20 md:w-24 md:h-24 bg-slate-200 rounded-[2rem] flex items-center justify-center text-4xl shadow-inner border-4 border-white mx-auto group-hover:scale-110 group-hover:-rotate-3 transition-all duration-500">🥈</div>
                                        <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-white rounded-2xl flex items-center justify-center shadow-lg border-2 border-slate-100 text-[10px] font-black italic">2nd</div>
                                    </div>
                                    <h5 class="font-black text-slate-800 text-lg mb-1 truncate max-w-[140px] uppercase tracking-tighter italic">{{ $leaderboardData[1]->name }}</h5>
                                    <div class="text-blue-600 font-black italic tracking-tighter text-sm">{{ $leaderboardData[1]->formatted_score }} PTS</div>
                                </div>
                                <div class="w-full h-40 md:h-48 bg-gradient-to-t from-slate-300 to-slate-200 rounded-t-[2rem] shadow-inner flex items-center justify-center relative overflow-hidden">
                                    <div class="absolute inset-x-0 top-0 h-1 bg-white/20"></div>
                                    <div class="text-5xl md:text-7xl font-black text-white/40 italic tracking-tighter">2</div>
                                </div>
                            @endif
                        </div>

                        {{-- Rank 1 --}}
                        <div class="flex-1 flex flex-col items-center order-2">
                            @if(isset($leaderboardData[0]) && $leaderboardData[0]->total_correct_questions > 0)
                                <div class="text-center mb-8 group relative z-10">
                                    <div class="absolute -top-14 left-1/2 -translate-x-1/2">
                                        <i class="fas fa-crown text-amber-400 text-5xl animate-bounce drop-shadow-[0_0_15px_rgba(251,191,36,0.5)]"></i>
                                    </div>
                                    <div class="relative mb-6">
                                        <div class="w-24 h-24 md:w-32 md:h-32 bg-amber-400 rounded-[2.5rem] flex items-center justify-center text-6xl shadow-2xl border-4 border-white mx-auto group-hover:scale-110 transition-all duration-500 ring-[12px] ring-amber-400/20">🥇</div>
                                        <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-xl border-2 border-amber-50 text-xs font-black italic">1st</div>
                                    </div>
                                    <h5 class="font-black text-slate-900 text-xl md:text-2xl mb-1 truncate max-w-[180px] italic uppercase tracking-tighter">{{ $leaderboardData[0]->name }}</h5>
                                    <div class="text-amber-600 font-black italic tracking-tighter text-xl">{{ $leaderboardData[0]->formatted_score }} PTS</div>
                                </div>
                                <div class="w-full h-56 md:h-64 bg-gradient-to-t from-amber-500 to-amber-400 rounded-t-[3rem] shadow-2xl shadow-amber-200/50 flex items-center justify-center relative overflow-hidden">
                                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white/30 to-transparent"></div>
                                    <div class="absolute inset-x-0 top-0 h-1.5 bg-white/30"></div>
                                    <div class="text-8xl md:text-9xl font-black text-white/40 italic tracking-tighter">1</div>
                                </div>
                            @endif
                        </div>

                        {{-- Rank 3 --}}
                        <div class="flex-1 flex flex-col items-center order-3">
                            @if(isset($leaderboardData[2]) && $leaderboardData[2]->total_correct_questions > 0)
                                <div class="text-center mb-8 group">
                                    <div class="relative mb-6">
                                        <div class="w-20 h-20 md:w-24 md:h-24 bg-rose-200 rounded-[2rem] flex items-center justify-center text-4xl shadow-inner border-4 border-white mx-auto group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">🥉</div>
                                        <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-white rounded-2xl flex items-center justify-center shadow-lg border-2 border-rose-50 text-[10px] font-black italic">3rd</div>
                                    </div>
                                    <h5 class="font-black text-slate-800 text-lg mb-1 truncate max-w-[140px] uppercase tracking-tighter italic">{{ $leaderboardData[2]->name }}</h5>
                                    <div class="text-blue-600 font-black italic tracking-tighter text-sm">{{ $leaderboardData[2]->formatted_score }} PTS</div>
                                </div>
                                <div class="w-full h-32 md:h-40 bg-gradient-to-t from-rose-400 to-rose-300 rounded-t-[2rem] shadow-inner flex items-center justify-center relative overflow-hidden">
                                    <div class="absolute inset-x-0 top-0 h-1 bg-white/20"></div>
                                    <div class="text-4xl md:text-6xl font-black text-white/40 italic tracking-tighter">3</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Table Section --}}
                <div class="bg-white">
                    <x-ui.table>
                        <thead>
                            <tr>
                                <x-ui.th>Peringkat</x-ui.th>
                                <x-ui.th>Mahasiswa</x-ui.th>
                                <x-ui.th>Kategori</x-ui.th>
                                <x-ui.th class="text-center">Progress</x-ui.th>
                                <x-ui.th class="text-right">Total Skor</x-ui.th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaderboardData as $data)
                                @if($data->total_correct_questions > 0)
                                <tr class="group hover:bg-slate-50 transition-all {{ $data->id === auth()->id() ? 'bg-blue-50/50' : '' }}">
                                    <td class="px-6 py-6">
                                        @if($data->rank <= 3)
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-white italic shadow-lg
                                                @if($data->rank === 1) bg-amber-400 shadow-amber-100
                                                @elseif($data->rank === 2) bg-slate-300 shadow-slate-100
                                                @else bg-rose-400 shadow-rose-100 @endif">
                                                {{ $data->rank }}
                                            </div>
                                        @else
                                            <span class="w-10 text-center block font-black text-slate-300 italic">#{{ $data->rank }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center uppercase font-black italic text-xs shadow-lg shadow-slate-200">
                                                {{ substr($data->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-black text-slate-900 italic tracking-tight uppercase">{{ $data->name }}</div>
                                                <div class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-0.5">
                                                    {{ $data->completion_date ? date('d M Y', strtotime($data->completion_date)) : 'Aktif Belajar' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <x-ui.badge variant="{{ $data->badge_color }}" size="xs">
                                            {{ $data->badge }}
                                        </x-ui.badge>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="w-32 mx-auto">
                                            <div class="flex justify-between items-center mb-1.5 px-0.5">
                                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter italic">{{ $data->percentage }}%</span>
                                            </div>
                                            <x-ui.progress-bar :value="$data->percentage" size="xs" :showPercentage="false" variant="{{ $data->badge_color }}" />
                                        </div>
                                    </td>
                                    <td class="px-6 py-6 text-right">
                                        <div class="font-black text-blue-600 italic text-xl tracking-tighter">
                                            {{ $data->formatted_score }} <span class="text-[10px] text-slate-300 not-italic uppercase font-bold tracking-widest ml-1">Pts</span>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </x-ui.table>
                </div>
            </x-ui.card>
        </div>
    </div>

    @push('scripts')
    <script>
        const leaderboardConfig = {
            currentUserRank: {{ $currentUserRank ? $currentUserRank->rank : 'null' }}
        };
    </script>
    @endpush
</x-layouts.app>
