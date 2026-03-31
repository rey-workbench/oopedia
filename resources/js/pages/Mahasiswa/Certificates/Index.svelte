<script lang="ts">
    import App from '@/layouts/App.svelte';
    import PageHeader from '@/components/ui/PageHeader.svelte';
    import EmptyState from '@/components/ui/EmptyState.svelte';
    import CertificateCard from '@/components/layout/CertificateCard.svelte';
    import { Award } from 'lucide-svelte';
    import type { CertificatesPageProps } from '@/types';

    const { certifications = [] }: Omit<CertificatesPageProps, 'auth' | 'flash' | 'errors'> =
        $props();
</script>

<App title="Sertifikat Saya">
    <div class="space-y-12">
        <PageHeader
            title="Sertifikat Saya"
            subtitle="Sertifikat yang Anda raih dari menyelesaikan Final Project OOP."
        />

        {#if certifications.length === 0}
            <EmptyState
                title="Belum Ada Sertifikat"
                description="Selesaikan Final Project untuk mendapatkan sertifikat kelulusan Anda."
                icon={Award}
            />
        {:else}
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
                {#each certifications as cert (cert.material_id)}
                    <CertificateCard
                        materialTitle={cert.material_title}
                        type={cert.type}
                        issuedAt={cert.issued_at ?? undefined}
                        id={cert.material_id}
                    />
                {/each}
            </div>
        {/if}
    </div>
</App>
