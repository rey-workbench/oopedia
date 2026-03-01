<script lang="ts">
    import App from '@/layouts/App.svelte';
    import DarkHeroPanel from '@/components/shared/DarkHeroPanel.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Input from '@/components/ui/Input.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Alert from '@/components/ui/Alert.svelte';
    import StatsGrid from '@/components/shared/StatsGrid.svelte';
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
    } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { ProfileState } from '@/states/Mahasiswa/ProfileState.svelte';
    import PageHeader from '@/components/shared/PageHeader.svelte';

    import type { StudentProfile } from '@/types';

    const { personalization }: { personalization: StudentProfile | null } = $props();

    const state = untrack(() => new ProfileState(personalization));

    const personalizationStats = $derived([
        {
            title: 'Gaya Belajar',
            value: state.personalization?.learning_style || 'Visual',
            icon:
                state.personalization?.learning_style === 'visual'
                    ? Eye
                    : state.personalization?.learning_style === 'auditory'
                      ? Brain
                      : Zap,
            variant: 'primary',
        },
        {
            title: 'Level Saat Ini',
            value: state.personalization?.current_level || 'Pemula',
            icon: Trophy,
            variant: 'primary',
            footer: `${state.personalization?.global_xp || 0} XP`,
        },
        {
            title: 'Akurasi',
            value: `${state.personalization?.accuracy || 0}%`,
            icon: Target,
            variant: 'success',
            footer: `${state.personalization?.correct_count || 0}/${state.personalization?.total_questions_answered || 0} Benar`,
        },
        {
            title: 'Streak',
            value: `${state.personalization?.current_streak || 0} 🔥`,
            icon: Flame,
            variant: 'warning',
            footer: `Max: ${state.personalization?.max_streak || 0}`,
        },
    ]);

    const detailedStats = $derived([
        {
            title: 'Total Soal Dijawab',
            value: state.personalization?.total_questions_answered || 0,
            icon: CheckCircle,
            variant: 'primary',
        },
        {
            title: 'Jawaban Benar',
            value: state.personalization?.correct_count || 0,
            icon: CheckCircle,
            variant: 'success',
        },
        {
            title: 'Jawaban Salah',
            value: state.personalization?.wrong_count || 0,
            icon: XCircle,
            variant: 'danger',
        },
        {
            title: 'Hints Digunakan',
            value: state.personalization?.hints_used_count || 0,
            icon: Lightbulb,
            variant: 'warning',
            footer: `${state.personalization?.hints_available || 3} Tersisa`,
        },
        {
            title: 'Status Fast Track',
            value: state.personalization?.fast_track_active ? 'Aktif' : 'Tidak Aktif',
            icon: Zap,
            variant: 'warning',
        },
    ]);
</script>

<App title="Profil Mahasiswa">
    <div class="space-y-12">
        <PageHeader
            title="Profil Saya"
            subtitle="Atur informasi akun dan keamanan Anda untuk pengalaman belajar yang lebih personal."
        >
            <div class="mt-6 flex flex-wrap gap-4">
                <div>
                    <div
                        class="flex items-center gap-2 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-2 text-emerald-600"
                    >
                        <ShieldCheck size={16} />
                        <span class="text-[10px] font-bold tracking-widest uppercase"
                            >Akun Terverifikasi</span
                        >
                    </div>
                </div>
            </div>
        </PageHeader>

        <!-- Profile Hero Card -->
        <DarkHeroPanel
            class="hover:shadow-primary-900/20 mb-8 p-8 shadow-2xl transition-all duration-500 md:p-12"
        >
            <div class="flex flex-col items-center gap-10 md:flex-row">
                <div class="group relative">
                    <div
                        class="flex h-32 w-32 rotate-3 items-center justify-center overflow-hidden rounded-[2.5rem] bg-white shadow-2xl transition-transform duration-500 group-hover:rotate-0"
                    >
                        <UserCircle size={80} class="text-slate-200" />
                    </div>
                    <div
                        class="absolute -right-2 -bottom-2 flex h-10 w-10 items-center justify-center rounded-2xl border-4 border-slate-900 bg-emerald-500 text-white shadow-xl"
                    >
                        <Check size={18} />
                    </div>
                </div>

                <div class="text-center md:text-left">
                    <p
                        class="text-primary-400 mb-2 text-[10px] font-bold tracking-widest uppercase"
                    >
                        MEMBER SINCE {new Date(state.user.created_at).getFullYear()}
                    </p>
                    <h2
                        class="mb-4 text-4xl font-bold tracking-tight text-white uppercase md:text-5xl"
                    >
                        {state.user.name}
                    </h2>
                    <div class="flex flex-wrap items-center justify-center gap-4 md:justify-start">
                        <div
                            class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/10 px-4 py-2 text-xs font-bold tracking-wider uppercase backdrop-blur-md"
                        >
                            <Mail size={14} class="text-primary-400" />
                            {state.user.email}
                        </div>
                        <div
                            class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/10 px-4 py-2 text-xs font-bold tracking-wider uppercase backdrop-blur-md"
                        >
                            <UserIcon size={14} class="text-primary-400" /> Mahasiswa
                        </div>
                    </div>
                </div>
            </div>
        </DarkHeroPanel>

        <!-- Personalization Section -->
        <div class="space-y-8">
            <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
                Data Personalisasi Pembelajaran
            </h3>
            <div class="space-y-8">
                <StatsGrid stats={personalizationStats} />

                <div class="space-y-6 pt-6">
                    <h4
                        class="border-b border-slate-100 pb-4 text-sm font-bold tracking-widest text-slate-900 uppercase"
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

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
            <!-- Sidebar Info -->
            <div class="space-y-8">
                <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
                    Status Akun
                </h3>

                <Card padding="p-8" class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="bg-primary-50 text-primary-600 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                        >
                            <ShieldCheck size={20} />
                        </div>
                        <div>
                            <h4 class="text-sm font-bold tracking-wider text-slate-900 uppercase">
                                Identitas
                            </h4>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                Informasi utama profil Anda yang terlihat oleh dosen dan sistem.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 border-t border-slate-50 pt-6">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600"
                        >
                            <Lock size={20} />
                        </div>
                        <div>
                            <h4 class="text-sm font-bold tracking-wider text-slate-900 uppercase">
                                Keamanan
                            </h4>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                Update password secara berkala untuk menjaga keamanan akun Anda.
                            </p>
                        </div>
                    </div>
                </Card>

                <div
                    class="group relative overflow-hidden rounded-[2rem] bg-slate-900 p-8 text-white"
                >
                    <div
                        class="bg-primary-500/10 absolute -top-10 -right-10 h-32 w-32 rounded-full blur-2xl transition-transform duration-1000"
                    ></div>
                    <h4 class="mb-4 text-lg font-bold tracking-widest uppercase">Butuh Bantuan?</h4>
                    <p class="mb-6 text-xs leading-relaxed font-medium text-slate-400">
                        Jika Anda mengalami kendala pada akun, silakan hubungi tim administrator
                        Oopedia.
                    </p>
                    <Button
                        variant="secondary"
                        size="sm"
                        class="w-full font-bold tracking-widest uppercase">Hubungi Admin</Button
                    >
                </div>
            </div>

            <!-- Main Form -->
            <div class="space-y-8 lg:col-span-2">
                <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
                    Pengaturan Profil
                </h3>

                <Card padding="p-8 md:p-12">
                    {#if state.flash?.success}
                        <Alert variant="success" class="mb-10">{(state.flash as any).success}</Alert
                        >
                    {/if}

                    <form
                        onsubmit={(e) => {
                            e.preventDefault();
                            state.submit();
                        }}
                        class="space-y-10"
                    >
                        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                            <div class="space-y-3">
                                <label
                                    for="name"
                                    class="block px-1 text-xs font-bold tracking-widest text-slate-400 uppercase"
                                    >Nama Lengkap</label
                                >
                                <Input
                                    id="name"
                                    type="text"
                                    bind:value={state.form.name}
                                    className="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
                                    placeholder="Masukkan nama lengkap"
                                    error={state.form.errors['name']}
                                />
                            </div>
                            <div class="space-y-3">
                                <label
                                    for="email"
                                    class="block px-1 text-xs font-bold tracking-widest text-slate-400 uppercase"
                                    >Alamat Email</label
                                >
                                <Input
                                    id="email"
                                    type="email"
                                    bind:value={state.form.email}
                                    className="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
                                    placeholder="email@contoh.com"
                                    error={state.form.errors['email']}
                                />
                            </div>
                        </div>

                        <div class="border-t border-slate-100 pt-10">
                            <h4
                                class="mb-8 text-sm font-bold tracking-widest text-slate-900 uppercase"
                            >
                                Update Password
                            </h4>
                            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                                <div class="space-y-3">
                                    <label
                                        for="password"
                                        class="block px-1 text-xs font-bold tracking-widest text-slate-400 uppercase"
                                        >Password Baru</label
                                    >
                                    <Input
                                        id="password"
                                        type="password"
                                        bind:value={state.form.password}
                                        className="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
                                        placeholder="••••••••"
                                        error={state.form.errors['password']}
                                    />
                                    <p class="px-1 text-[10px] font-bold text-slate-400 italic">
                                        * Kosongkan jika tidak ingin mengubah password
                                    </p>
                                </div>
                                <div class="space-y-3">
                                    <label
                                        for="password_confirmation"
                                        class="block px-1 text-xs font-bold tracking-widest text-slate-400 uppercase"
                                        >Konfirmasi Password</label
                                    >
                                    <Input
                                        id="password_confirmation"
                                        type="password"
                                        bind:value={state.form.password_confirmation}
                                        className="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
                                        placeholder="••••••••"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-10">
                            <Button
                                type="submit"
                                variant="primary"
                                size="xl"
                                class="group w-full px-12 md:w-auto"
                                disabled={state.form.processing}
                            >
                                {#if state.form.processing}
                                    <Loader2 size={18} class="mr-2 animate-spin" /> Menyimpan...
                                {:else}
                                    <Save
                                        size={18}
                                        class="mr-2 transition-transform group-hover:scale-110"
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
