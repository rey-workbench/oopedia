<x-layouts.app title="Daftar Soal" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="{{ $material ? 'Module: ' . $material->title : 'Global Repository' }}"
            subtitle="Daftar butir soal evaluasi yang terdaftar dalam sistem."
        >
            <x-ui.button 
                href="{{ $material ? route('admin.materials.questions.create', $material) : route('admin.questions.create') }}" 
                variant="primary" 
                icon="fas fa-plus"
            >
                Insert New Question
            </x-ui.button>
        </x-ui.page-header>

        {{-- Filter Hub --}}
        <x-ui.card class="border-slate-100 shadow-xl">
            <form method="GET" action="{{ $material ? route('admin.materials.questions.index', $material) : route('admin.questions.index') }}">
                <div class="flex flex-col md:flex-row gap-6 items-end">
                    <div class="flex-1 space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 font-poppins">Query Search</label>
                        <x-ui.input name="search" placeholder="Cari teks soal, tipe, atau pembuat..." value="{{ request('search') }}" />
                    </div>
                    <div class="w-full md:w-64 space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Complexity</label>
                        <select name="difficulty" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold tracking-tight outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase font-poppins">
                            <option value="">ALL LEVELS</option>
                            <option value="beginner" {{ request('difficulty') == 'beginner' ? 'selected' : '' }}>BEGINNER</option>
                            <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>MEDIUM</option>
                            <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>HARD</option>
                        </select>
                    </div>
                    <x-ui.button type="submit" variant="primary" class="h-[52px] px-8" icon="fas fa-search">Execute Search</x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
            <x-ui.table>
                <x-slot:thead>
                    <tr>
                        <x-ui.th>Origin Module</x-ui.th>
                        <x-ui.th>Question Insight</x-ui.th>
                        <x-ui.th class="text-center">Evaluation Type</x-ui.th>
                        <x-ui.th class="text-center">Complexity</x-ui.th>
                        <x-ui.th class="text-right">Actions</x-ui.th>
                    </tr>
                </x-slot:thead>
                @foreach($questions as $question)
                <tr class="group hover:bg-slate-50 transition-colors border-b border-slate-50">
                    <td class="px-6 py-8 align-top">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-8 bg-blue-600 rounded-full"></div>
                            <span class="text-[10px] font-bold text-slate-900 uppercase tracking-widest">{{ $question->material->title }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-8">
                        <div class="space-y-4">
                            <div class="text-xs font-medium text-slate-700 leading-relaxed line-clamp-2">
                                {!! strip_tags($question->question_text) !!}
                            </div>
                            
                            {{-- Integrated Answers Insight --}}
                            <div class="p-4 bg-slate-900/5 rounded-2xl border border-slate-100 space-y-2">
                                <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Answer Key Registry:</span>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    @foreach($question->answers as $answer)
                                        <div class="flex items-center gap-2">
                                            <div class="w-1 h-1 rounded-full {{ $answer->is_correct ? 'bg-emerald-500' : 'bg-slate-300' }}"></div>
                                            <span class="text-[9px] font-medium {{ $answer->is_correct ? 'text-emerald-600' : 'text-slate-500' }} truncate">{{ $answer->answer_text }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-8 text-center align-top">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">{{ $question->formatted_type }}</span>
                    </td>
                    <td class="px-6 py-8 text-center align-top">
                        @php
                            $variant = $question->difficulty == 'beginner' ? 'success' : ($question->difficulty == 'medium' ? 'warning' : 'danger');
                        @endphp
                        <x-ui.badge :variant="$variant" size="xs">{{ strtoupper($question->difficulty) }}</x-ui.badge>
                    </td>
                    <td class="px-6 py-8 align-top">
                        <div class="flex justify-end gap-2">
                            @if($material)
                                <x-ui.button variant="ghost" size="sm" href="{{ route('admin.materials.questions.edit', ['material' => $material, 'question' => $question]) }}" icon="fas fa-edit" />
                                <form action="{{ route('admin.materials.questions.destroy', ['material' => $material, 'question' => $question]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button type="submit" variant="ghost" size="sm" class="text-slate-300 hover:text-rose-500" icon="fas fa-trash-alt" onclick="return confirm('Hapus permanen soal ini?')" />
                                </form>
                            @else
                                <x-ui.button variant="ghost" size="sm" href="{{ route('admin.questions.edit', $question) }}" icon="fas fa-edit" />
                                <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button type="submit" variant="ghost" size="sm" class="text-slate-300 hover:text-rose-500" icon="fas fa-trash-alt" onclick="return confirm('Hapus permanen soal ini?')" />
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </x-ui.table>

            @if($questions->hasPages())
                <div class="p-8 border-t border-slate-100 bg-slate-50/30">
                    {{ $questions->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>
