<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import { fade, fly } from 'svelte/transition';
    import Input from '@/components/ui/Input.svelte';
    import Textarea from '@/components/ui/Textarea.svelte';
    import Select from '@/components/ui/Select.svelte';
    import Button from '@/components/ui/Button.svelte';
    import Alert from '@/components/ui/Alert.svelte';
    import { Save, X, Target, Code, Info } from 'lucide-svelte';
    import { ROUTES } from '@/utils/route';

    let {
        show = false,
        action = null,
        onclose,
    } = $props<{
        show: boolean;
        action: any | null;
        onclose: () => void;
    }>();

    const isEdit = $derived(!!action);

    let form = useForm({
        id: '',
        name: '',
        description: '',
        variant: 'result',
        instructions: {} as any,
    });

    let jsonString = $state('{}');
    let jsonError = $state<string | null>(null);

    $effect(() => {
        if (show) {
            if (action) {
                form.id = action.id;
                form.name = action.name;
                form.description = action.description;
                form.variant = action.variant || 'result';
                form.instructions = action.instructions || {};
                jsonString = JSON.stringify(action.instructions || {}, null, 4);
            } else {
                form.reset();
                jsonString = JSON.stringify(
                    {
                        flow: 'NEXT',
                        title: 'Bagus!',
                        message: 'Silakan lanjut ke soal berikutnya.',
                    },
                    null,
                    4
                );
            }
            jsonError = null;
        }
    });

    const variants = [
        { value: 'silent', label: 'Silent (Hanya Deduksi)' },
        { value: 'result', label: 'Result (Feedback Standar)' },
        { value: 'acceleration', label: 'Acceleration (Lompatan)' },
        { value: 'gamification', label: 'Gamification (Badge/XP)' },
        { value: 'warning', label: 'Warning (Peringatan)' },
        { value: 'danger', label: 'Danger (Intervensi)' },
        { value: 'info', label: 'Info (Tips)' },
    ];

    function validateJson() {
        try {
            form.instructions = JSON.parse(jsonString);
            jsonError = null;
            return true;
        } catch (e: any) {
            jsonError = `JSON Tidak Valid: ${e.message}`;
            return false;
        }
    }

    function handleSubmit(e: Event) {
        e.preventDefault();

        if (!validateJson()) return;

        if (isEdit) {
            form.put(`${ROUTES.ADMIN.ADAPTIVE_ACTIONS.UPDATE(action.id)}`, {
                onSuccess: () => onclose(),
            });
        } else {
            form.post(ROUTES.ADMIN.ADAPTIVE_ACTIONS.STORE, {
                onSuccess: () => onclose(),
            });
        }
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
</style>

{#if show}
    <!-- Overlay -->
    <!-- svelte-ignore a11y_click_events_have_key_events -->
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div
        class="fixed inset-0 z-200 bg-slate-900/40 backdrop-blur-sm"
        transition:fade={{ duration: 200 }}
        onclick={onclose}
    ></div>

    <!-- n8n-style Side Panel -->
    <div
        class="fixed inset-y-0 right-0 z-210 flex w-full max-w-xl flex-col border-l border-slate-200 bg-white shadow-2xl"
        transition:fly={{ x: 500, duration: 300 }}
    >
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="bg-emerald-50 text-emerald-600 rounded-xl p-2">
                    <Target size={20} />
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-800">
                        {isEdit ? `Node Editor: Aksi [${action.id}]` : 'Node Editor: Aksi Baru'}
                    </h3>
                    <p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">
                        Define outcomes & rewards
                    </p>
                </div>
            </div>
            <button
                onclick={onclose}
                class="rounded-full p-2 text-slate-400 transition-colors hover:bg-slate-50 hover:text-slate-600"
                aria-label="Close"
            >
                <X size={20} />
            </button>
        </div>

        <!-- Content -->
        <div class="custom-scrollbar flex-1 overflow-y-auto bg-slate-50/50 p-6">
            <form id="action-form" onsubmit={handleSubmit} class="space-y-8">
                <!-- 1. Basic Info Section -->
                <section class="space-y-4">
                    <div
                        class="flex items-center gap-2 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                    >
                        <div class="h-px flex-1 bg-slate-200"></div>
                        <span>1. Node Identity</span>
                        <div class="h-px w-4 bg-slate-200"></div>
                    </div>

                    <div class="space-y-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <Input
                                label="Kode Aksi (H-Code)"
                                placeholder="Misal: H10"
                                required
                                disabled={isEdit}
                                bind:value={form.id}
                                error={form.errors.id}
                                className="font-mono font-bold tracking-widest"
                            />
                            <Input
                                label="Nama Aksi"
                                placeholder="Misal: Intervensi Krisis"
                                required
                                bind:value={form.name}
                                error={form.errors.name}
                            />
                        </div>

                        <Select
                            label="Varian Tampilan"
                            options={variants}
                            required
                            bind:value={form.variant}
                            error={form.errors.variant}
                        />

                        <Textarea
                            label="Deskripsi Strategi"
                            placeholder="Jelaskan kegunaan aksi ini..."
                            required
                            bind:value={form.description}
                            error={form.errors.description}
                        />
                    </div>
                </section>

                <div class="flex justify-center">
                    <div class="h-8 w-px bg-slate-200"></div>
                </div>

                <!-- 2. Logic Instructions Section -->
                <section class="space-y-4 pb-12">
                    <div
                        class="flex items-center gap-2 text-[10px] font-black tracking-widest text-emerald-500 uppercase"
                    >
                        <div class="h-px flex-1 bg-emerald-100"></div>
                        <span>2. Execution Logic</span>
                        <div class="h-px w-4 bg-emerald-100"></div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label
                                class="flex items-center gap-2 text-[11px] font-black tracking-widest text-slate-500 uppercase"
                            >
                                <Code size={14} class="text-emerald-500" />
                                Instruksi JSON (Flow & Rewards)
                            </label>
                            <div class="relative">
                                <textarea
                                    bind:value={jsonString}
                                    oninput={() => (jsonError = null)}
                                    class="focus:border-emerald-500 min-h-[300px] w-full rounded-2xl border-2 border-slate-100 bg-slate-900 p-5 font-mono text-xs leading-relaxed text-emerald-400 shadow-xl focus:ring-0"
                                    spellcheck="false"
                                ></textarea>
                                {#if jsonError}
                                    <div class="mt-2 text-[10px] font-bold text-rose-500">
                                        {jsonError}
                                    </div>
                                {/if}
                            </div>
                        </div>

                        <Alert variant="info" class="border-slate-200 bg-white shadow-sm">
                            <div class="flex gap-4">
                                <Info size={16} class="mt-0.5 shrink-0 text-slate-600" />
                                <div class="space-y-1 text-[11px] font-bold text-slate-600">
                                    <p>Gunakan format JSON yang benar. Kunci yang didukung:</p>
                                    <ul class="list-disc space-y-1 pl-4 opacity-80">
                                        <li><code class="text-emerald-600">flow</code>: NEXT, UP, DOWN, REVIEW, FINISH</li>
                                        <li><code class="text-emerald-600">title</code>: Judul modal feedback</li>
                                        <li><code class="text-emerald-600">message</code>: Pesan untuk mahasiswa</li>
                                        <li><code class="text-emerald-600">xp</code>, <code class="text-emerald-600">streak</code>, <code class="text-emerald-600">target_difficulty</code></li>
                                    </ul>
                                </div>
                            </div>
                        </Alert>
                    </div>
                </section>
            </form>
        </div>

        <!-- Footer -->
        <div
            class="flex items-center justify-between border-t border-slate-100 bg-white px-6 py-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]"
        >
            <button
                type="button"
                onclick={onclose}
                class="text-xs font-black tracking-widest text-slate-400 uppercase hover:text-slate-600">Batal</button
            >
            <Button
                type="submit"
                form="action-form"
                variant="primary"
                icon={Save}
                disabled={form.processing}
                class="px-8 shadow-emerald-900/10 shadow-lg"
            >
                {form.processing ? 'Menyimpan...' : 'Simpan Aksi'}
            </Button>
        </div>
    </div>
{/if}
