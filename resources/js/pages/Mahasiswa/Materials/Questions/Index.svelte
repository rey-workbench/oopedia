<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import GuestBanner from "@/components/ui/GuestBanner.svelte";
    import { QuestionListState } from "@/states/Mahasiswa/QuestionListState.svelte";
    import QuestionMaterialCard from "@/components/Mahasiswa/Materials/Questions/QuestionMaterialCard.svelte";

    export let materials = [];
    export let isGuest = false;

    const state = new QuestionListState(materials, isGuest);
</script>

<App title="Latihan Soal PBO">
    <div class="space-y-12">
        <PageHeader
            title="Latihan Soal PBO"
            subtitle="Uji pemahaman Anda dengan mengerjakan latihan soal untuk setiap materi"
        />

        {#if state.isGuest}
            <GuestBanner
                show={state.isGuest}
                variant="banner"
                title="Mode Tamu Aktif!"
                message="Anda hanya dapat melihat sebagian materi dan hanya 3 soal latihan dari setiap tingkat kesulitan. Untuk akses penuh, silakan login atau daftar."
            />
        {/if}

        <div class="grid grid-cols-1 gap-10">
            {#each state.materials as material (material.id)}
                <QuestionMaterialCard {material} isGuest={state.isGuest} />
            {/each}
        </div>
    </div>
</App>
