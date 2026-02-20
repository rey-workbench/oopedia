<script>
    import { UserCircle, Check, Mail, User } from "lucide-svelte";

    export let user;
</script>

<div
    class="relative overflow-hidden rounded-[3rem] bg-slate-900 p-8 md:p-12 text-white shadow-2xl"
>
    <div class="absolute top-0 right-0 w-1/2 h-full bg-primary-600/10"></div>
    <div
        class="absolute -bottom-24 -right-24 w-96 h-96 bg-primary-500/10 rounded-full blur-[100px]"
    ></div>

    <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
        <div class="relative group">
            <div
                class="w-32 h-32 bg-white rounded-[2.5rem] flex items-center justify-center shadow-2xl rotate-3 group-hover:rotate-0 transition-transform duration-500 overflow-hidden"
            >
                <UserCircle size={80} class="text-slate-200" />
            </div>
            <div
                class="absolute -bottom-2 -right-2 w-10 h-10 bg-emerald-500 rounded-2xl flex items-center justify-center text-white border-4 border-slate-900 shadow-xl"
            >
                <Check size={18} />
            </div>
        </div>

        <div class="text-center md:text-left">
            <p
                class="text-[10px] font-bold uppercase tracking-widest text-primary-400 mb-2"
            >
                MEMBER SINCE {new Date(user.created_at).getFullYear()}
            </p>
            <h2
                class="text-4xl md:text-5xl font-bold tracking-widest mb-4 text-white uppercase"
            >
                {user.name}
            </h2>
            <div
                class="flex flex-wrap items-center justify-center md:justify-start gap-4"
            >
                <div
                    class="flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md rounded-xl border border-white/10 text-xs font-bold uppercase tracking-wider"
                >
                    <Mail size={14} class="text-primary-400" />
                    {user.email}
                </div>
                <div
                    class="flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md rounded-xl border border-white/10 text-xs font-bold uppercase tracking-wider"
                >
                    <User size={14} class="text-primary-400" />
                    Mahasiswa
                </div>
            </div>
        </div>
    </div>
</div>
