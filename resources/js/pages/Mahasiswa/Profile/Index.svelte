<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import DarkHeroPanel from "@/components/ui/DarkHeroPanel.svelte";
    import Card from "@/components/ui/Card.svelte";
    import Input from "@/components/ui/Input.svelte";
    import Button from "@/components/ui/Button.svelte";
    import Alert from "@/components/ui/Alert.svelte";
    import StatsGrid from "@/components/ui/StatsGrid.svelte";
    import {
        ShieldCheck,
        UserCircle,
        Mail,
        User as UserIcon,
        Check,
        Lock,
        Eye,
        Brain,
        Zap,
        Trophy,
        Target,
        Flame,
        CheckCircle,
        XCircle,
        Lightbulb,
        Loader2,
        Save,
    } from "lucide-svelte";
    import { useForm, page } from "@inertiajs/svelte";
    import { ProfileState } from "@/states/Mahasiswa/ProfileState.svelte";

    export let personalization = {};

    const state = new ProfileState(personalization);

    let form = useForm({
        name: state.user.name,
        email: state.user.email,
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

    $: personalizationStats = [
        {
            title: "Gaya Belajar",
            value: state.personalization.learning_style || "Visual",
            icon:
                state.personalization.learning_style === "visual"
                    ? Eye
                    : state.personalization.learning_style === "auditory"
                      ? Brain
                      : Zap,
            variant: "primary",
        },
        {
            title: "Level Saat Ini",
            value: state.personalization.current_level || "Pemula",
            icon: Trophy,
            variant: "primary",
            footer: `${state.personalization.global_xp || 0} XP`,
        },
        {
            title: "Akurasi",
            value: `${state.personalization.accuracy || 0}%`,
            icon: Target,
            variant: "success",
            footer: `${state.personalization.correct_count || 0}/${state.personalization.total_questions_answered || 0} Benar`,
        },
        {
            title: "Streak",
            value: `${state.personalization.current_streak || 0} 🔥`,
            icon: Flame,
            variant: "warning",
            footer: `Max: ${state.personalization.max_streak || 0}`,
        },
    ];

    $: detailedStats = [
        {
            title: "Total Soal Dijawab",
            value: state.personalization.total_questions_answered || 0,
            icon: CheckCircle,
            variant: "primary",
        },
        {
            title: "Jawaban Benar",
            value: state.personalization.correct_count || 0,
            icon: CheckCircle,
            variant: "success",
        },
        {
            title: "Jawaban Salah",
            value: state.personalization.wrong_count || 0,
            icon: XCircle,
            variant: "danger",
        },
        {
            title: "Hints Digunakan",
            value: state.personalization.hints_used_count || 0,
            icon: Lightbulb,
            variant: "warning",
            footer: `${state.personalization.hints_available || 3} Tersisa`,
        },
        {
            title: "Status Fast Track",
            value: state.personalization.fast_track_active
                ? "Aktif"
                : "Tidak Aktif",
            icon: Zap,
            variant: "warning",
        },
    ];
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
        <DarkHeroPanel
            class="p-8 md:p-12 mb-8 shadow-2xl transition-all duration-500 hover:shadow-primary-900/20"
        >
            <div class="flex flex-col md:flex-row items-center gap-10">
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
                        MEMBER SINCE {new Date(
                            state.user.created_at,
                        ).getFullYear()}
                    </p>
                    <h2
                        class="text-4xl md:text-5xl font-bold tracking-tight mb-4 text-white uppercase"
                    >
                        {state.user.name}
                    </h2>
                    <div
                        class="flex flex-wrap items-center justify-center md:justify-start gap-4"
                    >
                        <div
                            class="flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md rounded-xl border border-white/10 text-xs font-bold uppercase tracking-wider"
                        >
                            <Mail size={14} class="text-primary-400" />
                            {state.user.email}
                        </div>
                        <div
                            class="flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md rounded-xl border border-white/10 text-xs font-bold uppercase tracking-wider"
                        >
                            <UserIcon size={14} class="text-primary-400" /> Mahasiswa
                        </div>
                    </div>
                </div>
            </div>
        </DarkHeroPanel>

        <!-- Personalization Section -->
        <div class="space-y-8">
            <h3
                class="text-xl font-bold tracking-widest text-slate-900 uppercase"
            >
                Data Personalisasi Pembelajaran
            </h3>
            <div class="space-y-8">
                <StatsGrid stats={personalizationStats} />

                <div class="space-y-6 pt-6">
                    <h4
                        class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-4 uppercase tracking-widest"
                    >
                        Statistik Pembelajaran Detail
                    </h4>
                    <StatsGrid
                        stats={detailedStats}
                        gridClass="grid-cols-1 md:grid-cols-2 lg:grid-cols-3"
                    />
                </div>
            </div>
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
                            class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0"
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
                    class="p-8 rounded-[2rem] bg-slate-900 text-white relative overflow-hidden group"
                >
                    <div
                        class="absolute -top-10 -right-10 w-32 h-32 bg-primary-500/10 rounded-full blur-2xl transition-transform duration-1000"
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
                        <Alert variant="success" className="mb-10"
                            >{flash.success}</Alert
                        >
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
