<x-layouts.app title="Admin Dashboard" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Strategic Command"
            subtitle="Pusat kendali operasional dan visualisasi data sistem OOPedia."
        />

        {{-- Main Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <x-ui.stat-card 
                title="Total Mahasiswa" 
                :value="$totalStudents" 
                icon="fas fa-users-viewfinder" 
                variant="primary"
                footer="Entitas terdaftar"
            />
            <x-ui.stat-card 
                title="Active Nodes" 
                :value="$activeStudents" 
                icon="fas fa-signal" 
                variant="success"
                footer="Sesi aktif hari ini"
            />
            <x-ui.stat-card 
                title="Modul Instruksional" 
                :value="$totalMaterials" 
                icon="fas fa-folder-tree" 
                variant="primary"
                footer="Konten aktif"
            />
            <x-ui.stat-card 
                title="Evaluation Corpus" 
                :value="$totalQuestions" 
                icon="fas fa-microchip" 
                variant="success"
                footer="Total butir evaluasi"
            />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            {{-- Top Students Table --}}
            <div class="lg:col-span-2">
                <x-ui.card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
                    <x-slot:header>
                        <div class="flex items-center justify-between w-full px-6 py-4">
                            <h6 class="mb-0 font-bold uppercase tracking-widest text-[10px] text-slate-400">Top Performance Matrices</h6>
                            <x-ui.button variant="ghost" size="sm" :href="route('admin.students.index')" icon="fas fa-arrow-right">GLOBAL DATA</x-ui.button>
                        </div>
                    </x-slot:header>
                    <x-ui.table>
                        <x-slot:thead>
                            <tr>
                                <x-ui.th>Subject Identity</x-ui.th>
                                <x-ui.th class="text-center">Evaluation Count</x-ui.th>
                                <x-ui.th class="text-center">Sync Progress</x-ui.th>
                                <x-ui.th class="text-right">Action</x-ui.th>
                            </tr>
                        </x-slot:thead>
                        @foreach($studentProgress as $student)
                            <tr class="group hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black italic text-sm shadow-lg shadow-slate-200 uppercase">
                                            {{ substr($student->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 tracking-tight leading-none mb-1">{{ $student->name }}</div>
                                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest italic">{{ $student->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-black italic text-slate-900">{{ $student->completed_questions }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-2">
                                        <div class="flex justify-between text-[8px] font-black uppercase italic tracking-widest text-slate-400">
                                            <span>Progress</span>
                                            <span>{{ $student->materials_progress }}%</span>
                                        </div>
                                        <x-ui.progress-bar :value="$student->materials_progress" size="xs" :showPercentage="false" variant="primary" />
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <x-ui.button variant="ghost" size="sm" :href="route('admin.students.progress', $student->id)" icon="fas fa-microscope" />
                                </td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                </x-ui.card>
            </div>

            {{-- Popular Materials --}}
            <div>
                <x-ui.card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
                    <x-slot:header>
                         <h6 class="mb-0 italic font-black uppercase tracking-widest text-xs text-slate-400 px-6 py-4">Content Heatmap</h6>
                    </x-slot:header>
                    <div class="space-y-4 p-6 bg-slate-50/50">
                        @foreach($popularMaterials as $material)
                            <div class="flex items-center gap-4 p-4 rounded-3xl bg-white border border-slate-100 group hover:border-blue-200 transition-all shadow-sm">
                                <div class="w-12 h-12 rounded-2xl bg-slate-900 flex items-center justify-center shadow-lg text-white transition-transform group-hover:scale-110">
                                    <i class="fas fa-layer-group text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-xs font-bold tracking-tight text-slate-900 truncate mb-1">{{ $material->title }}</h5>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $material->students_count }} Subjects</p>
                                </div>
                                <span class="text-xs font-black italic text-blue-600">{{ $material->completion_rate }}%</span>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            </div>
        </div>

        {{-- Recent Activity Timeline --}}
        <x-ui.card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
            <x-slot:header>
                 <h6 class="mb-0 italic font-black uppercase tracking-widest text-xs text-slate-400 px-6 py-4">Operations Log (Live)</h6>
            </x-slot:header>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 p-8">
                @foreach($recentProgress as $progress)
                    <div class="relative p-6 rounded-[2.5rem] bg-slate-50 border border-slate-100 group hover:bg-white transition-colors">
                        <div class="absolute top-6 right-6">
                            <x-ui.badge variant="{{ $progress->is_correct ? 'success' : 'warning' }}" size="xs">
                                {{ strtoupper($progress->question->complexity ?? 'LVL') }}
                            </x-ui.badge>
                        </div>
                        
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg {{ $progress->is_correct ? 'bg-emerald-500' : 'bg-amber-500' }} text-white flex items-center justify-center text-[10px] shadow-lg shadow-emerald-500/20">
                                    <i class="fas {{ $progress->is_correct ? 'fa-check' : 'fa-hourglass-start' }}"></i>
                                </div>
                                <div class="font-black italic text-slate-900 uppercase tracking-tighter text-xs">
                                    {{ optional($progress->user)->name ?? 'ENT-UNK' }}
                                </div>
                            </div>
                            
                            <p class="text-[11px] font-bold text-slate-500 leading-relaxed italic">
                                {{ $progress->is_correct ? 'Successfully decrypted' : 'Analyzing' }} module <span class="text-slate-900 underline decoration-blue-200 underline-offset-4">{{ optional($progress->material)->title ?? '-' }}</span>
                            </p>
                            
                            <div class="pt-4 border-t border-slate-200 flex justify-between items-center text-[9px] font-black text-slate-300 uppercase tracking-[0.2em] italic">
                                <span>{{ $progress->created_at->diffForHumans() }}</span>
                                <i class="fas fa-bolt text-blue-500 opacity-20"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.card>
    </div>

    <x-admin.tutorial />
    
    <x-slot:scripts>
        <script>
            var materialStats = {
                labels: {!! json_encode($materialStats->pluck('title')) !!},
                data: {!! json_encode($materialStats->pluck('completion_rate')) !!}
            };
        </script>
    </x-slot:scripts>
</x-layouts.app>
