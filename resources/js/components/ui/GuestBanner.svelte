<script>
    import Button from "@/components/ui/Button.svelte";
    import { AlertTriangle } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";

    /** Control visibility externally */
    export let show = false;

    /** Title text */
    export let title = "Mode Tamu Aktif!";

    /** Description text */
    export let message =
        "Anda hanya dapat melihat sebagian materi. Untuk akses penuh, silakan login atau daftar.";

    /** Whether to show login/register action buttons */
    export let showActions = true;

    /** Visual variant: "banner" (full-width), "inline" (compact with icon) */
    export let variant = "banner";
</script>

{#if show}
    {#if variant === "banner"}
        <!-- Full-width page-level banner -->
        <div
            class="p-4 bg-amber-50 border border-amber-100 rounded-lg flex flex-col gap-2"
        >
            <span class="font-bold text-lg tracking-widest text-amber-900"
                >{title}</span
            >
            <p class="text-sm text-amber-800">
                {message}
            </p>
            {#if showActions}
                <div class="flex gap-4 mt-2">
                    <Button href={ROUTES.AUTH.LOGIN} variant="primary" size="sm"
                        >Login Sekarang</Button
                    >
                    <Button
                        href={ROUTES.AUTH.REGISTER}
                        variant="ghost"
                        size="sm">Daftar Akun</Button
                    >
                </div>
            {/if}
        </div>
    {:else}
        <!-- Compact inline banner with icon -->
        <div
            class="mb-8 p-5 bg-amber-50 border border-amber-100 rounded-2xl shadow-sm flex items-start gap-4"
        >
            <div
                class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center shrink-0"
            >
                <slot name="icon">
                    <AlertTriangle size={24} class="text-amber-600" />
                </slot>
            </div>
            <div>
                <strong class="text-amber-900 text-lg block mb-1"
                    >{title}</strong
                >
                <p class="text-amber-800">
                    <slot>
                        {message} Silakan
                        <a
                            href={ROUTES.AUTH.LOGIN}
                            class="font-bold underline hover:text-amber-950 transition-colors"
                            >login</a
                        >
                        atau
                        <a
                            href={ROUTES.AUTH.REGISTER}
                            class="font-bold underline hover:text-amber-950 transition-colors"
                            >daftar</a
                        > sebagai mahasiswa.
                    </slot>
                </p>
            </div>
        </div>
    {/if}
{/if}
