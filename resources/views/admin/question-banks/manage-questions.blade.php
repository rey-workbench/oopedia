<x-layouts.app title="Kelola Soal Bank" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Question Injection Hub"
            subtitle="Pindahkan soal dari repositori umum ke dalam bank soal ini."
        >
            <x-ui.button href="{{ route('admin.question-banks.show', $questionBank) }}" variant="ghost" icon="fas fa-arrow-left">BACK TO BANK</x-ui.button>
        </x-ui.page-header>

        <x-ui.card class="border-slate-100 shadow-2xl">
            <x-slot:header>
                <form action="{{ route('admin.question-banks.manage-questions', $questionBank) }}" method="GET" class="w-full">
                    <div class="flex flex-col md:flex-row gap-6 w-full items-end">
                        <div class="flex-1 space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Search Query</label>
                            <x-ui.input name="search" value="{{ request('search') }}" placeholder="Cari teks soal..." />
                        </div>
                        <div class="w-full md:w-64 space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Complexity</label>
                            <select name="difficulty" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-black italic tracking-tighter outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase">
                                <option value="">ALL LEVELS</option>
                                <option value="beginner" {{ request('difficulty') == 'beginner' ? 'selected' : '' }}>BEGINNER</option>
                                <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>MEDIUM</option>
                                <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>HARD</option>
                            </select>
                        </div>
                        <x-ui.button type="submit" variant="primary" class="h-[52px] px-8" icon="fas fa-filter">APPLY FILTERS</x-ui.button>
                    </div>
                </form>
            </x-slot:header>

            <x-ui.table>
                <x-slot:thead>
                    <tr>
                        <x-ui.th>Question Content</x-ui.th>
                        <x-ui.th>Module Link</x-ui.th>
                        <x-ui.th class="text-center">Complexity</x-ui.th>
                        <x-ui.th class="text-center">Evaluation Type</x-ui.th>
                        <x-ui.th class="text-right">Aksi</x-ui.th>
                    </tr>
                </x-slot:thead>
                @forelse($questions as $question)
                <tr class="group hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-6 max-w-md">
                        <div class="text-xs font-bold text-slate-900 line-clamp-2 italic leading-relaxed">
                            {!! strip_tags($question->question_text) !!}
                        </div>
                    </td>
                    <td class="px-6 py-6">
                        <span class="text-[10px] font-black italic text-slate-400 uppercase tracking-tighter">
                            {{ $question->material->title ?? 'SYSTEM CORE' }}
                        </span>
                    </td>
                    <td class="px-6 py-6 text-center">
                        @php
                            $variant = $question->difficulty == 'beginner' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger');
                        @endphp
                        <x-ui.badge :variant="$variant" size="xs">{{ strtoupper($question->difficulty) }}</x-ui.badge>
                    </td>
                    <td class="px-6 py-6 text-center">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $question->formatted_type ?? strtoupper(str_replace('_', ' ', $question->question_type)) }}</span>
                    </td>
                    <td class="px-6 py-6">
                        <div class="flex justify-end">
                            <form action="{{ route('admin.question-banks.add-question', ['questionBank' => $questionBank, 'question' => $question]) }}" method="POST">
                                @csrf
                                <x-ui.button type="submit" variant="primary" size="sm" icon="fas fa-plus" class="shadow-lg shadow-blue-500/20">INJECT</x-ui.button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-20 text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-search text-slate-200"></i>
                        </div>
                        <h3 class="text-lg font-black italic uppercase tracking-tighter text-slate-900 mb-2">No Matches Found</h3>
                        <p class="text-slate-400 text-xs uppercase tracking-widest font-black">Repositori tidak memiliki soal yang belum ditambahkan.</p>
                    </td>
                </tr>
                @endforelse
            </x-ui.table>

            @if($questions->hasPages())
                <div class="p-8 border-t border-slate-100 bg-slate-50/30">
                    {{ $questions->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>