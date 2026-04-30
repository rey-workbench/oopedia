<script lang="ts">
    import App from '@/layouts/App.svelte';
    import Panel from '@/components/ui/Panel.svelte';
    import Card from '@/components/ui/Card.svelte';
    import Input from '@/components/ui/Input.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Alert from '@/components/ui/Alert.svelte';
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
        BookOpen,
        Activity,
        Gauge,
    } from 'lucide-svelte';
    import { untrack } from 'svelte';
    import { ProfileState } from '@/states/Mahasiswa/ProfileState.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import CertificateCard from '@/components/layout/CertificateCard.svelte';
    import { ROUTES } from '@/utils/route';
    import EmptyState from '@/components/ui/EmptyState.svelte';

    import type { StudentProfile, Certification } from '@/types';

    const {
        personalization,
        certifications = [],
    }: { personalization: StudentProfile | null; certifications: Certification[] } = $props();

    const state = untrack(() => new ProfileState(personalization));

    const learningStyleIcon = $derived.by(() => {
        const style = state.personalization?.learning_style;
        if (style === 'deep') return Brain;
        if (style === 'motivated') return Zap;
        if (style === 'strategic') return BookOpen;
        if (style === 'balanced') return Target;
        return Eye; // unknown
    });

    const personalizationStats = $derived([
        {
            title: 'Profil Belajar',
            value: state.personalization?.learning_profile_label || 'Belum Diisi',
            icon: learningStyleIcon,
            variant: state.personalization?.mslq_filled ? 'primary' : 'warning',
            footer: state.personalization?.mslq_filled ? 'Berdasarkan MSLQ' : 'Isi Kuesioner MSLQ',
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
            value: `${state.personalization?.accuracy || 0} %`,
            icon: Target,
            variant: 'success',
            footer: `${state.personalization?.correct_count || 0}/${state.personalization?.total_questions_answered || 0} Benar`,
        },
        {
            title: 'Streak',
            value: state.personalization?.current_streak || 0,
            icon: Flame,
            variant: 'warning',
            footer: `Max: ${state.personalization?.max_streak || 0} Hari`,
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
            footer: `${state.personalization?.hints_available ?? 3} Tersisa`,
        },
        {
            title: 'Target Kesulitan',
            value: (() => {
                const d = state.personalization?.target_difficulty;
                if (d === 'hard') return 'Tinggi';
                if (d === 'medium') return 'Menengah';
                return 'Dasar';
            })(),
            icon: Gauge,
            variant: 'primary',
            footer: 'Ditetapkan Mesin Adaptif',
        },
        {
            title: 'Status Diagnosis',
            value: state.personalization?.last_diagnosis ?? 'Belum Ada',
            icon: Activity,
            variant: state.personalization?.needs_remedial ? 'danger' : 'success',
            footer: state.personalization?.needs_remedial ? 'Perlu Remedial' : 'Normal',
        },
    ]);
</script>

<App title="Profil Mahasiswa">
    <div class="space-y-12">
        <PageHeader
            id="page-header"
            title="Profil Saya"
            subtitle="Atur informasi akun dan keamanan Anda untuk pengalaman belajar yang lebih personal."
        ></PageHeader>

        <!-- Profile Hero Card -->
        <div id="profile-hero">
            <Panel rounded="full" class="mb-8 shadow-xl" padding="p-8 md:p-12">
                <div class="flex flex-col items-center gap-10 md:flex-row">
                    <div class="group relative">
                        <div
                            class="flex h-32 w-32 items-center justify-center overflow-hidden rounded-3xl bg-white shadow-xl"
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
                            MEMBER SINCE {state.user ? new Date(state.user.created_at).getFullYear() : 'N/A'}
                        </p>
                        <h2
                            class="mb-4 text-4xl font-bold tracking-tight text-white uppercase md:text-5xl"
                        >
                            {state.user?.name ?? 'GUEST'}
                        </h2>
                        <div
                            class="flex flex-wrap items-center justify-center gap-4 md:justify-start"
                        >
                            <div
                                class="flex items-center gap-2 rounded-xl border-2 border-b-4 border-white/10 bg-white/10 px-4 py-2 text-xs font-bold tracking-wider uppercase shadow-sm backdrop-blur-md"
                            >
                                <Mail size={14} class="text-primary-400" />
                                {state.user?.email ?? 'guest@oopedia'}
                            </div>
                            <div
                                class="flex items-center gap-2 rounded-xl border-2 border-b-4 border-white/10 bg-white/10 px-4 py-2 text-xs font-bold tracking-wider uppercase shadow-sm backdrop-blur-md"
                            >
                                <UserIcon size={14} class="text-primary-400" /> Mahasiswa
                            </div>
                        </div>
                    </div>
                </div></Panel
            >
        </div>

        <!-- Personalization Section -->
        <div class="space-y-8">
            <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
                Data Personalisasi Pembelajaran
            </h3>
            <div id="profile-stats" class="space-y-8">
                <div
                    id="learning-profile-analysis"
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4"
                >
                    {#each personalizationStats as stat}
                        <Card hover={true} class="group relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-4 text-slate-400 opacity-10">
                                {#if typeof stat.icon !== 'string'}
                                    {@const IconComponent = stat.icon}
                                    <div class="scale-[4] opacity-50">
                                        <IconComponent size={24} strokeWidth={2.5} />
                                    </div>
                                {/if}
                            </div>

                            <div class="relative z-10">
                                <div
                                    class="glass mb-6 flex h-14 w-14 items-center justify-center rounded-2xl shadow-sm
                                    {stat.variant === 'success'
                                        ? 'bg-emerald-100 text-emerald-600'
                                        : stat.variant === 'warning'
                                          ? 'bg-amber-100 text-amber-600'
                                          : stat.variant === 'danger'
                                            ? 'bg-rose-100 text-rose-600'
                                            : 'bg-primary-100 text-primary-600'}"
                                >
                                    {#if typeof stat.icon === 'string'}
                                        <i class={stat.icon}></i>
                                    {:else}
                                        {@const IconComponent = stat.icon}
                                        <IconComponent size={24} strokeWidth={2.5} />
                                    {/if}
                                </div>

                                <h3
                                    class="mb-2 text-[10px] font-bold tracking-wider text-slate-600 uppercase"
                                >
                                    {stat.title}
                                </h3>
                                <div
                                    class="font-display mb-2 flex items-center gap-3 text-4xl font-black tracking-tight text-slate-900"
                                >
                                    {stat.value}
                                    {#if stat.title === 'Streak'}
                                        <div class="animate-pulse text-amber-500">
                                            <Flame
                                                size={32}
                                                strokeWidth={2.5}
                                                class="fill-amber-500/20"
                                            />
                                        </div>
                                    {/if}
                                </div>

                                {#if stat.footer}
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="h-1.5 w-1.5 rounded-full {stat.variant ===
                                            'success'
                                                ? 'bg-emerald-500'
                                                : stat.variant === 'warning'
                                                  ? 'bg-amber-500'
                                                  : stat.variant === 'danger'
                                                    ? 'bg-rose-500'
                                                    : 'bg-primary-500'}"
                                        ></div>
                                        <p
                                            class="text-[10px] font-bold tracking-widest text-slate-500 uppercase"
                                        >
                                            {stat.footer}
                                        </p>
                                    </div>
                                {/if}
                            </div>
                        </Card>
                    {/each}
                </div>

                <div class="space-y-6 pt-6">
                    <h4
                        class="border-b border-slate-100 pb-4 text-sm font-bold tracking-widest text-slate-900 uppercase"
                    >
                        Statistik Pembelajaran Detail
                    </h4>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        {#each detailedStats as stat}
                            <Card hover={true} class="group relative overflow-hidden">
                                <div class="absolute top-0 right-0 p-4 text-slate-400 opacity-10">
                                    {#if typeof stat.icon !== 'string'}
                                        {@const IconComponent = stat.icon}
                                        <div class="scale-[4] opacity-50">
                                            <IconComponent size={24} strokeWidth={2.5} />
                                        </div>
                                    {/if}
                                </div>

                                <div class="relative z-10">
                                    <div
                                        class="glass mb-6 flex h-14 w-14 items-center justify-center rounded-2xl shadow-sm
                                        {stat.variant === 'success'
                                            ? 'bg-emerald-100 text-emerald-600'
                                            : stat.variant === 'warning'
                                              ? 'bg-amber-100 text-amber-600'
                                              : stat.variant === 'danger'
                                                ? 'bg-rose-100 text-rose-600'
                                                : 'bg-primary-100 text-primary-600'}"
                                    >
                                        {#if typeof stat.icon === 'string'}
                                            <i class={stat.icon}></i>
                                        {:else}
                                            {@const IconComponent = stat.icon}
                                            <IconComponent size={24} strokeWidth={2.5} />
                                        {/if}
                                    </div>

                                    <h3
                                        class="mb-2 text-[10px] font-bold tracking-wider text-slate-600 uppercase"
                                    >
                                        {stat.title}
                                    </h3>
                                    <div
                                        class="font-display mb-2 text-4xl font-black tracking-tight text-slate-900"
                                    >
                                        {stat.value}
                                    </div>

                                    {#if stat.footer}
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="h-1.5 w-1.5 rounded-full {stat.variant ===
                                                'success'
                                                    ? 'bg-emerald-500'
                                                    : stat.variant === 'warning'
                                                      ? 'bg-amber-500'
                                                      : stat.variant === 'danger'
                                                        ? 'bg-rose-500'
                                                        : 'bg-primary-500'}"
                                            ></div>
                                            <p
                                                class="text-[10px] font-bold tracking-widest text-slate-500 uppercase"
                                            >
                                                {stat.footer}
                                            </p>
                                        </div>
                                    {/if}
                                </div>
                            </Card>
                        {/each}
                    </div>
                </div>
            </div>
        </div>

        <!-- Certificate Section -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
                    Sertifikat Saya
                </h3>
                {#if certifications.length > 0}
                    <a
                        href={ROUTES.MAHASISWA.CERTIFICATES.INDEX}
                        class="text-primary-600 text-[10px] font-black tracking-widest uppercase hover:underline"
                        >Lihat &amp; Unduh Semua →</a
                    >
                {/if}
            </div>

            <div id="profile-certificates">
                {#if certifications.length > 0}
                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                        {#each certifications.slice(0, 2) as cert (cert.material_id)}
                            <CertificateCard
                                materialTitle={cert.material_title}
                                type={cert.type as 'gold' | 'silver' | 'bronze'}
                                issuedAt={cert.issued_at ?? undefined}
                                id={cert.material_id}
                            />
                        {/each}
                    </div>
                {:else}
                    <EmptyState
                        title="Belum Ada Sertifikat"
                        description="Selesaikan materi dan kuis untuk mulai mengumpulkan penghargaan sertifikat."
                        icon={Trophy}
                    />
                {/if}
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

                <Panel padding="p-8" class="group">
                    <h4 class="mb-4 text-lg font-bold tracking-widest uppercase">Butuh Bantuan?</h4>
                    <p class="mb-6 text-xs leading-relaxed font-medium text-slate-400">
                        Jika Anda mengalami kendala pada akun, silakan hubungi tim administrator
                        Oopedia.
                    </p>
                    <Button
                        id="btn-contact-admin"
                        variant="secondary"
                        size="sm"
                        class="w-full font-bold tracking-widest uppercase">Hubungi Admin</Button
                    >
                </Panel>
            </div>

            <!-- Main Form -->
            <div class="space-y-8 lg:col-span-2">
                <h3 class="text-xl font-bold tracking-widest text-slate-900 uppercase">
                    Pengaturan Profil
                </h3>

                <div id="profile-settings">
                    <Card padding="p-8 md:p-12" id="profile-personal-info">
                        {#if state.flash?.success}
                            <Alert variant="success" class="mb-10"
                                >{(state.flash as any).success}</Alert
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
                                        inputClass="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
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
                                        inputClass="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
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
                                            inputClass="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
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
                                            inputClass="rounded-2xl border-slate-100 bg-slate-50/50 py-4 font-bold focus:bg-white"
                                            placeholder="••••••••"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-10">
                                <Button
                                    id="btn-save-profile"
                                    type="submit"
                                    variant="primary"
                                    size="xl"
                                    class="group w-full px-12 md:w-auto"
                                    disabled={state.form.processing}
                                >
                                    {#if state.form.processing}
                                        <Loader2 size={18} class="mr-2 animate-spin" /> Menyimpan...
                                    {:else}
                                        <Save size={18} class="mr-2" /> Simpan Perubahan
                                    {/if}
                                </Button>
                            </div>
                        </form>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</App>
