<script>
    import { onMount } from "svelte";
    import { page } from "@inertiajs/svelte";
    import Alert from "../components/ui/Alert.svelte";

    export let title;

    $: flash = $page.props.flash || {};

    onMount(() => {
        document.title = title || "OOPedia";
    });
</script>

<div
    class="relative min-h-screen flex items-center justify-center p-6 overflow-hidden bg-slate-50 font-poppins text-slate-600 antialiased"
>
    <!-- Flash Messages (Absolute positioned over the whole layout) -->
    {#if flash.success || flash.error || flash.info || flash.warning || flash.status}
        <div
            class="fixed top-6 right-6 z-[100] flex flex-col gap-3 pointer-events-auto max-w-sm w-full"
        >
            {#if flash.success}
                <Alert variant="success" dismissible={true}
                    >{flash.success}</Alert
                >
            {/if}
            {#if flash.error}
                <Alert variant="danger" dismissible={true}>{flash.error}</Alert>
            {/if}
            {#if flash.info}
                <Alert variant="info" dismissible={true}>{flash.info}</Alert>
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
    <!-- Decorative Background -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none">
        <div
            class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-600/5 rounded-full blur-[120px]"
        ></div>
        <div
            class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-600/5 rounded-full blur-[120px]"
        ></div>
    </div>

    <div
        class="relative w-full max-w-lg animate-in fade-in zoom-in duration-700"
    >
        <!-- Logo -->
        <div class="flex flex-col items-center mb-10">
            <a href="/" class="flex items-center gap-4 group">
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

        <!-- Content slot -->
        <slot />

        <p
            class="text-center mt-10 text-[10px] font-bold text-slate-300 uppercase tracking-widest"
        >
            &copy; {new Date().getFullYear()} OOPEDIA TEAM. ALL RIGHTS RESERVED.
        </p>
    </div>
</div>
