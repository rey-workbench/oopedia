<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import Modal from '@/components/ui/Modal.svelte';
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
        code: '',
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
                form.code = action.code;
                form.name = action.name;
                form.description = action.description;
                form.variant = action.variant || 'result';
                form.instructions = action.instructions || {};
                jsonString = JSON.stringify(action.instructions || {}, null, 4);
            } else {
                form.reset();
                jsonString = JSON.stringify({
                    flow: 'NEXT',
                    title: 'Bagus!',
                    message: 'Silakan lanjut ke soal berikutnya.',
                }, null, 4);
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

<Modal {show} {onclose} maxWidth="2xl">
    <div class="flex max-h-[90vh] flex-col bg-white">
        <div class="z-10 flex shrink-0 items-center justify-between border-b border-slate-100 bg-white/80 px-6 py-4 backdrop-blur">
            <h3 class="flex items-center gap-2 text-lg font-black text-slate-800">
                <Target size={18} class="text-emerald-600" />
                {isEdit ? `Edit Aksi [${action.code}]` : 'Tambah Aksi Adaptif'}
            </h3>
            <button onclick={onclose} class="rounded-full p-2 text-slate-400 transition-colors hover:bg-slate-50 hover:text-slate-600">
                <X size={20} />
            </button>
        </div>

        <div class="custom-scrollbar flex-1 overflow-y-auto p-6">
            <form id="action-form" onsubmit={handleSubmit} class="space-y-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <Input
                        label="Kode Aksi (H-Code)"
                        placeholder="Misal: H10"
                        required
                        disabled={isEdit}
                        bind:value={form.code}
                        error={form.errors.code}
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
                    label="Varian UI"
                    options={variants}
                    required
                    bind:value={form.variant}
                    error={form.errors.variant}
                />

                <Textarea
                    label="Deskripsi"
                    placeholder="Jelaskan kegunaan aksi ini..."
                    required
                    bind:value={form.description}
                    error={form.errors.description}
                />

                <div class="space-y-2">
                    <label class="flex items-center gap-2 text-xs font-black tracking-widest text-slate-500 uppercase">
                        <Code size={14} class="text-primary-500" />
                        Instruksi JSON (Flow & Rewards)
                    </label>
                    <div class="relative">
                        <textarea
                            bind:value={jsonString}
                            oninput={() => jsonError = null}
                            class="min-h-[250px] w-full rounded-2xl border-2 border-slate-100 bg-slate-900 p-4 font-mono text-xs leading-relaxed text-emerald-400 focus:border-primary-500 focus:ring-0"
                            spellcheck="false"
                        ></textarea>
                        {#if jsonError}
                            <div class="mt-2 text-[10px] font-bold text-rose-500">
                                {jsonError}
                            </div>
                        {/if}
                    </div>
                </div>

                <Alert variant="info" class="bg-slate-50 border-slate-200">
                    <div class="flex gap-4">
                        <Info size={16} class="text-slate-600 mt-0.5 shrink-0" />
                        <div class="space-y-1 text-[11px] font-bold text-slate-600">
                            <p>Gunakan format JSON yang benar. Kunci yang didukung:</p>
                            <ul class="list-disc pl-4 space-y-0.5">
                                <li><code class="text-primary-600">flow</code>: NEXT, UP, DOWN, REVIEW, FINISH</li>
                                <li><code class="text-primary-600">title</code>: Judul modal feedback</li>
                                <li><code class="text-primary-600">message</code>: Pesan untuk mahasiswa</li>
                                <li><code class="text-primary-600">xp</code>, <code class="text-primary-600">streak</code>, <code class="text-primary-600">target_difficulty</code></li>
                            </ul>
                        </div>
                    </div>
                </Alert>
            </form>
        </div>

        <div class="flex shrink-0 items-center justify-between rounded-b-3xl border-t border-slate-100 bg-slate-50 px-6 py-4">
            <button type="button" onclick={onclose} class="text-sm font-bold text-slate-500 hover:text-slate-700">Batal</button>
            <Button type="submit" form="action-form" variant="primary" icon={Save} disabled={form.processing}>
                {form.processing ? 'Menyimpan...' : 'Simpan Aksi'}
            </Button>
        </div>
    </div>
</Modal>

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
