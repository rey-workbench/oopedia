<script lang="ts">
    import { fade, fly } from 'svelte/transition';
    import {
        X,
        Brain,
        GitBranch,
        Target,
        Pencil,
        Trophy,
        Info,
        Trash2,
        MessageSquareQuote,
    } from 'lucide-svelte';
    import { router } from '@inertiajs/svelte';
    import { ROUTES } from '@/utils/route';

    interface Props {
        selectedNode: any;
        factData: any[];
        actionData: any[];
        onclose: () => void;
        onedit?: ((rule: any) => void) | undefined;
        oneditaction?: ((action: any) => void) | undefined;
        resetD3Flow: () => void;
    }

    let { selectedNode, factData, actionData, onclose, onedit, oneditaction, resetD3Flow }: Props =
        $props();

    function handleClose() {
        onclose();
        resetD3Flow();
    }

    function handleDelete() {
        if (!confirm('Apakah Anda yakin ingin menghapus item ini?')) return;

        const url =
            selectedNode.type === 'gate'
                ? ROUTES.ADMIN.ADAPTIVE_RULES.DELETE(selectedNode.data.id)
                : ROUTES.ADMIN.ADAPTIVE_ACTIONS.DELETE(selectedNode.data.id);

        router.delete(url, {
            onSuccess: () => handleClose(),
        });
    }
</script>

{#if selectedNode}
    <button
        type="button"
        class="absolute inset-0 z-20 bg-slate-900/10 backdrop-blur-[2px]"
        onclick={handleClose}
        transition:fade={{ duration: 200 }}
        aria-label="Close details overlay"
    ></button>

    <div
        class="absolute inset-y-0 right-0 z-150 flex w-full max-w-sm flex-col border-l border-slate-200 bg-white shadow-2xl"
        transition:fly={{ x: 400, duration: 300 }}
    >
        <!-- Drawer Header -->
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
            <div class="flex items-center gap-3">
                <div
                    class="rounded-xl p-2 {selectedNode.type === 'gate'
                        ? 'bg-primary-50 text-primary-600'
                        : 'bg-emerald-50 text-emerald-600'}"
                >
                    {#if selectedNode.type === 'gate'}
                        <GitBranch size={20} />
                    {:else if selectedNode.type === 'action'}
                        <Target size={20} />
                    {:else}
                        <Brain size={20} />
                    {/if}
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-800">
                        {selectedNode.type === 'gate'
                            ? 'Detail Aturan'
                            : selectedNode.type === 'action'
                              ? 'Detail Aksi'
                              : 'Detail Fakta'}
                    </h3>
                    <p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">
                        Node Configuration
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                {#if selectedNode.type === 'gate' && onedit}
                    <button
                        onclick={() => onedit(selectedNode.data)}
                        class="text-primary-600 hover:bg-primary-50 rounded-xl p-2 transition-colors"
                        title="Ubah Aturan"
                    >
                        <Pencil size={18} />
                    </button>
                {:else if selectedNode.type === 'action' && oneditaction}
                    <button
                        onclick={() => oneditaction(selectedNode.data)}
                        class="rounded-xl p-2 text-emerald-600 transition-colors hover:bg-emerald-50"
                        title="Ubah Aksi"
                    >
                        <Pencil size={18} />
                    </button>
                {/if}

                <button
                    onclick={handleDelete}
                    class="rounded-xl p-2 text-rose-400 transition-colors hover:bg-rose-50 hover:text-rose-600"
                    title="Hapus"
                >
                    <Trash2 size={18} />
                </button>
                <button
                    onclick={handleClose}
                    class="rounded-xl p-2 text-slate-400 transition-colors hover:bg-slate-50 hover:text-slate-600"
                >
                    <X size={20} />
                </button>
            </div>
        </div>

        <!-- Drawer Body -->
        <div class="custom-scrollbar flex-1 space-y-10 overflow-y-auto p-8">
            {#if selectedNode.type === 'gate'}
                <section class="space-y-4">
                    <div
                        class="flex items-center gap-2 text-[10px] font-black tracking-widest text-blue-500 uppercase"
                    >
                        <div class="h-px flex-1 bg-blue-100"></div>
                        <span>Logic Flow</span>
                        <div class="h-px w-4 bg-blue-100"></div>
                    </div>

                    <div
                        class="rounded-2xl border border-slate-100 bg-slate-50 p-4 font-mono text-[11px] leading-relaxed"
                    >
                        <div class="mb-3">
                            <span class="font-bold text-blue-600">IF (Triggers)</span>
                            <div class="mt-2 flex flex-wrap gap-2">
                                {#each selectedNode.data.required_fact_ids || [] as req}
                                    <span
                                        class="rounded border border-slate-200 bg-white px-2 py-1 text-slate-700 shadow-sm"
                                    >
                                        {factData.find((f) => f.id === req)?.name || req}
                                    </span>
                                {/each}
                            </div>
                        </div>

                        {#if selectedNode.data.deduced_fact_ids?.length > 0}
                            <div class="mb-3">
                                <span class="font-bold text-purple-600">THEN (Diagnosa)</span>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    {#each selectedNode.data.deduced_fact_ids as ded}
                                        <span
                                            class="rounded border border-purple-100 bg-purple-50 px-2 py-1 text-purple-700 shadow-sm"
                                        >
                                            {factData.find((f) => f.id === ded)?.name || ded}
                                        </span>
                                    {/each}
                                </div>
                            </div>
                        {/if}

                        {#if selectedNode.data.actions?.length > 0}
                            <div>
                                <span class="font-bold text-emerald-600">THEN (Action)</span>
                                <div class="mt-2 space-y-2">
                                    {#each selectedNode.data.actions as ruleAction}
                                        {@const action = actionData.find(
                                            (a) => a.id === ruleAction.id
                                        )}
                                        <div
                                            class="rounded-xl border border-emerald-100 bg-emerald-50/50 p-3"
                                        >
                                            <p class="text-xs font-bold text-emerald-900">
                                                {action?.name || ruleAction.id}
                                            </p>
                                            <p
                                                class="mt-1 text-[10px] leading-relaxed text-emerald-700/80"
                                            >
                                                {action?.description || ''}
                                            </p>
                                        </div>
                                    {/each}
                                </div>
                            </div>
                        {/if}
                    </div>
                </section>

                <section class="space-y-4">
                    <div
                        class="flex items-center gap-2 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                    >
                        <div class="h-px flex-1 bg-slate-100"></div>
                        <span>Metadata</span>
                        <div class="h-px w-4 bg-slate-100"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-slate-100 bg-white p-3 shadow-sm">
                            <p class="mb-1 text-[9px] font-bold text-slate-400 uppercase">Kode</p>
                            <p class="font-mono text-xs font-black text-slate-900">
                                {selectedNode.data.id}
                            </p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-white p-3 shadow-sm">
                            <p class="mb-1 text-[9px] font-bold text-slate-400 uppercase">
                                Prioritas
                            </p>
                            <p class="text-xs font-black text-slate-900">
                                Level {selectedNode.data.priority}
                            </p>
                        </div>
                    </div>

                    {#if selectedNode.data.recommendation}
                        <div class="space-y-3">
                            <div
                                class="flex items-center gap-2 text-[10px] font-black tracking-widest text-amber-500 uppercase"
                            >
                                <MessageSquareQuote size={12} />
                                <span>Rekomendasi Pedagogis</span>
                            </div>
                            <div
                                class="rounded-2xl border-2 border-amber-100 bg-amber-50/50 p-4 shadow-sm"
                            >
                                <p class="text-[11px] leading-relaxed font-medium text-amber-900">
                                    {selectedNode.data.recommendation}
                                </p>
                            </div>
                        </div>
                    {/if}

                    {#if selectedNode.data.description}
                        <div class="rounded-2xl border border-blue-50 bg-blue-50/30 p-4">
                            <p class="text-[11px] leading-relaxed text-slate-600 italic">
                                "{selectedNode.data.description}"
                            </p>
                        </div>
                    {/if}
                </section>
            {:else}
                <div class="rounded-3xl border border-slate-100 bg-slate-50 p-6 text-center">
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm"
                    >
                        <Brain size={32} class="text-slate-400" />
                    </div>
                    <h4 class="mb-2 text-sm font-black text-slate-800">
                        {selectedNode.data.name}
                    </h4>
                    <p class="text-xs leading-relaxed text-slate-500 italic">
                        {selectedNode.data.logic || 'Tidak ada logika komputasi untuk node ini.'}
                    </p>
                    {#if selectedNode.data.variant}
                        <div class="mt-4 flex justify-center">
                            <span
                                class="rounded-full bg-slate-200 px-3 py-1 text-[10px] font-black tracking-widest text-slate-700 uppercase"
                            >
                                {selectedNode.data.variant}
                            </span>
                        </div>
                    {/if}
                </div>
            {/if}

            {#if selectedNode.type === 'gate' && selectedNode.data.actions && selectedNode.data.actions.length > 0}
                <section class="space-y-4 border-t border-slate-100 pt-6">
                    <div class="mt-6 space-y-4">
                        <div class="mb-2 flex items-center gap-2">
                            <Target size={14} class="text-amber-500" />
                            <span
                                class="text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                >System Actions & Parameters</span
                            >
                        </div>
                        <div class="grid gap-3">
                            {#each selectedNode.data.actions as action}
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="mb-2 flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-700"
                                            >{action.id}</span
                                        >
                                    </div>
                                    {#if Object.keys(action.metadata || {}).length > 0}
                                        <div class="space-y-1.5 border-t border-slate-200/60 pt-2">
                                            {#each Object.entries(action.metadata) as [key, val]}
                                                <div
                                                    class="flex items-center justify-between text-[10px]"
                                                >
                                                    <span class="font-medium text-slate-400"
                                                        >{key}</span
                                                    >
                                                    <span class="font-bold text-slate-600"
                                                        >{val}</span
                                                    >
                                                </div>
                                            {/each}
                                        </div>
                                    {:else}
                                        <span class="text-[9px] text-slate-300 italic"
                                            >No specific parameters</span
                                        >
                                    {/if}
                                </div>
                            {/each}
                        </div>
                    </div>
                </section>
            {/if}

            {#if selectedNode.type === 'gate' || selectedNode.type === 'action'}
                <!-- Preview Section -->
                <section class="space-y-4">
                    <div
                        class="flex items-center gap-2 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                    >
                        <div class="h-px flex-1 bg-slate-100"></div>
                        <span>Preview Tampilan</span>
                        <div class="h-px w-4 bg-slate-100"></div>
                    </div>
                    <div
                        class="overflow-hidden rounded-2xl border border-slate-100 bg-slate-50 p-4 shadow-inner"
                    >
                        {#if (selectedNode.type === 'gate' && selectedNode.data.actions?.length > 0) || selectedNode.type === 'action'}
                            {@const actionId =
                                selectedNode.type === 'gate'
                                    ? selectedNode.data.actions[0].id
                                    : selectedNode.data.id}

                            {#if actionId === 'INCREASE_DIFF'}
                                <div
                                    class="flex items-center gap-3 rounded-xl bg-blue-600 p-3 text-white shadow-lg"
                                >
                                    <div class="rounded-full bg-white/20 p-1.5">
                                        <Target size={14} />
                                    </div>
                                    <div class="leading-tight">
                                        <p class="text-xs font-bold">Level Up!</p>
                                        <p class="text-[10px] opacity-80">
                                            Tantangan baru tersedia untukmu.
                                        </p>
                                    </div>
                                </div>
                            {:else if actionId === 'CERTIFICATION' || actionId === 'H06'}
                                <div
                                    class="flex items-center gap-3 rounded-xl bg-amber-500 p-3 text-white shadow-lg"
                                >
                                    <div class="rounded-full bg-white/20 p-1.5">
                                        <Trophy size={14} />
                                    </div>
                                    <div class="leading-tight">
                                        <p class="text-xs font-bold">Selamat!</p>
                                        <p class="text-[10px] opacity-80">
                                            Kamu meraih sertifikat baru.
                                        </p>
                                    </div>
                                </div>
                            {:else}
                                <div
                                    class="flex items-center gap-3 rounded-xl bg-slate-800 p-3 text-white shadow-lg"
                                >
                                    <div class="rounded-full bg-white/20 p-1.5">
                                        <Info size={14} />
                                    </div>
                                    <div class="leading-tight">
                                        <p class="text-xs font-bold">Feedback Sistem</p>
                                        <p class="text-[10px] opacity-80">
                                            Pertahankan ritme belajarmu.
                                        </p>
                                    </div>
                                </div>
                            {/if}
                        {/if}
                    </div>
                </section>
            {/if}
        </div>
    </div>
{/if}
