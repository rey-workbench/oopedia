<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import { onMount, onDestroy } from "svelte";
    import Navbar from "@/components/navigation/Navbar.svelte";
    import Sidebar from "@/components/navigation/Sidebar.svelte";
    import Alert from "@/components/ui/Alert.svelte";
    import { sidebarOpen } from "@/stores/sidebar";
    import { isAdmin, isStudent } from "@/utils/roles";
    import { ROUTES } from "@/utils/route";
    import type { SharedProps } from "@/types/inertia";

    interface Props {
        title?: string;
        showNavbar?: boolean;
        showSidebar?: boolean;
        fullWidth?: boolean;
        variant?: "app" | "auth";
        children?: import("svelte").Snippet;
    }

    let {
        title = "OOPEDIAV2",
        showNavbar = true,
        showSidebar = true,
        fullWidth = false,
        variant = "app",
        children,
    }: Props = $props();

    const flash = $derived(($page.props as unknown as SharedProps).flash ?? {});
    const auth = $derived(($page.props as unknown as SharedProps).auth);
    const user = $derived(auth?.user ?? null);
    const isAuthenticated = $derived(!!user);
    const isAdminRole = $derived(isAuthenticated && isAdmin(user?.role_id));
    const isStudentRole = $derived(isAuthenticated && isStudent(user?.role_id));
    const showSidebarRender = $derived(
        variant === "app" && showSidebar && (isAdminRole || isStudentRole),
    );

    function handleResize() {
        if (window.innerWidth >= 1024) {
            sidebarOpen.set(false);
        }
    }

    onMount(() => {
        window.addEventListener("resize", handleResize);
    });
    onDestroy(() => window.removeEventListener("resize", handleResize));
</script>

<svelte:head>
    <title>{title}</title>
</svelte:head>

{#if variant === "auth"}
    <div
        class="relative min-h-screen flex items-center justify-center p-6 overflow-hidden bg-slate-50 font-poppins text-slate-600 antialiased"
    >
        <!-- Flash Messages -->
        {#if flash.success || flash.error || flash.info || flash.warning || (flash as any).status}
            <div
                class="fixed top-6 right-6 z-[100] flex flex-col gap-3 pointer-events-auto max-w-sm w-full"
            >
                {#if flash.success}
                    <Alert variant="success" dismissible={true}
                        >{flash.success}</Alert
                    >
                {/if}
                {#if flash.error}
                    <Alert variant="danger" dismissible={true}
                        >{flash.error}</Alert
                    >
                {/if}
                {#if flash.info}
                    <Alert variant="info" dismissible={true}>{flash.info}</Alert
                    >
                {/if}
                {#if flash.warning}
                    <Alert variant="warning" dismissible={true}
                        >{flash.warning}</Alert
                    >
                {/if}
                {#if (flash as any).status}
                    <Alert variant="success" dismissible={true}
                        >{(flash as any).status}</Alert
                    >
                {/if}
            </div>
        {/if}

        <!-- Decorative Background -->
        <div class="absolute top-0 left-0 w-full h-full pointer-events-none">
            <div
                class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary-600/5 rounded-full blur-[120px]"
            ></div>
            <div
                class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-primary-600/5 rounded-full blur-[120px]"
            ></div>
        </div>

        <div
            class="relative w-full max-w-lg animate-in fade-in zoom-in duration-700"
        >
            <!-- Logo -->
            <div class="flex flex-col items-center mb-10">
                <a href={ROUTES.HOME} class="flex items-center gap-4 group">
                    <div
                        class="w-16 h-16 bg-white rounded-[2rem] flex items-center justify-center shadow-2xl shadow-slate-200 group-hover:rotate-12 transition-transform duration-500"
                    >
                        <img
                            src="/images/logo.png"
                            alt="OOPedia"
                            class="w-10 h-auto"
                        />
                    </div>
                    <div>
                        <h2
                            class="text-3xl font-bold tracking-widest text-slate-900 leading-none"
                        >
                            OOPEDIA
                        </h2>
                        <p
                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1"
                        >
                            Learning System
                        </p>
                    </div>
                </a>
            </div>

            {@render children?.()}

            <p
                class="text-center mt-10 text-[10px] font-bold text-slate-300 uppercase tracking-widest"
            >
                &copy; {new Date().getFullYear()} OOPEDIA TEAM. ALL RIGHTS RESERVED.
            </p>
        </div>
    </div>
{:else}
    <div
        class="relative flex min-h-screen bg-slate-50 font-sans text-slate-900 antialiased overflow-x-hidden"
    >
        {#if showSidebarRender}
            <Sidebar />
            {#if $sidebarOpen}
                <div
                    role="button"
                    tabindex="0"
                    aria-label="Tutup sidebar"
                    class="fixed inset-0 z-[45] bg-gray-900/50 backdrop-blur-sm lg:hidden transition-opacity duration-300"
                    onclick={() => sidebarOpen.set(false)}
                    onkeydown={(e) =>
                        e.key === "Escape" && sidebarOpen.set(false)}
                ></div>
            {/if}
        {/if}

        <div
            class="flex-1 flex flex-col min-w-0 transition-all duration-300 {showSidebarRender
                ? 'lg:ml-64'
                : ''}"
        >
            {#if showNavbar}
                <Navbar titlePage={title} />
            {/if}

            <!-- Flash Messages -->
            {#if flash.success || flash.error || flash.info || flash.warning || (flash as any).status}
                <div
                    class="fixed top-24 right-6 z-[100] flex flex-col gap-3 pointer-events-auto max-w-sm w-full"
                >
                    {#if flash.success}
                        <Alert variant="success" dismissible={true}
                            >{flash.success}</Alert
                        >
                    {/if}
                    {#if flash.error}
                        <Alert variant="danger" dismissible={true}
                            >{flash.error}</Alert
                        >
                    {/if}
                    {#if flash.info}
                        <Alert variant="info" dismissible={true}
                            >{flash.info}</Alert
                        >
                    {/if}
                    {#if flash.warning}
                        <Alert variant="warning" dismissible={true}
                            >{flash.warning}</Alert
                        >
                    {/if}
                    {#if (flash as any).status}
                        <Alert variant="success" dismissible={true}
                            >{(flash as any).status}</Alert
                        >
                    {/if}
                </div>
            {/if}

            <main
                class="flex-1 w-full {fullWidth
                    ? ''
                    : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8'}"
            >
                {@render children?.()}
            </main>
        </div>
    </div>
{/if}
