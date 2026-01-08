<x-layouts.app title="Pending Requisitions" theme="admin">
    <div class="max-w-5xl mx-auto space-y-12">
        <x-ui.page-header
            title="Access Requisitions"
            subtitle="Otorisasi permohonan akses administratif dari entitas eksternal."
        >
            <x-ui.button href="{{ route('admin.users.index') }}" variant="ghost" icon="fas fa-arrow-left">BACK TO REPOSITORY</x-ui.button>
        </x-ui.page-header>

        @if(count($pendingAdmins) > 0)
            <x-ui.card padding="p-0" class="overflow-hidden border-slate-100 shadow-2xl">
                <x-slot:header>
                    <div class="flex items-center gap-4">
                        <div class="w-1.5 h-8 bg-amber-500 rounded-full animate-pulse"></div>
                        <h6 class="mb-0 italic font-black uppercase tracking-widest text-xs text-slate-400">Pending Authorization Queue</h6>
                    </div>
                </x-slot:header>

                <x-ui.table>
                    <x-slot:thead>
                        <tr>
                            <x-ui.th>Identity</x-ui.th>
                            <x-ui.th>Email Source</x-ui.th>
                            <x-ui.th class="text-center">Submission Date</x-ui.th>
                            <x-ui.th class="text-right">Aksi Otorisasi</x-ui.th>
                        </tr>
                    </x-slot:thead>
                    @foreach($pendingAdmins as $admin)
                        <tr class="group hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center font-black italic shadow-sm uppercase text-xs">
                                        {{ substr($admin->name, 0, 1) }}
                                    </div>
                                    <div class="font-bold text-slate-900 uppercase italic tracking-tighter">{{ $admin->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <span class="text-xs font-bold text-slate-400 italic underline decoration-slate-200 underline-offset-4">{{ $admin->email }}</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-[10px] font-black italic text-slate-400 uppercase tracking-widest">{{ $admin->created_at->format('d M Y H:i') }}</span>
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex justify-end gap-3">
                                    <form action="{{ route('admin.users.approve', $admin->id) }}" method="POST">
                                        @csrf
                                        <x-ui.button type="submit" variant="success" size="sm" icon="fas fa-user-check" class="shadow-lg shadow-emerald-500/20">
                                            APPROVE
                                        </x-ui.button>
                                    </form>
                                    <form action="{{ route('admin.users.reject', $admin->id) }}" method="POST">
                                        @csrf
                                        <x-ui.button type="submit" variant="danger" size="sm" icon="fas fa-user-xmark" class="shadow-lg shadow-rose-500/20" onclick="return confirm('Apakah Anda yakin ingin menolak admin ini? Akun akan diubah menjadi mahasiswa.')">
                                            REJECT
                                        </x-ui.button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table>
            </x-ui.card>
        @else
            <x-ui.card padding="p-20" class="text-center border-slate-100 shadow-xl">
                <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-slate-200">
                    <i class="fas fa-inbox text-3xl"></i>
                </div>
                <h3 class="text-lg font-black italic uppercase tracking-tighter text-slate-900 mb-2">Queue Zero</h3>
                <p class="text-slate-400 text-xs max-w-xs mx-auto">Tidak ada permohonan akses administratif yang menunggu otorisasi saat ini.</p>
            </x-ui.card>
        @endif
    </div>
    <x-admin.tutorial />
</x-layouts.app>