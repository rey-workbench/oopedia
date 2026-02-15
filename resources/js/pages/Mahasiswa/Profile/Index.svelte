<script>
    import App from "../../../layouts/App.svelte";
    import PageHeader from "../../../components/ui/PageHeader.svelte";
    import Card from "../../../components/ui/Card.svelte";
    import Button from "../../../components/ui/Button.svelte";
    import Input from "../../../components/ui/Input.svelte";
    import { useForm, page, Link } from "@inertiajs/svelte";
    import {
        UserCircle,
        Check,
        Lock,
        Loader2,
        Save,
        Home,
        ChevronRight,
        User,
        ShieldCheck,
        Mail,
        Brain,
        Eye,
        Zap,
        Trophy,
        Target,
        Flame,
        CheckCircle,
        XCircle,
        Lightbulb,
    } from "lucide-svelte";

    export let user;
    export let personalization = {};

    let form = useForm({
        name: user.name,
        email: user.email,
        password: "",
        password_confirmation: "",
        _method: "PUT",
    });

    function handleSubmit() {
        $form.post("/mahasiswa/profile", {
            onSuccess: () => {
                form.reset("password", "password_confirmation");
            },
        });
    }

    $: flash = $page.props.flash || {};
</script>

<App title="Profil Mahasiswa">
    <div class="space-y-12">
        <!-- Header -->
        <PageHeader
            title="Profil Saya"
            subtitle="Atur informasi akun dan keamanan Anda untuk pengalaman belajar yang lebih personal."
        >
            <div slot="actions">
                <div
                    class="flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 rounded-2xl border border-emerald-100"
                >
                    <ShieldCheck size={16} />
                    <span
                        class="text-[10px] font-bold uppercase tracking-widest"
                        >Akun Terverifikasi</span
                    >
                </div>
            </div>
        </PageHeader>

        <!-- Profile Hero Card -->
        <div
            class="relative overflow-hidden rounded-[3rem] bg-slate-900 p-8 md:p-12 text-white shadow-2xl"
        >
            <div
                class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-indigo-600/20 to-transparent"
            ></div>
            <div
                class="absolute -bottom-24 -right-24 w-96 h-96 bg-indigo-500/10 rounded-full blur-[100px]"
            ></div>

            <div
                class="relative z-10 flex flex-col md:flex-row items-center gap-10"
            >
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
                        class="text-[10px] font-bold uppercase tracking-widest text-indigo-400 mb-2"
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
                            <Mail size={14} class="text-indigo-400" />
                            {user.email}
                        </div>
                        <div
                            class="flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md rounded-xl border border-white/10 text-xs font-bold uppercase tracking-wider"
                        >
                            <User size={14} class="text-indigo-400" />
                            Mahasiswa
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personalization Section -->
        <div class="space-y-8">
            <h3
                class="text-xl font-bold tracking-widest text-slate-900 uppercase"
            >
                Data Personalisasi Pembelajaran
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Learning Style Card -->
                <Card
                    padding="p-8"
                    class="bg-gradient-to-br from-purple-50 to-white border-purple-100 shadow-lg shadow-purple-100/50"
                >
                    <div class="space-y-4">
                        <div
                            class="w-14 h-14 bg-purple-500 text-white rounded-[1.5rem] flex items-center justify-center shadow-xl shadow-purple-200"
                        >
                            {#if personalization.learning_style === "visual"}
                                <Eye size={28} />
                            {:else if personalization.learning_style === "auditory"}
                                <Brain size={28} />
                            {:else}
                                <Zap size={28} />
                            {/if}
                        </div>
                        <div>
                            <p
                                class="text-[9px] font-bold text-purple-400 uppercase tracking-widest mb-1"
                            >
                                Gaya Belajar
                            </p>
                            <p
                                class="text-2xl font-bold text-purple-900 tracking-widest uppercase"
                            >
                                {personalization.learning_style || "Visual"}
                            </p>
                        </div>
                    </div>
                </Card>

                <!-- Current Level Card -->
                <Card
                    padding="p-8"
                    class="bg-gradient-to-br from-blue-50 to-white border-blue-100 shadow-lg shadow-blue-100/50"
                >
                    <div class="space-y-4">
                        <div
                            class="w-14 h-14 bg-blue-500 text-white rounded-[1.5rem] flex items-center justify-center shadow-xl shadow-blue-200"
                        >
                            <Trophy size={28} />
                        </div>
                        <div>
                            <p
                                class="text-[9px] font-bold text-blue-400 uppercase tracking-widest mb-1"
                            >
                                Level Saat Ini
                            </p>
                            <p
                                class="text-2xl font-bold text-blue-900 tracking-widest uppercase"
                            >
                                {personalization.current_level || "Pemula"}
                            </p>
                            <p
                                class="text-[10px] font-bold text-blue-400 uppercase tracking-widest mt-2"
                            >
                                {personalization.global_xp || 0} XP
                            </p>
                        </div>
                    </div>
                </Card>

                <!-- Accuracy Card -->
                <Card
                    padding="p-8"
                    class="bg-gradient-to-br from-emerald-50 to-white border-emerald-100 shadow-lg shadow-emerald-100/50"
                >
                    <div class="space-y-4">
                        <div
                            class="w-14 h-14 bg-emerald-500 text-white rounded-[1.5rem] flex items-center justify-center shadow-xl shadow-emerald-200"
                        >
                            <Target size={28} />
                        </div>
                        <div>
                            <p
                                class="text-[9px] font-bold text-emerald-400 uppercase tracking-widest mb-1"
                            >
                                Akurasi
                            </p>
                            <p
                                class="text-2xl font-bold text-emerald-900 tracking-widest"
                            >
                                {personalization.accuracy || 0}%
                            </p>
                            <p
                                class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest mt-2"
                            >
                                {personalization.correct_count ||
                                    0}/{personalization.total_questions_answered ||
                                    0} Benar
                            </p>
                        </div>
                    </div>
                </Card>

                <!-- Streak Card -->
                <Card
                    padding="p-8"
                    class="bg-gradient-to-br from-orange-50 to-white border-orange-100 shadow-lg shadow-orange-100/50"
                >
                    <div class="space-y-4">
                        <div
                            class="w-14 h-14 bg-orange-500 text-white rounded-[1.5rem] flex items-center justify-center shadow-xl shadow-orange-200"
                        >
                            <Flame size={28} />
                        </div>
                        <div>
                            <p
                                class="text-[9px] font-bold text-orange-400 uppercase tracking-widest mb-1"
                            >
                                Streak
                            </p>
                            <p
                                class="text-2xl font-bold text-orange-900 tracking-widest"
                            >
                                {personalization.current_streak || 0} 🔥
                            </p>
                            <p
                                class="text-[10px] font-bold text-orange-400 uppercase tracking-widest mt-2"
                            >
                                Max: {personalization.max_streak || 0}
                            </p>
                        </div>
                    </div>
                </Card>
            </div>

            <!-- Detailed Stats -->
            <Card
                padding="p-8 md:p-12"
                class="bg-gradient-to-br from-slate-50 to-white border-slate-100"
            >
                <h4
                    class="text-sm font-bold text-slate-900 uppercase tracking-widest mb-8"
                >
                    Statistik Pembelajaran Detail
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Questions Answered -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center"
                            >
                                <CheckCircle size={24} />
                            </div>
                            <div>
                                <p
                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"
                                >
                                    Total Soal Dijawab
                                </p>
                                <p
                                    class="text-3xl font-bold text-slate-900 tracking-widest"
                                >
                                    {personalization.total_questions_answered ||
                                        0}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Correct Answers -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center"
                            >
                                <CheckCircle size={24} />
                            </div>
                            <div>
                                <p
                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"
                                >
                                    Jawaban Benar
                                </p>
                                <p
                                    class="text-3xl font-bold text-emerald-600 tracking-widest"
                                >
                                    {personalization.correct_count || 0}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Wrong Answers -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center"
                            >
                                <XCircle size={24} />
                            </div>
                            <div>
                                <p
                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"
                                >
                                    Jawaban Salah
                                </p>
                                <p
                                    class="text-3xl font-bold text-rose-600 tracking-widest"
                                >
                                    {personalization.wrong_count || 0}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-8 pt-8 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-8"
                >
                    <!-- Hints Usage -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center"
                            >
                                <Lightbulb size={20} />
                            </div>
                            <div>
                                <p
                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"
                                >
                                    Hints Digunakan
                                </p>
                                <p
                                    class="text-xl font-bold text-slate-900 tracking-widest"
                                >
                                    {personalization.hints_used_count || 0}
                                </p>
                            </div>
                        </div>
                        <div
                            class="px-4 py-2 bg-amber-50 text-amber-600 rounded-xl border border-amber-100"
                        >
                            <p
                                class="text-[10px] font-bold uppercase tracking-widest"
                            >
                                {personalization.hints_available || 3} Tersisa
                            </p>
                        </div>
                    </div>

                    <!-- Fast Track Status -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 {personalization.fast_track_active
                                    ? 'bg-purple-100 text-purple-600'
                                    : 'bg-slate-100 text-slate-400'} rounded-xl flex items-center justify-center"
                            >
                                <Zap size={20} />
                            </div>
                            <div>
                                <p
                                    class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"
                                >
                                    Status Fast Track
                                </p>
                                <p
                                    class="text-xl font-bold {personalization.fast_track_active
                                        ? 'text-purple-600'
                                        : 'text-slate-400'} tracking-widest uppercase"
                                >
                                    {personalization.fast_track_active
                                        ? "Aktif"
                                        : "Tidak Aktif"}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </Card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Sidebar Info -->
            <div class="space-y-8">
                <h3
                    class="text-xl font-bold tracking-widest text-slate-900 uppercase"
                >
                    Status Akun
                </h3>

                <Card padding="p-8" class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0"
                        >
                            <ShieldCheck size={20} />
                        </div>
                        <div>
                            <h4
                                class="font-bold text-slate-900 text-sm uppercase tracking-wider"
                            >
                                Identitas
                            </h4>
                            <p
                                class="text-slate-500 text-xs mt-1 leading-relaxed"
                            >
                                Informasi utama profil Anda yang terlihat oleh
                                dosen dan sistem.
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex items-start gap-4 pt-6 border-t border-slate-50"
                    >
                        <div
                            class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0"
                        >
                            <Lock size={20} />
                        </div>
                        <div>
                            <h4
                                class="font-bold text-slate-900 text-sm uppercase tracking-wider"
                            >
                                Keamanan
                            </h4>
                            <p
                                class="text-slate-500 text-xs mt-1 leading-relaxed"
                            >
                                Update password secara berkala untuk menjaga
                                keamanan akun Anda.
                            </p>
                        </div>
                    </div>
                </Card>

                <div
                    class="p-8 rounded-[2rem] bg-gradient-to-br from-slate-800 to-slate-900 text-white relative overflow-hidden group"
                >
                    <div
                        class="absolute -top-10 -right-10 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl transition-transform duration-1000"
                    ></div>
                    <h4
                        class="text-lg font-bold tracking-widest mb-4 uppercase"
                    >
                        Butuh Bantuan?
                    </h4>
                    <p
                        class="text-slate-400 text-xs font-medium mb-6 leading-relaxed"
                    >
                        Jika Anda mengalami kendala pada akun, silakan hubungi
                        tim administrator Oopedia.
                    </p>
                    <Button
                        variant="secondary"
                        size="sm"
                        class="w-full font-bold uppercase tracking-widest"
                        >Hubungi Admin</Button
                    >
                </div>
            </div>

            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-8">
                <h3
                    class="text-xl font-bold tracking-widest text-slate-900 uppercase"
                >
                    Pengaturan Profil
                </h3>

                <Card padding="p-8 md:p-12">
                    {#if flash && flash.success}
                        <div
                            class="mb-10 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-4 animate-in fade-in slide-in-from-top-4 duration-500"
                        >
                            <div
                                class="w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center shrink-0 shadow-lg"
                            >
                                <Check size={18} />
                            </div>
                            <p class="text-emerald-800 font-bold text-sm">
                                {flash.success}
                            </p>
                        </div>
                    {/if}

                    <form
                        on:submit|preventDefault={handleSubmit}
                        class="space-y-10"
                    >
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label
                                    for="name"
                                    class="block text-xs font-bold text-slate-400 uppercase tracking-widest px-1"
                                    >Nama Lengkap</label
                                >
                                <Input
                                    id="name"
                                    type="text"
                                    bind:value={$form.name}
                                    className="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
                                    placeholder="Masukkan nama lengkap"
                                    error={$form.errors.name}
                                />
                            </div>

                            <div class="space-y-3">
                                <label
                                    for="email"
                                    class="block text-xs font-bold text-slate-400 uppercase tracking-widest px-1"
                                    >Alamat Email</label
                                >
                                <Input
                                    id="email"
                                    type="email"
                                    bind:value={$form.email}
                                    className="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
                                    placeholder="email@contoh.com"
                                    error={$form.errors.email}
                                />
                            </div>
                        </div>

                        <div class="pt-10 border-t border-slate-100">
                            <h4
                                class="text-sm font-bold text-slate-900 uppercase tracking-widest mb-8"
                            >
                                Update Password
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-3">
                                    <label
                                        for="password"
                                        class="block text-xs font-bold text-slate-400 uppercase tracking-widest px-1"
                                        >Password Baru</label
                                    >
                                    <Input
                                        id="password"
                                        type="password"
                                        bind:value={$form.password}
                                        className="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
                                        placeholder="••••••••"
                                        error={$form.errors.password}
                                    />
                                    <p
                                        class="text-[10px] font-bold text-slate-400 px-1 italic"
                                    >
                                        * Kosongkan jika tidak ingin mengubah
                                        password
                                    </p>
                                </div>

                                <div class="space-y-3">
                                    <label
                                        for="password_confirmation"
                                        class="block text-xs font-bold text-slate-400 uppercase tracking-widest px-1"
                                        >Konfirmasi Password</label
                                    >
                                    <Input
                                        id="password_confirmation"
                                        type="password"
                                        bind:value={$form.password_confirmation}
                                        className="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
                                        placeholder="••••••••"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="pt-10 flex justify-end">
                            <Button
                                type="submit"
                                variant="primary"
                                size="xl"
                                class="w-full md:w-auto px-12 group"
                                disabled={$form.processing}
                            >
                                {#if $form.processing}
                                    <Loader2
                                        size={18}
                                        class="mr-2 animate-spin"
                                    /> Menyimpan...
                                {:else}
                                    <Save
                                        size={18}
                                        class="mr-2 group-hover:scale-110 transition-transform"
                                    /> Simpan Perubahan
                                {/if}
                            </Button>
                        </div>
                    </form>
                </Card>
            </div>
        </div>
    </div>
</App>
