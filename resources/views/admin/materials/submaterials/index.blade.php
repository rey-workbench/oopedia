<x-layouts.app title="Kelola Sub-materi" theme="admin">
  <div class="space-y-12">
    <x-ui.page-header
      title="Kelola Sub-materi"
      subtitle="Modul: {{ $material->title }}"
    >
      <div class="flex gap-4">
        <x-ui.button href="{{ route('admin.materials.index') }}" variant="ghost" icon="fas fa-arrow-left">Kembali</x-ui.button>
        <x-ui.button href="{{ route('admin.materials.submaterials.create', $material->id) }}" variant="primary" icon="fas fa-plus">Tambah Sub-materi</x-ui.button>
      </div>
    </x-ui.page-header>

    <x-ui.card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
      <x-ui.table>
        <x-slot:thead>
          <tr>
            <x-ui.th class="w-16 text-center">Urutan</x-ui.th>
            <x-ui.th>Judul Sub-materi</x-ui.th>
            <x-ui.th>Jenis Konten</x-ui.th>
            <x-ui.th class="text-right">Operasi</x-ui.th>
          </tr>
        </x-slot:thead>
        <tbody>
          @forelse($subMaterials as $sub)
          <tr class="group hover:bg-slate-50 transition-colors">
            <td class="px-6 py-6 text-center font-bold text-slate-400">
              {{ $sub->order }}
            </td>
            <td class="px-6 py-6 font-bold text-slate-900">
              {{ $sub->title }}
            </td>
            <td class="px-6 py-6">
              <span class="text-[10px] font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full uppercase tracking-widest">
                {{ $sub->jenis_konten }}
              </span>
            </td>
            <td class="px-6 py-6">
              <div class="flex justify-end gap-3">
                <x-ui.button variant="ghost" size="sm" href="{{ route('admin.materials.submaterials.edit', [$material->id, $sub->id]) }}" icon="fas fa-pen-nib" />
                <form action="{{ route('admin.materials.submaterials.destroy', [$material->id, $sub->id]) }}" method="POST" class="inline">
                  @csrf
                  @method('DELETE')
                  <x-ui.button type="submit" variant="ghost" size="sm" class="text-slate-300 hover:text-rose-500" icon="fas fa-trash-can" onclick="return confirm('Hapus sub-materi ini?')" />
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="4" class="p-24 text-center text-slate-400">
              Belum ada sub-materi.
            </td>
          </tr>
          @endforelse
        </tbody>
      </x-ui.table>
    </x-ui.card>
  </div>
</x-layouts.app>
