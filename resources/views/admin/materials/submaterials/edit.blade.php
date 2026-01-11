<x-layouts.app title="Edit Sub-materi" theme="admin">
    <div class="max-w-4xl mx-auto space-y-12">
        <x-ui.page-header
            title="Edit Sub-materi"
            subtitle="Modul: {{ $material->title }}"
        >
            <x-ui.button href="{{ route('admin.materials.submaterials.index', $material->id) }}" variant="ghost" icon="fas fa-arrow-left">Kembali</x-ui.button>
        </x-ui.page-header>

        <x-ui.card class="border-slate-100 shadow-2xl">
            <form action="{{ route('admin.materials.submaterials.update', [$material->id, $submaterial->id]) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <x-forms.form-group label="Judul Sub-materi" name="title" required>
                        <x-ui.input name="title" placeholder="Contoh: Pengenalan Class & Object" required value="{{ old('title', $submaterial->title) }}" />
                    </x-forms.form-group>

                    <x-forms.form-group label="Urutan Tampil" name="order" required>
                        <x-ui.input type="number" name="order" placeholder="1" required value="{{ old('order', $submaterial->order) }}" />
                    </x-forms.form-group>
                </div>

                <x-forms.form-group label="Jenis Konten Utama" name="jenis_konten" required>
                    <select name="jenis_konten" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-100 focus:border-blue-600 transition-all outline-none">
                        <option value="teori" {{ old('jenis_konten', $submaterial->jenis_konten) == 'teori' ? 'selected' : '' }}>Teori (Konsep Dasar)</option>
                        <option value="sintaks" {{ old('jenis_konten', $submaterial->jenis_konten) == 'sintaks' ? 'selected' : '' }}>Sintaks (Kode Program)</option>
                        <option value="mixed" {{ old('jenis_konten', $submaterial->jenis_konten) == 'mixed' ? 'selected' : '' }}>Mixed (Teori & Kode)</option>
                    </select>
                </x-forms.form-group>

                <x-forms.form-group label="Isi Materi" name="content" required>
                    <div class="quill-editor h-[400px] bg-white border border-slate-200 rounded-2xl overflow-hidden" data-input="content"></div>
                    <input type="hidden" id="content" name="content" value="{{ old('content', $submaterial->content) }}">
                </x-forms.form-group>

                <div class="pt-6 border-t border-slate-100 flex justify-end">
                    <x-ui.button type="submit" variant="primary" size="lg" icon="fas fa-save">Perbarui Sub-materi</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>
