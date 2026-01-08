<x-layouts.app title="Tambah Admin" theme="admin">
    <div class="max-w-4xl mx-auto space-y-12">
        <x-ui.page-header
            title="Administrator Genesis"
            subtitle="Otorisasi entitas baru ke dalam pusat kendali sistem."
        >
            <x-ui.button href="{{ route('admin.users.index') }}" variant="ghost" icon="fas fa-arrow-left">BACK TO LIST</x-ui.button>
        </x-ui.page-header>

        <x-ui.card class="border-slate-100 shadow-2xl">
            <x-slot:header>
                <div class="flex items-center gap-4">
                    <div class="w-1.5 h-8 bg-blue-600 rounded-full"></div>
                    <h6 class="mb-0 italic font-black uppercase tracking-widest text-xs text-slate-400">Credential Architecture</h6>
                </div>
            </x-slot:header>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-10">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Full Identity</label>
                        <x-ui.input name="name" value="{{ old('name') }}" placeholder="Subject's full name" required />
                    </div>
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Digital Alias (Email)</label>
                        <x-ui.input type="email" name="email" value="{{ old('email') }}" placeholder="Subject's electronic mail" required />
                    </div>
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Security Key</label>
                        <x-ui.input type="password" name="password" placeholder="Initialize password" required />
                    </div>
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">Verify Security Key</label>
                        <x-ui.input type="password" name="password_confirmation" placeholder="Re-initialize password" required />
                    </div>
                    <div class="md:col-span-2 space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 italic">System Role Authorization</label>
                        <select name="role_id" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-3xl text-xs font-black italic tracking-tighter outline-none focus:ring-4 focus:ring-blue-100 transition-all appearance-none cursor-pointer uppercase font-poppins" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ strtoupper($role->role_name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-10">
                    <x-ui.button type="submit" variant="primary" class="w-full h-[60px] shadow-2xl shadow-blue-500/30" icon="fas fa-user-shield">
                        AUTHORIZE ENTITY
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
    <x-admin.tutorial />
</x-layouts.app>