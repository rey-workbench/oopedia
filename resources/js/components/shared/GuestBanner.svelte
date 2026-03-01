<script lang="ts">
    import type { Snippet } from "svelte";
    import Button from "@/components/ui/Button.svelte";
    import { AlertTriangle } from "lucide-svelte";
    import { ROUTES } from "@/utils/route";

    interface Props {
        /** Control visibility externally */
        show?: boolean;
        /** Title text */
        title?: string;
        /** Description text */
        message?: string;
        /** Whether to show login/register action buttons */
        showActions?: boolean;
        /** Visual variant: "banner" (full-width), "inline" (compact with icon) */
        variant?: "banner" | "inline";
        /** Custom icon snippet */
        icon?: Snippet;
        /** Default slot / children snippet */
        children?: Snippet;
    }

    let {
        show = false,
        title = "Mode Tamu Aktif!",
        message = "Anda hanya dapat melihat sebagian materi. Untuk akses penuh, silakan login atau daftar.",
        showActions = true,
        variant = "banner",
        icon,
        children,
    }: Props = $props();
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
                {#if icon}
                    {@render icon()}
                {:else}
                    <AlertTriangle size={24} class="text-amber-600" />
                {/if}
            </div>
            <div>
                <strong class="text-amber-900 text-lg block mb-1"
                    >{title}</strong
                >
                <p class="text-amber-800">
                    {#if children}
                        {@render children()}
                    {:else}
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
                    {/if}
                </p>
            </div>
        </div>
    {/if}
{/if}
