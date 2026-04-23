<script lang="ts">
    import { Download, FileImage } from 'lucide-svelte';
    import { ROUTES } from '@/utils';

    interface CertProps {
        materialTitle: string;
        type: 'gold' | 'silver' | 'bronze';
        issuedAt?: string | undefined;
        recipientName?: string | undefined;
        id?: string | number | undefined;
    }

    let { id }: CertProps = $props();

    let isDownloading = $state(false);

    const previewUrl = $derived(
        id ? ROUTES.MAHASISWA.CERTIFICATES.PREVIEW(id) : '#'
    );

    const downloadAs = async () => {
        if (!id) return;
        isDownloading = true;
        try {
            window.location.href = ROUTES.MAHASISWA.CERTIFICATES.DOWNLOAD(id);
        } finally {
            setTimeout(() => (isDownloading = false), 2000);
        }
    };
</script>

<div class="group space-y-4">
    <div
        class="relative aspect-[1.414/1] w-full overflow-hidden rounded-2xl border-4 border-slate-200 bg-white shadow-2xl transition-all hover:border-amber-400/50"
    >
        {#if id}
            <iframe
                src={previewUrl}
                title="Sertifikat Preview"
                class="h-[200%] w-[200%] origin-top-left scale-50 border-none pointer-events-none"
            ></iframe>
        {:else}
            <div class="flex h-full items-center justify-center bg-slate-50 text-slate-400">
                Memuat preview...
            </div>
        {/if}
        
        <div class="absolute inset-0 bg-transparent pointer-events-auto"></div>
    </div>

    <div class="flex gap-3">
        <button
            onclick={() => downloadAs()}
            disabled={isDownloading}
            class="flex flex-1 items-center justify-center gap-2 rounded-xl border-2 border-b-4 border-slate-200 bg-white px-4 py-3 text-xs font-bold tracking-widest text-slate-700 uppercase transition-all hover:bg-slate-50 active:translate-y-[2px] active:border-b-2 disabled:translate-y-0 disabled:border-b-4 disabled:opacity-60"
        >
            {#if isDownloading}
                <span class="animate-spin">⏳</span> Mengunduh...
            {:else}
                <Download size={14} />
                Download PDF
            {/if}
        </button>
        <a
            href={previewUrl}
            target="_blank"
            class="flex flex-1 items-center justify-center gap-2 rounded-xl border-2 border-b-4 border-slate-200 bg-white px-4 py-3 text-xs font-bold tracking-widest text-slate-700 uppercase transition-all hover:bg-slate-50 active:translate-y-[2px] active:border-b-2"
        >
            <FileImage size={14} />
            Buka Preview
        </a>
    </div>
</div>
