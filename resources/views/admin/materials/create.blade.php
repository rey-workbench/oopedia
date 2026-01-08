<x-layouts.app title="Tambah Materi" theme="admin">
    @push('head')
        <x-head.tinymce-config />
    @endpush

    <div class="space-y-12">
        <x-ui.page-header
            title="Curriculum Content Architect"
            subtitle="Publikasikan modul pembelajaran baru dengan visualisasi premium."
        >
            <x-ui.button href="{{ route('admin.materials.index') }}" variant="ghost" icon="fas fa-arrow-left">BATALKAN PUBLIKASI</x-ui.button>
        </x-ui.page-header>

        <form action="{{ route('admin.materials.store') }}" method="POST" id="materialForm" enctype="multipart/form-data" class="space-y-12">
            @csrf
            <input type="hidden" name="created_by" value="{{ auth()->id() }}">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                {{-- Master Data --}}
                <div class="lg:col-span-2 space-y-8">
                    <x-ui.card class="border-slate-100 shadow-2xl">
                        <x-slot:header>Identity & Core Content</x-slot:header>
                        <div class="space-y-8">
                            <x-forms.form-group label="Descriptive Title" name="title" required>
                                <x-ui.input 
                                    name="title" 
                                    value="{{ old('title') }}" 
                                    placeholder="e.g. Fundamental of Object Oriented Programming" 
                                    class="text-lg font-black italic tracking-tighter"
                                    required 
                                />
                            </x-forms.form-group>

                            <x-forms.form-group label="Academic Content (WYSIWYG Engine)" name="content">
                                <div class="rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
                                    <textarea id="content-editor" name="content" class="@error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                                </div>
                            </x-forms.form-group>
                        </div>
                    </x-ui.card>
                </div>

                {{-- Visualization & Media --}}
                <div class="lg:col-span-1 space-y-8">
                    <x-ui.card class="bg-slate-900 border-0 shadow-2xl overflow-hidden relative">
                        <div class="absolute right-0 top-0 w-32 h-32 bg-blue-600/10 blur-3xl"></div>
                        <x-slot:header class="border-slate-800">
                            <span class="text-white font-black italic tracking-widest text-[10px] uppercase">Cover Visualization</span>
                        </x-slot:header>
                        
                        <div class="space-y-6">
                            <div id="imagePreview" class="relative group aspect-video rounded-3xl bg-slate-800 border-2 border-dashed border-slate-700 flex flex-col items-center justify-center overflow-hidden transition-all hover:border-blue-500/50">
                                <img id="cover_preview_img" src="" class="absolute inset-0 w-full h-full object-cover hidden">
                                <div id="preview_placeholder" class="text-center group-hover:scale-110 transition-transform">
                                    <i class="fas fa-cloud-arrow-up text-slate-600 text-3xl mb-4"></i>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Drop Identity Image</p>
                                </div>
                                <input type="file" name="cover_image" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" onchange="previewImage(this)">
                            </div>

                            <x-ui.alert variant="primary" :dismissible="false" class="bg-slate-800/50 border-slate-700">
                                <div class="flex gap-4">
                                    <i class="fas fa-circle-info text-blue-400 mt-1"></i>
                                    <div class="text-[9px] font-bold text-slate-400 leading-relaxed uppercase tracking-widest">
                                        <span class="text-white block mb-1">Optimum Specs:</span>
                                        - 16:9 Aspect Ratio<br>
                                        - 1280x720 (720p)<br>
                                        - Max 2MB (JPG/PNG)
                                    </div>
                                </div>
                            </x-ui.alert>
                        </div>
                    </x-ui.card>

                    <x-ui.card class="border-slate-100 shadow-xl p-8 bg-slate-50">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center">
                                <i class="fas fa-rocket text-xs"></i>
                            </div>
                            <h6 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-900">Deployment Readiness</h6>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-loose mb-8">
                            Modul ini akan langsung tersedia untuk seluruh mahasiswa setelah dipublikasikan. Pastikan konten sudah valid.
                        </p>
                        <x-ui.button type="submit" variant="primary" size="lg" class="w-full shadow-2xl shadow-blue-500/40" icon="fas fa-check-double">PUBLISH MODULE</x-ui.button>
                    </x-ui.card>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function previewImage(input) {
            const preview = document.getElementById('cover_preview_img');
            const placeholder = document.getElementById('preview_placeholder');
            const container = document.getElementById('imagePreview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    container.classList.remove('border-dashed');
                    container.classList.add('border-solid', 'border-blue-500/30');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
</x-layouts.app>
