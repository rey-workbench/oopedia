<script lang="ts">
    import { Download, FileImage, Award, CheckCircle, Shield } from 'lucide-svelte';
    import { page } from '@inertiajs/svelte';

    interface CertProps {
        materialTitle: string;
        type: 'gold' | 'silver' | 'bronze';
        issuedAt?: string | undefined;
        recipientName?: string | undefined;
        id?: string | number | undefined;
    }

    let { materialTitle, type = 'gold', issuedAt, recipientName, id }: CertProps = $props();

    let certRef: HTMLElement;
    let isDownloading = $state(false);

    const typeConfig = $derived(
        {
            gold: {
                label: 'Object-Oriented Architect',
                tier: 'GOLD',
                gradient: 'from-amber-400 via-yellow-300 to-amber-500',
                bgGradient: 'from-amber-50 via-yellow-50 to-amber-100',
                borderColor: '#d97706',
                accent: '#92400e',
                shimmer: 'rgba(251,191,36,0.4)',
                badgeClass: 'bg-amber-400 text-amber-900',
            },
            silver: {
                label: 'Senior OOP Developer',
                tier: 'SILVER',
                gradient: 'from-slate-400 via-gray-300 to-slate-500',
                bgGradient: 'from-slate-50 via-gray-50 to-slate-100',
                borderColor: '#64748b',
                accent: '#1e293b',
                shimmer: 'rgba(148,163,184,0.4)',
                badgeClass: 'bg-slate-400 text-slate-900',
            },
            bronze: {
                label: 'Junior OOP Programmer',
                tier: 'BRONZE',
                gradient: 'from-orange-400 via-amber-300 to-orange-500',
                bgGradient: 'from-orange-50 via-amber-50 to-orange-100',
                borderColor: '#c2410c',
                accent: '#7c2d12',
                shimmer: 'rgba(251,146,60,0.4)',
                badgeClass: 'bg-orange-400 text-orange-900',
            },
        }[type]
    );

    interface PagePropsWithAuth {
        auth: { user: { name: string } };
    }
    const userName = $derived(
        recipientName ??
            (page.props as unknown as PagePropsWithAuth)?.auth?.user?.name ??
            'Mahasiswa'
    );

    const formattedDate = $derived(
        issuedAt
            ? new Date(issuedAt).toLocaleDateString('id-ID', {
                  year: 'numeric',
                  month: 'long',
                  day: 'numeric',
              })
            : new Date().toLocaleDateString('id-ID', {
                  year: 'numeric',
                  month: 'long',
                  day: 'numeric',
              })
    );

    const certId = $derived(
        id ? `CERT-${String(id).padStart(6, '0')}` : `CERT-${Date.now().toString(36).toUpperCase()}`
    );

    async function downloadAs(format: 'pdf' | 'png') {
        if (!certRef || isDownloading) return;
        isDownloading = true;

        try {
            const { default: html2canvas } = await import('html2canvas');
            const canvas = await html2canvas(certRef, {
                scale: 3,
                useCORS: true,
                backgroundColor: '#fff',
                logging: false,
            });

            if (format === 'png') {
                const link = document.createElement('a');
                link.download = `Sertifikat-${type}-${userName.replace(/\s+/g, '_')}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            } else {
                const { jsPDF } = await import('jspdf');
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF({
                    orientation: 'landscape',
                    unit: 'mm',
                    format: 'a4',
                });
                const pageW = pdf.internal.pageSize.getWidth();
                const pageH = pdf.internal.pageSize.getHeight();
                pdf.addImage(imgData, 'PNG', 0, 0, pageW, pageH);
                pdf.save(`Sertifikat-${type}-${userName.replace(/\s+/g, '_')}.pdf`);
            }
        } finally {
            isDownloading = false;
        }
    }
</script>

<div
    class="group space-y-4"
    style="
        --cert-border: {typeConfig.borderColor};
        --cert-accent: {typeConfig.accent};
        --cert-shimmer: {typeConfig.shimmer};
    "
>
    <div
        bind:this={certRef}
        class="relative min-h-[380px] overflow-hidden rounded-2xl border-4 border-(--cert-border) bg-white shadow-2xl"
    >
        <div class="bg-linear-to-br {typeConfig.bgGradient} absolute inset-0"></div>

        <div class="absolute top-0 left-0 h-28 w-28 text-(--cert-border) opacity-20">
            <svg viewBox="0 0 100 100" class="h-full w-full" aria-hidden="true" role="presentation">
                <path d="M0,0 L100,0 L100,20 L20,20 L20,100 L0,100 Z" fill="currentColor"></path>
                <path d="M0,0 L80,0 L80,10 L10,10 L10,80 L0,80 Z" fill="currentColor" opacity="0.5"
                ></path>
            </svg>
        </div>
        <div class="absolute top-0 right-0 h-28 w-28 rotate-90 text-(--cert-border) opacity-20">
            <svg viewBox="0 0 100 100" class="h-full w-full" aria-hidden="true" role="presentation">
                <path d="M0,0 L100,0 L100,20 L20,20 L20,100 L0,100 Z" fill="currentColor"></path>
                <path d="M0,0 L80,0 L80,10 L10,10 L10,80 L0,80 Z" fill="currentColor" opacity="0.5"
                ></path>
            </svg>
        </div>
        <div class="absolute bottom-0 left-0 h-28 w-28 -rotate-90 text-(--cert-border) opacity-20">
            <svg viewBox="0 0 100 100" class="h-full w-full" aria-hidden="true" role="presentation">
                <path d="M0,0 L100,0 L100,20 L20,20 L20,100 L0,100 Z" fill="currentColor"></path>
            </svg>
        </div>
        <div class="absolute right-0 bottom-0 h-28 w-28 rotate-180 text-(--cert-border) opacity-20">
            <svg viewBox="0 0 100 100" class="h-full w-full" aria-hidden="true" role="presentation">
                <path d="M0,0 L100,0 L100,20 L20,20 L20,100 L0,100 Z" fill="currentColor"></path>
            </svg>
        </div>

        <div
            class="pointer-events-none absolute inset-0 flex items-center justify-center text-(--cert-border) opacity-[0.04]"
        >
            <Award size={320} />
        </div>

        <div
            class="pointer-events-none absolute inset-3 rounded-xl border-2 border-(--cert-border) opacity-30"
        ></div>

        <div class="relative z-10 flex flex-col items-center px-10 py-10 text-center">
            <div class="mb-2 flex items-center gap-3">
                <div class="h-px flex-1 bg-(--cert-border) opacity-30"></div>
                <p class="text-xs font-black tracking-[0.3em] text-(--cert-accent) uppercase">
                    OOPEDIA • POLITEKNIK NEGERI MALANG
                </p>
                <div class="h-px flex-1 bg-(--cert-border) opacity-30"></div>
            </div>

            <p class="mb-1 text-sm font-bold tracking-widest text-slate-500 uppercase">
                Certificate of Completion
            </p>

            <div
                class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-linear-to-br from-(--cert-shimmer) to-white shadow-xl ring-4 ring-(--cert-border)"
            >
                <Award size={40} class="text-(--cert-accent)" />
            </div>

            <span
                class="mb-4 rounded-full px-4 py-1 text-xs font-black tracking-widest uppercase shadow {typeConfig.badgeClass}"
            >
                {typeConfig.tier} CERTIFICATE
            </span>

            <p class="text-xs font-medium text-slate-500">Diberikan kepada</p>

            <h2 class="my-2 font-serif text-3xl font-black tracking-tight text-(--cert-accent)">
                {userName}
            </h2>

            <div class="my-3 flex items-center gap-2">
                <div class="h-px w-16 bg-(--cert-border) opacity-40"></div>
                <CheckCircle size={14} class="text-(--cert-border)" />
                <div class="h-px w-16 bg-(--cert-border) opacity-40"></div>
            </div>

            <p class="mb-1 max-w-sm text-xs leading-relaxed font-medium text-slate-600">
                telah berhasil menyelesaikan
            </p>
            <h3 class="mb-1 text-lg font-black tracking-wider text-(--cert-accent) uppercase">
                {materialTitle}
            </h3>
            <p class="mb-4 text-xs font-bold tracking-widest text-slate-500 uppercase">
                Object-Oriented Programming • {typeConfig.label}
            </p>

            <div
                class="mt-4 flex w-full items-end justify-between border-t pt-4"
                style="border-color: {typeConfig.borderColor}40;"
            >
                <div class="text-left">
                    <p class="text-xs font-bold tracking-widest text-slate-400 uppercase">
                        Tanggal
                    </p>
                    <p class="text-xs font-bold text-(--cert-accent)">
                        {formattedDate}
                    </p>
                </div>

                <div
                    class="flex h-16 w-16 items-center justify-center rounded-full border-2 border-(--cert-border) shadow-inner"
                    style="background: radial-gradient(circle, var(--cert-shimmer), transparent);"
                >
                    <Shield size={28} class="text-(--cert-border)" />
                </div>

                <div class="text-right">
                    <p class="text-xs font-bold tracking-widest text-slate-400 uppercase">
                        ID Sertifikat
                    </p>
                    <p class="font-mono text-xs font-bold text-(--cert-accent)">
                        {certId}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <button
            onclick={() => downloadAs('pdf')}
            disabled={isDownloading}
            class="flex flex-1 items-center justify-center gap-2 rounded-xl border-2 border-b-4 border-(--cert-border) bg-white px-4 py-3 text-xs font-bold tracking-widest text-(--cert-accent) uppercase transition-all hover:bg-slate-50 active:translate-y-[2px] active:border-b-2 disabled:translate-y-0 disabled:border-b-4 disabled:opacity-60"
        >
            {#if isDownloading}
                <span class="animate-spin">⏳</span> Mengunduh...
            {:else}
                <Download size={14} />
                Download PDF
            {/if}
        </button>
        <button
            onclick={() => downloadAs('png')}
            disabled={isDownloading}
            class="flex flex-1 items-center justify-center gap-2 rounded-xl border-2 border-b-4 border-(--cert-border) bg-white px-4 py-3 text-xs font-bold tracking-widest text-(--cert-accent) uppercase transition-all hover:bg-slate-50 active:translate-y-[2px] active:border-b-2 disabled:translate-y-0 disabled:border-b-4 disabled:opacity-60"
        >
            {#if isDownloading}
                <span class="animate-spin">⏳</span>
            {:else}
                <FileImage size={14} />
                Download PNG
            {/if}
        </button>
    </div>
</div>
