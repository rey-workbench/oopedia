<x-layouts.app title="Edit Materi" theme="admin">
    @push('head')
        <x-head.tinymce-config />
    @endpush

    <div class="space-y-12">
        <x-ui.page-header
            title="Curriculum Update"
            subtitle="Modifikasi konten instruksional dan optimasi media visual."
        >
            <x-ui.button href="{{ route('admin.materials.index') }}" variant="ghost" icon="fas fa-arrow-left">BATALKAN MODIFIKASI</x-ui.button>
        </x-ui.page-header>

        <form action="{{ route('admin.materials.update', $material->id) }}" method="POST" id="materialForm" enctype="multipart/form-data" class="space-y-12">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                {{-- Master Data --}}
                <div class="lg:col-span-2 space-y-8">
                    <x-ui.card class="border-slate-100 shadow-2xl">
                        <x-slot:header>Registry Update</x-slot:header>
                        <div class="space-y-8">
                            <x-forms.form-group label="Redesign Title" name="title" required>
                                <x-ui.input 
                                    name="title" 
                                    value="{{ old('title', $material->title) }}" 
                                    class="text-lg font-black italic tracking-tighter"
                                    required 
                                />
                            </x-forms.form-group>

                            <x-forms.form-group label="Core Knowledge Base (WYSIWYG)" name="content">
                                <div class="rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
                                    <textarea id="content-editor" name="content" class="@error('content') is-invalid @enderror">{{ old('content', $material->content) }}</textarea>
                                </div>
                            </x-forms.form-group>
                        </div>
                    </x-ui.card>
                </div>

                {{-- Visualization & Media --}}
                <div class="lg:col-span-1 space-y-8">
                    <x-ui.card class="bg-slate-900 border-0 shadow-2xl overflow-hidden relative">
                        <div class="absolute right-0 top-0 w-32 h-32 bg-indigo-600/10 blur-3xl"></div>
                        <x-slot:header class="border-slate-800">
                            <span class="text-white font-black italic tracking-widest text-[10px] uppercase">Cover Synchronization</span>
                        </x-slot:header>
                        
                        <div class="space-y-6">
                            <div id="imagePreview" class="relative group aspect-video rounded-3xl bg-slate-800 border-2 border-solid border-slate-700 flex flex-col items-center justify-center overflow-hidden transition-all hover:border-blue-500/50">
                                @php
                                    $coverMedia = $material->media->first();
                                @endphp
                                <img id="cover_preview_img" 
                                     src="{{ $coverMedia ? asset($coverMedia->media_url) : '' }}" 
                                     class="absolute inset-0 w-full h-full object-cover {{ $coverMedia ? '' : 'hidden' }}">
                                
                                <div id="preview_placeholder" class="text-center group-hover:scale-110 transition-transform {{ $coverMedia ? 'hidden' : '' }}">
                                    <i class="fas fa-camera-retro text-slate-600 text-3xl mb-4"></i>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Inject Identity Image</p>
                                </div>
                                <input type="file" name="cover_image" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" onchange="previewImage(this)">
                            </div>

                            <div class="p-6 bg-slate-800/50 border border-slate-700 rounded-[2rem] text-center">
                                <p class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-2">Metadata Summary</p>
                                <div class="text-[10px] text-white font-mono">
                                    {{ $coverMedia ? 'FILENAME: '.basename($coverMedia->media_url) : 'STATUS: NO ASSET FOUND' }}
                                </div>
                            </div>
                        </div>
                    </x-ui.card>

                    <x-ui.card class="border-indigo-100 shadow-xl p-8 bg-indigo-50/50">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/20">
                                <i class="fas fa-sync-alt text-xs animate-spin-slow"></i>
                            </div>
                            <h6 class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-900">Synchronize Cloud</h6>
                        </div>
                        <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest leading-loose mb-8 italic">
                            Semua perubahan konten dan media akan segera direplikasi ke direktori belajar mahasiswa.
                        </p>
                        <x-ui.button type="submit" variant="primary" size="lg" class="w-full shadow-2xl shadow-indigo-500/40 bg-indigo-600 hover:bg-indigo-700" icon="fas fa-cloud-upload-alt">COMMIT UPDATES</x-ui.button>
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
    <style>
        .animate-spin-slow {
            animation: spin 3s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
    @endpush
</x-layouts.app>