<x-layouts.app title="Detail Aturan Adaptif" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Logic Logic Detail"
            subtitle="Inspeksi mendalam konfigurasi dan alur kerja engine adaptif."
        >
            <div class="flex gap-4">
                <x-ui.button href="{{ route('admin.adaptive-rules.edit', $adaptiveRule) }}" variant="primary" icon="fas fa-edit">EDIT ARCHITECTURE</x-ui.button>
                <x-ui.button href="{{ route('admin.adaptive-rules.index') }}" variant="ghost" icon="fas fa-arrow-left">BACK TO FLEET</x-ui.button>
            </div>
        </x-ui.page-header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            {{-- Technical Specs --}}
            <div class="lg:col-span-1 space-y-8">
                <x-ui.card padding="p-0" class="overflow-hidden shadow-2xl border-slate-100">
                    <div class="p-6 bg-slate-900 text-white">
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Technical Specifications</span>
                        <h4 class="text-xl font-black italic tracking-tighter uppercase mt-2">{{ $adaptiveRule->name }}</h4>
                    </div>
                    <div class="p-8 space-y-8">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Operational Status</span>
                            <x-ui.badge :variant="$adaptiveRule->is_active ? 'success' : 'secondary'" class="text-[10px] px-4 py-1.5">
                                {{ $adaptiveRule->is_active ? 'RUNNING' : 'DISABLED' }}
                            </x-ui.badge>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Priority Index</span>
                            <div class="text-3xl font-black italic tracking-tighter text-slate-900 border-l-4 border-blue-600 pl-4">{{ $adaptiveRule->priority }}</div>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 block mb-2">Scope Enforcement</span>
                            <div class="flex items-center gap-3">
                                <i class="fas fa-microchip text-blue-600"></i>
                                <span class="font-bold text-slate-900 text-sm">{{ $adaptiveRule->material ? $adaptiveRule->material->title : 'Global Access' }}</span>
                            </div>
                        </div>
                        <div class="pt-6 border-t border-slate-100 italic text-xs text-slate-400 font-medium">
                            Synthesized on {{ $adaptiveRule->created_at->format('M d, Y') }} by {{ $adaptiveRule->creator->name ?? 'System' }}
                        </div>
                    </div>
                </x-ui.card>

                <x-ui.card class="bg-blue-600 text-white border-0 shadow-2xl shadow-blue-500/20 px-8 py-10 relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 opacity-20 transform rotate-12">
                        <i class="fas fa-brain text-[150px]"></i>
                    </div>
                    <h5 class="text-lg font-black italic tracking-tighter uppercase mb-4 relative z-10">Logic Rationale</h5>
                    <p class="text-xs font-bold leading-relaxed text-blue-50 italic relative z-10">
                        "{{ $adaptiveRule->description ?? 'No formal documentation provided for this logic module.' }}"
                    </p>
                </x-ui.card>
            </div>

            {{-- Logic Execution Plan --}}
            <div class="lg:col-span-2 space-y-12">
                <div class="relative">
                    <div class="absolute left-1/2 top-0 bottom-0 w-1 bg-slate-200 -translate-x-1/2"></div>
                    
                    <div class="space-y-24 relative z-10">
                        {{-- IF BLOCK --}}
                        <div class="flex flex-col items-center">
                            <div class="px-8 py-2 bg-blue-600 text-white rounded-full text-[10px] font-black italic tracking-widest uppercase mb-8 shadow-xl">Trigger Logic</div>
                            <x-ui.card padding="p-8" class="w-full border-blue-100 shadow-2xl bg-white">
                                <div class="space-y-6">
                                    @foreach($adaptiveRule->conditions ?? [] as $index => $condition)
                                        <div class="flex items-center gap-6 p-6 bg-slate-50 rounded-[2rem] border border-slate-100 group hover:border-blue-200 transition-all">
                                            <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-blue-600 font-black italic text-xs">
                                                {{ $index + 1 }}
                                            </div>
                                            <div class="flex-1 grid grid-cols-3 gap-8">
                                                <div>
                                                    <span class="text-[9px] font-black uppercase text-slate-400 block mb-1">Variable</span>
                                                    <span class="font-black italic text-slate-900 border-b-2 border-blue-600">{{ $condition['key'] ?? 'N/A' }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-[9px] font-black uppercase text-slate-400 block mb-1">Operator</span>
                                                    <span class="font-black italic text-blue-600">{{ $condition['operator'] ?? '==' }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-[9px] font-black uppercase text-slate-400 block mb-1">Threshold</span>
                                                    <span class="font-black italic text-slate-900">{{ $condition['value'] ?? '0' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </x-ui.card>
                        </div>

                        {{-- THEN BLOCK --}}
                        <div class="flex flex-col items-center">
                            <div class="px-8 py-2 bg-emerald-500 text-white rounded-full text-[10px] font-black italic tracking-widest uppercase mb-8 shadow-xl">Execution Action</div>
                            <x-ui.card padding="p-8" class="w-full border-emerald-100 shadow-2xl bg-white">
                                <div class="space-y-6">
                                    @foreach($adaptiveRule->actions ?? [] as $index => $action)
                                        <div class="flex items-center gap-6 p-6 bg-emerald-50/30 rounded-[2rem] border border-emerald-100 group hover:border-emerald-300 transition-all">
                                            <i class="fas fa-bolt text-emerald-500"></i>
                                            <div class="flex-1 grid grid-cols-3 gap-8">
                                                <div>
                                                    <span class="text-[9px] font-black uppercase text-slate-400 block mb-1">Target</span>
                                                    <span class="font-black italic text-slate-900">{{ $action['key'] ?? 'N/A' }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-[9px] font-black uppercase text-slate-400 block mb-1">Operation</span>
                                                    <span class="font-black italic text-emerald-600 uppercase">{{ $action['operator'] == '=' ? 'SET TO' : ($action['operator'] == '+' ? 'INCREMENT BY' : ($action['operator'] == '-' ? 'DECREMENT BY' : 'MULTIPLY BY')) }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-[9px] font-black uppercase text-slate-400 block mb-1">Impact Value</span>
                                                    <span class="font-black italic text-slate-900">{{ $action['value'] ?? '0' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </x-ui.card>
                        </div>
                    </div>
                </div>

                <div class="p-10 bg-slate-50 rounded-[3rem] border border-dashed border-slate-200 mt-20">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 rounded-[2rem] bg-slate-900 text-white flex items-center justify-center">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <div class="flex-1">
                            <h6 class="text-sm font-black italic tracking-tight uppercase text-slate-900">Logic Decommissioning</h6>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Hapus modul ini secara permanen dari sistem inti.</p>
                        </div>
                        <form action="{{ route('admin.adaptive-rules.destroy', $adaptiveRule) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="danger" size="sm" onclick="return confirm('Initiate logic deletion?')">TERMINATE MODULE</x-ui.button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
