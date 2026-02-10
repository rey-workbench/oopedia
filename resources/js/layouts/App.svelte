<script>
    import { page } from "@inertiajs/svelte";
    import { onMount, onDestroy } from "svelte";
    import Navbar from "../components/navigation/Navbar.svelte";
    import Sidebar from "../components/navigation/Sidebar.svelte";
    import Alert from "../components/ui/Alert.svelte";

    export let title = "OOPEDIAV2";
    export let showNavbar = true;
    export let showSidebar = true;
    export let fullWidth = false;

    let showMobileSidebar = false;

    // Handle flash messages
    $: flash = ($page && $page.props && $page.props.flash) || {};

    function toggleSidebar() {
        showMobileSidebar = !showMobileSidebar;
    }

    function handleResize() {
        if (window.innerWidth >= 1024) {
            showMobileSidebar = false; // Reset on large screens
        }
    }

    onMount(() => {
        window.addEventListener("resize", handleResize);
        window.addEventListener("toggle-sidebar", toggleSidebar);
    });

    onDestroy(() => {
        if (typeof window !== "undefined") {
            window.removeEventListener("resize", handleResize);
            window.removeEventListener("toggle-sidebar", toggleSidebar);
        }
    });

    $: auth = ($page && $page.props && $page.props.auth) || {};
    $: user = auth.user;
    $: isAuthenticated = !!user;
    $: userRole = user ? user.role_id : null;
    $: isAdminRole = isAuthenticated && [1, 2].includes(userRole);
    $: isStudentRole = isAuthenticated && [3, 4].includes(userRole);

    // Update showSidebar prop logic if needed based on role,
    // but for now relying on passed prop or default.
    // Blade logic: if (showSidebar === null) showSidebar = true;
    // We can default showSidebar=true in props.

    $: showSidebarRender = showSidebar && (isAdminRole || isStudentRole);
</script>

<svelte:head>
    <title>{title}</title>
</svelte:head>

<div
    class="relative flex min-h-screen bg-gray-50 font-sans text-slate-900 antialiased"
>
    {#if showSidebarRender}
        <Sidebar showSidebar={showMobileSidebar} />
        {#if showMobileSidebar}
            <!-- svelte-ignore a11y-click-events-have-key-events -->
            <div
                role="button"
                tabindex="0"
                aria-label="Close sidebar"
                class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden transition-opacity duration-300"
                on:click={toggleSidebar}
                on:keydown={(e) => e.key === "Escape" && toggleSidebar()}
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
        {#if flash.success || flash.error || flash.info || flash.warning || flash.status}
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
                    <Alert variant="info" dismissible={true}>{flash.info}</Alert
                    >
                {/if}
                {#if flash.warning}
                    <Alert variant="warning" dismissible={true}
                        >{flash.warning}</Alert
                    >
                {/if}
                {#if flash.status}
                    <Alert variant="success" dismissible={true}
                        >{flash.status}</Alert
                    >
                {/if}
            </div>
        {/if}

        <main
            class="flex-1 w-full {fullWidth
                ? ''
                : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8'}"
        >
            <slot />
        </main>
    </div>
</div>
