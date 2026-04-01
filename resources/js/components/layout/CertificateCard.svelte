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

    let {
        materialTitle,
        type = 'gold',
        issuedAt,
        recipientName,
        id,
    }: CertProps = $props();

    let certRef: HTMLElement;
    let isDownloading = $state(false);

    const typeConfig = $derived({
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
    }[type]);

    interface PagePropsWithAuth {
        auth: { user: { name: string } };
    }
    const userName = $derived(recipientName ?? (($page.props as unknown as PagePropsWithAuth)?.auth?.user?.name) ?? 'Mahasiswa');

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

<!-- Certificate Card Wrapper -->
<div class="group space-y-4">
    <!-- The certificate itself (captured by html2canvas) -->
    <div
        bind:this={certRef}
        class="relative overflow-hidden rounded-2xl border-4 bg-white shadow-2xl"
        style="border-color: {typeConfig.borderColor}; min-height: 380px;"
    >
        <!-- Background gradient -->
        <div class="bg-linear-to-br {typeConfig.bgGradient} absolute inset-0"></div>

        <!-- Decorative corner patterns -->
        <div class="absolute top-0 left-0 h-28 w-28 opacity-20">
            <svg viewBox="0 0 100 100" class="h-full w-full" style="color: {typeConfig.borderColor}">
                <path d="M0,0 L100,0 L100,20 L20,20 L20,100 L0,100 Z" fill="currentColor" />
                <path d="M0,0 L80,0 L80,10 L10,10 L10,80 L0,80 Z" fill="currentColor" opacity="0.5" />
            </svg>
        </div>
        <div class="absolute top-0 right-0 h-28 w-28 rotate-90 opacity-20">
            <svg viewBox="0 0 100 100" class="h-full w-full" style="color: {typeConfig.borderColor}">
                <path d="M0,0 L100,0 L100,20 L20,20 L20,100 L0,100 Z" fill="currentColor" />
                <path d="M0,0 L80,0 L80,10 L10,10 L10,80 L0,80 Z" fill="currentColor" opacity="0.5" />
            </svg>
        </div>
        <div class="absolute bottom-0 left-0 h-28 w-28 -rotate-90 opacity-20">
            <svg viewBox="0 0 100 100" class="h-full w-full" style="color: {typeConfig.borderColor}">
                <path d="M0,0 L100,0 L100,20 L20,20 L20,100 L0,100 Z" fill="currentColor" />
            </svg>
        </div>
        <div class="absolute bottom-0 right-0 h-28 w-28 rotate-180 opacity-20">
            <svg viewBox="0 0 100 100" class="h-full w-full" style="color: {typeConfig.borderColor}">
                <path d="M0,0 L100,0 L100,20 L20,20 L20,100 L0,100 Z" fill="currentColor" />
            </svg>
        </div>

        <!-- Watermark -->
        <div class="absolute inset-0 flex items-center justify-center opacity-[0.04] pointer-events-none">
            <Award size={320} style="color: {typeConfig.borderColor}" />
        </div>

        <!-- Inner border line -->
        <div
            class="absolute inset-3 rounded-xl border-2 pointer-events-none opacity-30"
            style="border-color: {typeConfig.borderColor}"
        ></div>

        <!-- Content -->
        <div class="relative z-10 flex flex-col items-center px-10 py-10 text-center">
            <!-- Header -->
            <div class="mb-2 flex items-center gap-3">
                <div class="h-px flex-1 opacity-30" style="background: {typeConfig.borderColor}"></div>
                <p class="text-[10px] font-black tracking-[0.3em] uppercase" style="color: {typeConfig.accent}">
                    OOPEDIA • POLITEKNIK NEGERI MALANG
                </p>
                <div class="h-px flex-1 opacity-30" style="background: {typeConfig.borderColor}"></div>
            </div>

            <!-- Certificate of Completion text (Udemy style) -->
            <p class="mb-1 text-sm font-bold tracking-widest uppercase text-slate-500">
                Certificate of Completion
            </p>

            <!-- Trophy / Award icon -->
            <div
                class="mb-4 flex h-20 w-20 items-center justify-center rounded-full shadow-xl ring-4"
                style="background: linear-gradient(135deg, {typeConfig.shimmer}, white); ring-color: {typeConfig.borderColor}"
            >
                <Award size={40} style="color: {typeConfig.accent}" />
            </div>

            <!-- Tier badge -->
            <span
                class="mb-4 rounded-full px-4 py-1 text-[10px] font-black tracking-widest uppercase shadow {typeConfig.badgeClass}"
            >
                {typeConfig.tier} CERTIFICATE
            </span>

            <!-- "This is to certify that" -->
            <p class="text-xs font-medium text-slate-500">Diberikan kepada</p>

            <!-- Recipient Name -->
            <h2
                class="my-2 font-serif text-3xl font-black tracking-tight"
                style="color: {typeConfig.accent}; font-family: 'Georgia', serif;"
            >
                {userName}
            </h2>

            <!-- Divider -->
            <div class="my-3 flex items-center gap-2">
                <div class="h-px w-16 opacity-40" style="background: {typeConfig.borderColor}"></div>
                <CheckCircle size={14} style="color: {typeConfig.borderColor}" />
                <div class="h-px w-16 opacity-40" style="background: {typeConfig.borderColor}"></div>
            </div>

            <!-- Description -->
            <p class="mb-1 max-w-sm text-xs font-medium leading-relaxed text-slate-600">
                telah berhasil menyelesaikan
            </p>
            <h3 class="mb-1 text-lg font-black uppercase tracking-wider" style="color: {typeConfig.accent}">
                {materialTitle}
            </h3>
            <p class="mb-4 text-xs font-bold text-slate-500 uppercase tracking-widest">
                Object-Oriented Programming • {typeConfig.label}
            </p>

            <!-- Footer -->
            <div class="mt-4 flex w-full items-end justify-between border-t pt-4" style="border-color: {typeConfig.borderColor}40">
                <div class="text-left">
                    <p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">Tanggal</p>
                    <p class="text-xs font-bold" style="color: {typeConfig.accent}">{formattedDate}</p>
                </div>

                <!-- Seal -->
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-full shadow-inner"
                    style="background: radial-gradient(circle, {typeConfig.shimmer}, transparent); border: 2px solid {typeConfig.borderColor}"
                >
                    <Shield size={28} style="color: {typeConfig.borderColor}" />
                </div>

                <div class="text-right">
                    <p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">ID Sertifikat</p>
                    <p class="text-xs font-bold font-mono" style="color: {typeConfig.accent}">{certId}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Download Buttons -->
    <div class="flex gap-3">
        <button
            onclick={() => downloadAs('pdf')}
            disabled={isDownloading}
            class="flex flex-1 items-center justify-center gap-2 rounded-xl border-2 bg-white px-4 py-3 text-xs font-bold tracking-widest uppercase shadow-sm transition-all hover:shadow-md disabled:opacity-60"
            style="border-color: {typeConfig.borderColor}; color: {typeConfig.accent};"
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
            class="flex flex-1 items-center justify-center gap-2 rounded-xl border-2 bg-white px-4 py-3 text-xs font-bold tracking-widest uppercase shadow-sm transition-all hover:shadow-md disabled:opacity-60"
            style="border-color: {typeConfig.borderColor}; color: {typeConfig.accent};"
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
