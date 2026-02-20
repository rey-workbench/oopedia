<script>
    import App from "@/layouts/App.svelte";
    import PageHeader from "@/components/ui/PageHeader.svelte";
    import { ShieldCheck } from "lucide-svelte";
    import ProfileHero from "@/components/Mahasiswa/Profile/ProfileHero.svelte";
    import PersonalizationStats from "@/components/Mahasiswa/Profile/PersonalizationStats.svelte";
    import DetailedStats from "@/components/Mahasiswa/Profile/DetailedStats.svelte";
    import AccountStatusSidebar from "@/components/Mahasiswa/Profile/AccountStatusSidebar.svelte";
    import ProfileForm from "@/components/Mahasiswa/Profile/ProfileForm.svelte";
    import { ProfileState } from "@/states/Mahasiswa/ProfileState.svelte";

    export let user;
    export let personalization = {};

    const state = new ProfileState(user, personalization);
</script>

<App title="Profil Mahasiswa">
    <div class="space-y-12">
        <!-- Header -->
        <PageHeader
            title="Profil Saya"
            subtitle="Atur informasi akun dan keamanan Anda untuk pengalaman belajar yang lebih personal."
        >
            <div slot="actions">
                <div
                    class="flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 rounded-2xl border border-emerald-100"
                >
                    <ShieldCheck size={16} />
                    <span
                        class="text-[10px] font-bold uppercase tracking-widest"
                        >Akun Terverifikasi</span
                    >
                </div>
            </div>
        </PageHeader>

        <!-- Profile Hero Card -->
        <ProfileHero user={state.user} />

        <!-- Personalization Section -->
        <div class="space-y-8">
            <h3
                class="text-xl font-bold tracking-widest text-slate-900 uppercase"
            >
                Data Personalisasi Pembelajaran
            </h3>

            <PersonalizationStats personalization={state.personalization} />

            <!-- Detailed Stats -->
            <DetailedStats personalization={state.personalization} />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Sidebar Info -->
            <AccountStatusSidebar />

            <!-- Main Form -->
            <ProfileForm user={state.user} />
        </div>
    </div>
</App>
