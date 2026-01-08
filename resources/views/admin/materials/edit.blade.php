<x-layouts.app title="Edit Materi" theme="admin">
    @push('head')

    @endpush

    <div class="space-y-12">
        <x-ui.page-header
            title="Pembaruan Kurikulum"
            subtitle="Modifikasi konten instruksional dan optimasi media visual."
        >
            <x-ui.button href="{{ route('admin.materials.index') }}" variant="ghost" icon="fas fa-arrow-left">BATALKAN MODIFIKASI</x-ui.button>
        </x-ui.page-header>

        <form action="{{ route('admin.materials.update', $material->id) }}" method="POST" id="materialForm" enctype="multipart/form-data" class="space-y-12">
            @csrf
            @method('PUT')

            <x-ui.card class="border-slate-100 shadow-2xl">
                <x-slot:header>Sinkronisasi & Konten Modul</x-slot:header>
                
                <div class="space-y-10">
                    {{-- Top Row: Title & Cover Image --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        <div class="lg:col-span-2 space-y-6">
                            <x-forms.form-group label="Revisi Judul" name="title" required>
                                <x-ui.input 
                                    name="title" 
                                    value="{{ old('title', $material->title) }}" 
                                    class="text-lg font-black italic tracking-tighter"
                                    required 
                                />
                            </x-forms.form-group>

                            <x-ui.alert variant="primary" :dismissible="false" class="bg-indigo-50/50 border-indigo-100">
                                <div class="flex gap-4">
                                    <i class="fas fa-sync text-indigo-500 mt-1"></i>
                                    <div class="text-[10px] font-bold text-slate-500 leading-relaxed uppercase tracking-widest">
                                        Perubahan pada modul ini akan langsung disinkronkan ke seluruh direktori belajar mahasiswa secara real-time.
                                    </div>
                                </div>
                            </x-ui.alert>
                        </div>

                        <div class="lg:col-span-1 space-y-4">
                            <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 block">Sinkronisasi Sampul</label>
                            <div id="imagePreview" class="relative group aspect-video rounded-2xl bg-slate-50 border-2 border-solid border-slate-200 flex flex-col items-center justify-center overflow-hidden transition-all hover:border-indigo-500/50">
                                @php
                                    $coverMedia = $material->media->first();
                                @endphp
                                <img id="cover_preview_img" 
                                     src="{{ $coverMedia ? asset($coverMedia->media_url) : '' }}" 
                                     class="absolute inset-0 w-full h-full object-cover {{ $coverMedia ? '' : 'hidden' }}">
                                
                                <div id="preview_placeholder" class="text-center group-hover:scale-110 transition-transform {{ $coverMedia ? 'hidden' : '' }}">
                                    <i class="fas fa-camera-retro text-slate-300 text-2xl mb-2"></i>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Masukkan Gambar</p>
                                </div>
                                <input type="file" name="cover_image" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" onchange="previewImage(this)">
                            </div>
                        </div>
                    </div>

                    {{-- Middle Row: WYSIWYG Editor --}}
                    <div class="space-y-4">
                        <x-forms.form-group label="Basis Pengetahuan Utama (WYSIWYG)" name="content">
                            <div class="quill-editor h-[500px] bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm" data-input="content"></div>
                            <input type="hidden" id="content" name="content" value="{{ old('content', $material->content) }}">
                        </x-forms.form-group>
                    </div>

                    {{-- Bottom Row: commit --}}
                    <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-cloud-upload-alt text-indigo-500 text-xs animation-pulse"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Sinkronisasi Cloud: Terhubung & Siap</span>
                        </div>
                        <x-ui.button type="submit" variant="primary" size="lg" class="shadow-xl shadow-indigo-500/20 bg-indigo-600 hover:bg-indigo-700" icon="fas fa-cloud-upload-alt">SIMPAN PERUBAHAN</x-ui.button>
                    </div>
                </div>
            </x-ui.card>
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