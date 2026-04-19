<script lang="ts">
    import { CloudUpload, Camera } from 'lucide-svelte';

    interface Props {
        preview?: string | null;
        label?: string;
        emptyIcon?: string; // "upload" | "camera"
        emptyText?: string;
        error?: string | undefined;
        onchange: (e: Event) => void;
    }

    let {
        preview = null,
        label = 'Visualisasi Sampul',
        emptyIcon = 'upload',
        emptyText = 'Unggah Sampul',
        error = undefined,
        onchange,
    }: Props = $props();
</script>

<div class="space-y-4 lg:col-span-1">
    <label
        for="cover_image"
        class="block text-xs font-bold tracking-widest text-slate-400 uppercase"
    >
        {label}
    </label>
    <div
        class="group relative aspect-video rounded-3xl border-2 bg-slate-50 transition-all {preview
            ? 'border-primary-200 border-b-4 border-solid'
            : 'border-dashed border-slate-200'} hover:border-primary-400 flex flex-col items-center justify-center overflow-hidden"
    >
        {#if preview}
            <img
                src={preview}
                alt="Preview Sampul"
                class="absolute inset-0 h-full w-full object-cover"
            />
        {:else}
            <div class="text-center transition-transform group-hover:scale-110">
                {#if emptyIcon === 'camera'}
                    <Camera size={24} class="mx-auto mb-2 text-slate-300" />
                {:else}
                    <CloudUpload size={24} class="mx-auto mb-2 text-slate-300" />
                {/if}
                <p class="text-xs font-bold tracking-widest text-slate-400 uppercase">
                    {emptyText}
                </p>
            </div>
        {/if}
        <input
            id="cover_image"
            type="file"
            accept="image/*"
            class="absolute inset-0 cursor-pointer opacity-0"
            {onchange}
        />
    </div>
    {#if error}
        <p class="mt-1 text-xs text-rose-500">{error}</p>
    {/if}
</div>
