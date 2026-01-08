<x-layouts.app title="Detail Bank Soal" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Repository Insight"
            subtitle="Inspeksi konten dan statistik bank soal."
        >
            <div class="flex gap-4">
                <x-ui.button href="{{ route('admin.question-banks.manage-questions', $questionBank) }}" variant="primary" icon="fas fa-layer-group">KELOLA SOAL</x-ui.button>
                <x-ui.button href="{{ route('admin.question-banks.configure', $questionBank) }}" variant="secondary" icon="fas fa-cog">KONFIGURASI</x-ui.button>
                <x-ui.button href="{{ route('admin.question-banks.index') }}" variant="ghost" icon="fas fa-arrow-left">BACK</x-ui.button>
            </div>
        </x-ui.page-header>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Stats Cards --}}
            <x-ui.card class="border-emerald-100 bg-emerald-50/30">
                <div class="flex flex-col items-center text-center">
                    <span class="text-[9px] font-black uppercase tracking-widest text-emerald-600 mb-2">Beginner Scope</span>
                    <div class="text-4xl font-black italic tracking-tighter text-slate-900 mb-1">{{ $questionCounts['beginner'] }}</div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">Questions</span>
                </div>
            </x-ui.card>
            <x-ui.card class="border-amber-100 bg-amber-50/30">
                <div class="flex flex-col items-center text-center">
                    <span class="text-[9px] font-black uppercase tracking-widest text-amber-600 mb-2">Medium Scope</span>
                    <div class="text-4xl font-black italic tracking-tighter text-slate-900 mb-1">{{ $questionCounts['medium'] }}</div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">Questions</span>
                </div>
            </x-ui.card>
            <x-ui.card class="border-rose-100 bg-rose-50/30">
                <div class="flex flex-col items-center text-center">
                    <span class="text-[9px] font-black uppercase tracking-widest text-rose-600 mb-2">Hard Scope</span>
                    <div class="text-4xl font-black italic tracking-tighter text-slate-900 mb-1">{{ $questionCounts['hard'] }}</div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">Questions</span>
                </div>
            </x-ui.card>
            <x-ui.card class="bg-slate-900 border-0 shadow-2xl overflow-hidden relative">
                <div class="absolute right-0 top-0 w-32 h-32 bg-blue-600/10 blur-3xl"></div>
                <div class="flex flex-col items-center text-center relative z-10">
                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-2">Registry Creator</span>
                    <div class="text-xs font-black italic text-white uppercase mb-2">{{ $questionBank->creator->name ?? 'System Admin' }}</div>
                    <span class="text-[8px] font-bold text-slate-600 uppercase tracking-[0.3em] font-mono">{{ $questionBank->created_at->format('d.m.Y') }}</span>
                </div>
            </x-ui.card>
        </div>

        <x-ui.card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
            <x-slot:header>
                <div class="flex justify-between items-center w-full">
                    <h6 class="mb-0 italic font-black uppercase tracking-widest text-xs text-slate-400">Content Directory ({{ $questionBank->questions->count() }} Items)</h6>
                </div>
            </x-slot:header>

            <x-ui.table>
                <x-slot:thead>
                    <tr>
                        <x-ui.th>Question Snippet</x-ui.th>
                        <x-ui.th class="text-center">Complexity</x-ui.th>
                        <x-ui.th class="text-center">Evaluation Type</x-ui.th>
                        <x-ui.th class="text-right">Aksi</x-ui.th>
                    </tr>
                </x-slot:thead>
                @forelse($questionBank->questions as $question)
                <tr class="group hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-6">
                        <div class="text-xs font-bold text-slate-900 line-clamp-2 italic leading-relaxed">
                            {!! strip_tags($question->question_text) !!}
                        </div>
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
                            <form action="{{ route('admin.question-banks.remove-question', ['questionBank' => $questionBank, 'question' => $question]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit" variant="ghost" size="sm" class="text-slate-300 hover:text-rose-500" icon="fas fa-trash-alt" onclick="return confirm('Hapus soal dari bank ini?')" />
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-20 text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-question text-slate-200"></i>
                        </div>
                        <h3 class="text-lg font-black italic uppercase tracking-tighter text-slate-900 mb-2">Repository Empty</h3>
                        <x-ui.button variant="primary" size="sm" href="{{ route('admin.question-banks.manage-questions', $questionBank) }}" icon="fas fa-plus">INJECT NEW QUESTIONS</x-ui.button>
                    </td>
                </tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>
    </div>
</x-layouts.app>