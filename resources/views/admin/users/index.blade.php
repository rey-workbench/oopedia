<x-layouts.app title="Manajemen Admin" theme="admin">
    <div class="space-y-12">
        <x-ui.page-header
            title="Akses Kontrol Admin"
            subtitle="Kelola akun Administrator dan Dosen pembimbing sistem."
        >
            @if(auth()->user()->role_id == 1)
            <div class="flex flex-wrap items-center gap-4">
                @php
                    $pendingAdminsCount = \App\Models\User::where('role_id', 2)->where('is_approved', false)->count();
                @endphp
                @if($pendingAdminsCount > 0)
                    <x-ui.button href="{{ route('admin.pending-admins') }}" variant="danger" icon="fas fa-clock">
                        {{ $pendingAdminsCount }} Permintaan Menunggu
                    </x-ui.button>
                @endif
                <x-ui.button href="{{ route('admin.users.create') }}" variant="primary" icon="fas fa-plus">Tambah User</x-ui.button>
            </div>
            @endif
        </x-ui.page-header>

        <x-ui.card padding="p-0" class="overflow-hidden">
            <x-slot:header>
                <div class="flex flex-col md:flex-row justify-between items-center gap-6 w-full">
                    <h6 class="mb-0">Direktori Pengguna Sistem</h6>
                    <form method="GET" action="{{ route('admin.users.index') }}" class="w-full md:w-auto">
                        <div class="relative group">
                            <i class="fas fa-shield-halved absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
                            <input 
                                type="text" 
                                name="search" 
                                placeholder="Cari nama atau email..." 
                                value="{{ request('search') }}"
                                class="w-full md:w-64 pl-12 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-100 focus:border-blue-600 transition-all outline-none"
                            >
                        </div>
                    </form>
                </div>
            </x-slot:header>

            <x-ui.table>
                <thead>
                    <tr>
                        <x-ui.th>Identitas</x-ui.th>
                        <x-ui.th>Otorisasi Email</x-ui.th>
                        <x-ui.th class="text-center">Peran Sistem</x-ui.th>
                        <x-ui.th class="text-center">Status Akses</x-ui.th>
                        <x-ui.th class="text-right">Aksi</x-ui.th>
                    </tr>
                </thead>
                <tbody>
                    @if(auth()->user()->role_id == 1)
                        @php $superadmins = \App\Models\User::where('role_id', 1)->get(); @endphp
                        @foreach($superadmins as $superadmin)
                        <tr class="bg-slate-900/5 group transition-colors">
                            <td class="px-6 py-6 border-l-4 border-slate-900">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold shadow-lg shadow-slate-200 uppercase text-xs">
                                        {{ substr($superadmin->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 tracking-tight">{{ $superadmin->name }}</div>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">ADMIN SISTEM UTAMA</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <span class="text-xs font-bold text-slate-400 underline decoration-slate-200 underline-offset-4">{{ $superadmin->email }}</span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <x-ui.badge variant="dark" size="xs">SUPERADMIN</x-ui.badge>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-600">TANPA BATAS</span>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex justify-end">
                                    @if(auth()->id() == $superadmin->id)
                                        <x-ui.button variant="ghost" size="sm" href="{{ route('admin.users.edit', $superadmin->id) }}" icon="fas fa-user-gear" />
                                    @else
                                        <x-ui.button variant="ghost" size="sm" href="{{ route('admin.users.edit', $superadmin->id) }}" icon="fas fa-pen-nib" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @endif

                    @foreach($users as $user)
                    <tr class="group hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center font-bold shadow-sm uppercase text-xs">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div class="font-bold text-slate-900">{{ $user->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <span class="text-xs font-bold text-slate-400">{{ $user->email }}</span>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <x-ui.badge variant="{{ $user->role_id == 2 ? 'primary' : 'secondary' }}" size="xs">{{ strtoupper($user->role->role_name) }}</x-ui.badge>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-2 h-2 rounded-full {{ $user->is_approved ? 'bg-emerald-500' : 'bg-amber-500' }}"></div>
                                <span class="text-[10px] font-bold uppercase tracking-widest {{ $user->is_approved ? 'text-emerald-500' : 'text-amber-500' }}">
                                    {{ $user->is_approved ? 'DISETUJUI' : 'MENUNGGU' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex justify-end gap-2">
                                <x-ui.button variant="ghost" size="sm" href="{{ route('admin.users.edit', $user->id) }}" icon="fas fa-pen-fancy" />
                                @if(auth()->user()->role_id == 1 && auth()->id() != $user->id)
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button type="submit" variant="danger" size="sm" icon="fas fa-user-xmark" onclick="return confirm('Hapus akses admin ini?')" />
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </x-ui.table>

            @if($users->hasPages())
                <div class="p-6 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>