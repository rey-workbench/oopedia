<script lang="ts">
    import App from '@/layouts/App.svelte';
    import { router } from '@inertiajs/svelte';
    import { UserRound, GraduationCap, ArrowLeft } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';

    interface Props {
        googleUser: {
            name: string;
            email: string;
            avatar: string;
        };
    }

    const { googleUser }: Props = $props();

    let processing = $state(false);

    function selectRole(role: 'mahasiswa' | 'dosen') {
        processing = true;
        router.post(
            ROUTES.AUTH.GOOGLE_REGISTER(role),
            {},
            {
                onFinish: () => (processing = false),
            }
        );
    }
</script>

<App variant="auth" title="Pilih Peran - OOPedia">
    <div
        class="relative flex min-h-screen flex-col bg-slate-50 px-4 py-4 font-sans text-slate-900 antialiased"
    >
        <!-- Header -->
        <div class="mx-auto flex w-full max-w-4xl items-center justify-between p-2">
            <button
                onclick={() => history.back()}
                class="p-2 text-slate-400 transition hover:text-slate-600"
                aria-label="Kembali"
            >
                <ArrowLeft size={32} strokeWidth={2.5} />
            </button>
        </div>

        <!-- Main Content -->
        <div
            class="mx-auto -mt-12 flex w-full max-w-lg flex-1 flex-col items-center justify-center sm:-mt-20"
        >
            <div class="mb-8 flex flex-col items-center text-center">
                {#if googleUser.avatar}
                    <img
                        src={googleUser.avatar}
                        alt={googleUser.name}
                        class="mb-4 h-20 w-20 rounded-full border-4 border-white shadow-lg"
                    />
                {/if}
                <h1 class="text-3xl font-black tracking-tight">
                    Selamat Datang, {googleUser.name}!
                </h1>
                <p class="mt-2 text-lg font-medium text-balance text-slate-500">
                    Satu langkah lagi. Apa peran Anda di platform ini?
                </p>
            </div>

            <div class="grid w-full grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Mahasiswa Card -->
                <button
                    onclick={() => selectRole('mahasiswa')}
                    disabled={processing}
                    class="group hover:border-primary-400 hover:bg-primary-50 relative flex flex-col items-center justify-center overflow-hidden rounded-3xl border-2 border-b-8 border-slate-200 bg-white p-8 transition-all active:translate-y-1 active:border-b-4 disabled:opacity-50"
                >
                    <div
                        class="bg-primary-100 text-primary-600 group-hover:bg-primary-200 mb-4 flex h-20 w-20 items-center justify-center rounded-2xl transition-colors"
                    >
                        <GraduationCap size={48} strokeWidth={2.5} />
                    </div>
                    <h3
                        class="group-hover:text-primary-700 text-xl font-black text-slate-700 transition-colors"
                    >
                        Mahasiswa
                    </h3>
                    <p
                        class="group-hover:text-primary-500 mt-2 text-center text-sm font-bold text-slate-400 transition-colors"
                    >
                        Belajar dengan cara yang adaptif dan seru.
                    </p>
                    {#if processing}
                        <div
                            class="absolute inset-0 flex items-center justify-center bg-white/50 backdrop-blur-sm"
                        >
                            <div
                                class="border-primary-500 h-8 w-8 animate-spin rounded-full border-4 border-t-transparent"
                            ></div>
                        </div>
                    {/if}
                </button>

                <!-- Dosen Card -->
                <button
                    onclick={() => selectRole('dosen')}
                    disabled={processing}
                    class="group hover:border-secondary-400 hover:bg-secondary-50 relative flex flex-col items-center justify-center overflow-hidden rounded-3xl border-2 border-b-8 border-slate-200 bg-white p-8 transition-all active:translate-y-1 active:border-b-4 disabled:opacity-50"
                >
                    <div
                        class="bg-secondary-100 text-secondary-600 group-hover:bg-secondary-200 mb-4 flex h-20 w-20 items-center justify-center rounded-2xl transition-colors"
                    >
                        <UserRound size={48} strokeWidth={2.5} />
                    </div>
                    <h3
                        class="group-hover:text-secondary-700 text-xl font-black text-slate-700 transition-colors"
                    >
                        Dosen
                    </h3>
                    <p
                        class="group-hover:text-secondary-500 mt-2 text-center text-sm font-bold text-slate-400 transition-colors"
                    >
                        Kelola materi dan pantau perkembangan mahasiswa.
                    </p>
                    {#if processing}
                        <div
                            class="absolute inset-0 flex items-center justify-center bg-white/50 backdrop-blur-sm"
                        >
                            <div
                                class="border-secondary-500 h-8 w-8 animate-spin rounded-full border-4 border-t-transparent"
                            ></div>
                        </div>
                    {/if}
                </button>
            </div>

            <div class="mt-8 text-center text-sm font-bold text-slate-400">
                Email Anda: <span class="text-slate-600">{googleUser.email}</span>
            </div>
        </div>
    </div>
</App>
