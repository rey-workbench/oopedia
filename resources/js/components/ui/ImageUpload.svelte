<script>
    import { CloudUpload, Camera } from "lucide-svelte";

    /** @type {string | null} */
    export let preview = null;
    /** @type {string} */
    export let label = "Visualisasi Sampul";
    /** @type {string} */
    export let emptyIcon = "upload"; // "upload" | "camera"
    /** @type {string} */
    export let emptyText = "Unggah Sampul";
    /** @type {string | undefined} */
    export let error = undefined;
    /** @type {(e: Event) => void} */
    export let onChange;
</script>

<div class="lg:col-span-1 space-y-4">
    <label
        for="cover_image"
        class="text-[10px] font-bold uppercase tracking-widest text-slate-400 block"
    >
        {label}
    </label>
    <div
        class={`relative group aspect-video rounded-2xl bg-slate-50 border-2 ${preview ? "border-solid border-primary-500/30" : "border-dashed border-slate-200"} flex flex-col items-center justify-center overflow-hidden transition-all hover:border-primary-500/50`}
    >
        {#if preview}
            <img
                src={preview}
                alt="Preview Sampul"
                class="absolute inset-0 w-full h-full object-cover"
            />
        {:else}
            <div class="text-center group-hover:scale-110 transition-transform">
                {#if emptyIcon === "camera"}
                    <Camera size={24} class="text-slate-300 mb-2 mx-auto" />
                {:else}
                    <CloudUpload size={24} class="text-slate-300 mb-2 mx-auto" />
                {/if}
                <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400">
                    {emptyText}
                </p>
            </div>
        {/if}
        <input
            id="cover_image"
            type="file"
            accept="image/*"
            class="absolute inset-0 opacity-0 cursor-pointer"
            on:change={onChange}
        />
    </div>
    {#if error}
        <p class="text-rose-500 text-xs mt-1">{error}</p>
    {/if}
</div>
