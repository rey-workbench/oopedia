<script>
    import Button from "@/ui/Button.svelte";
    import {
        Compass,
        ShieldAlert,
        Server,
        Wrench,
        AlertTriangle,
        Home,
    } from "lucide-svelte";

    export let status;

    const title =
        {
            503: "503: Service Unavailable",
            500: "500: Server Error",
            404: "404: Page Not Found",
            403: "403: Forbidden",
        }[status] || "Error";

    const description =
        {
            503: "Sorry, we are doing some maintenance on the site. Please check back soon.",
            500: "Whoops, something went wrong on our servers.",
            404: "Sorry, the page you are looking for could not be found.",
            403: "Sorry, you are forbidden from accessing this page.",
        }[status] || "An unexpected error occurred.";

    const illustration =
        {
            404: Compass,
            403: ShieldAlert,
            500: Server,
            503: Wrench,
        }[status] || AlertTriangle;
</script>

<svelte:head>
    <title>{title}</title>
</svelte:head>

<div
    class="min-h-screen flex items-center justify-center p-6 bg-slate-50 overflow-hidden font-poppins relative"
>
    <!-- Decorative Background -->
    <div class="absolute top-0 left-0 w-full h-full">
        <div
            class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary-600/5 rounded-full blur-[120px]"
        ></div>
        <div
            class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-primary-600/5 rounded-full blur-[120px]"
        ></div>
    </div>

    <div
        class="relative w-full max-w-lg text-center animate-in fade-in zoom-in duration-700"
    >
        <div
            class="w-32 h-32 bg-white rounded-[2.5rem] flex items-center justify-center shadow-2xl shadow-slate-200 mx-auto mb-8 relative z-10"
        >
            <svelte:component
                this={illustration}
                size={64}
                class="text-slate-300"
            />
        </div>

        <h1 class="text-6xl font-black text-slate-900 tracking-widest mb-2">
            {status}
        </h1>
        <h2
            class="text-xl font-bold text-slate-700 uppercase tracking-widest mb-4"
        >
            {title.split(":")[1] || "Error"}
        </h2>

        <p
            class="text-slate-400 font-bold text-xs uppercase tracking-widest mb-10 max-w-sm mx-auto leading-relaxed"
        >
            {description}
        </p>

        <div class="flex justify-center">
            <Button
                href="/"
                variant="primary"
                size="lg"
                icon={Home}
                class="shadow-xl shadow-primary-900/20"
            >
                KEMBALI KE BERANDA
            </Button>
        </div>

        <p
            class="text-center mt-12 text-[10px] font-bold text-slate-300 uppercase tracking-widest"
        >
            &copy; {new Date().getFullYear()} OOPEDIA TEAM.
        </p>
    </div>
</div>
