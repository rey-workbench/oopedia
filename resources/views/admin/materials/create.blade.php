<x-layouts.app title="Tambah Materi" theme="admin">
  @push('head')

  @endpush

  <div class="space-y-12">
    <x-ui.page-header
      title="Arsitek Konten Kurikulum"
      subtitle="Publikasikan modul pembelajaran baru dengan visualisasi premium."
    >
      <x-ui.button href="{{ route('admin.materials.index') }}" variant="ghost" icon="fas fa-arrow-left">BATALKAN PUBLIKASI</x-ui.button>
    </x-ui.page-header>

    <form action="{{ route('admin.materials.store') }}" method="POST" id="materialForm" enctype="multipart/form-data" class="space-y-12">
      @csrf
      <input type="hidden" name="created_by" value="{{ auth()->id() }}">

      <x-ui.card class="border-slate-100 shadow-2xl">
        <x-slot:header>Identifikasi & Konten Modul</x-slot:header>
        
        <div class="space-y-10">
          {{-- Top Row: Title & Cover Image --}}
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 space-y-6">
              <x-forms.form-group label="Judul Modul" name="title" required>
                <x-ui.input 
                  name="title" 
                  value="{{ old('title') }}" 
                  placeholder="e.g. Fundamental of Object Oriented Programming" 
                  class="text-lg font-bold tracking-widest"
                  required 
                />
              </x-forms.form-group>

              <x-ui.alert variant="primary" :dismissible="false" class="bg-blue-50/50 border-blue-100">
                <div class="flex gap-4">
                  <i class="fas fa-circle-info text-blue-500 mt-1"></i>
                  <div class="text-[10px] font-bold text-slate-500 leading-relaxed uppercase tracking-widest">
                    Pastikan judul modul mendeskripsikan isi materi dengan jelas untuk memudahkan mahasiswa.
                  </div>
                </div>
              </x-ui.alert>
            </div>

            <div class="lg:col-span-1 space-y-4">
              <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 block">Visualisasi Sampul</label>
              <div id="imagePreview" class="relative group aspect-video rounded-2xl bg-slate-50 border-2 border-dashed border-slate-200 flex flex-col items-center justify-center overflow-hidden transition-all hover:border-blue-500/50">
                <img id="cover_preview_img" src="" class="absolute inset-0 w-full h-full object-cover hidden">
                <div id="preview_placeholder" class="text-center group-hover:scale-110 transition-transform">
                  <i class="fas fa-cloud-arrow-up text-slate-300 text-2xl mb-2"></i>
                  <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Unggah Sampul</p>
                </div>
                <input type="file" name="cover_image" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" onchange="previewImage(this)">
              </div>
            </div>
          </div>

          {{-- Middle Row: WYSIWYG Editor --}}
          <div class="space-y-4">
            <x-forms.form-group label="Konten Instruksional" name="content">
              <div class="quill-editor h-[500px] bg-white border border-slate-200 rounded-2xl overflow-hidden" data-input="content"></div>
              <input type="hidden" id="content" name="content" value="{{ old('content') }}">
            </x-forms.form-group>
          </div>

          {{-- Bottom Row: Deployment --}}
          <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <i class="fas fa-rocket text-blue-500 text-xs"></i>
              <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Status Kesiapan: Siap Dipublikasikan</span>
            </div>
            <x-ui.button type="submit" variant="primary" size="lg" class="shadow-xl shadow-blue-500/20" icon="fas fa-check-double">PUBLIKASIKAN MODUL</x-ui.button>
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
  @endpush
</x-layouts.app>
