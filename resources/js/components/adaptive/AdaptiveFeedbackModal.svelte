<script>
    import { createEventDispatcher } from "svelte";
    import Button from "../ui/Button.svelte";
    import Badge from "../ui/Badge.svelte";
    import {
        CheckCircle2,
        XCircle,
        RotateCcw,
        ArrowRight,
        Video,
        FileText,
        Code,
        Brain,
        Zap,
        ArrowDown,
        Trophy,
        Medal,
        BookOpen,
        AlertTriangle,
        Target,
        TrendingUp,
        Star,
    } from "lucide-svelte";

    export let show = false;
    export let feedbackData = {
        status: "success",
        message: "",
        nextUrl: "",
        adaptiveResult: {},
    };

    const dispatch = createEventDispatcher();

    // Parse adaptive result with fallbacks
    $: actionCode = feedbackData.adaptiveResult?.triggered_rule?.action || null;
    $: nextAction =
        feedbackData.adaptiveResult?.new_state?.next_action_data?.label ||
        (feedbackData.status === "success" ? "Lanjut" : "Lihat Materi");
    $: nextActionType =
        feedbackData.adaptiveResult?.new_state?.next_action_data?.type ||
        "question";
    $: recommendation =
        feedbackData.adaptiveResult?.new_state?.recommendation || null;
    $: triggeredRule = feedbackData.adaptiveResult?.triggered_rule || null;
    $: certification =
        feedbackData.adaptiveResult?.new_state?.certification || null;
    $: xpEarned = feedbackData.adaptiveResult?.global_xp_earned || 0;
    $: streakBonus = feedbackData.adaptiveResult?.streak_bonus || null;
    $: interventionType =
        feedbackData.adaptiveResult?.new_state?.intervention_type || null;
    $: fastTrackActive =
        feedbackData.adaptiveResult?.new_state?.fast_track_active || false;

    // Determine modal variant based on action code and intervention type
    function getModalVariant() {
        if (certification) return "certificate"; // H09, H10, H11

        // Check intervention type first (crisis/recovery/persistent) - these should NEVER show "Try Again"
        if (
            interventionType?.includes("crisis") ||
            interventionType?.includes("recovery") ||
            interventionType?.includes("persistent") ||
            interventionType?.includes("project_revision") ||
            interventionType?.includes("safety")
        )
            return "intervention";

        // Also check by action code
        if (["H01", "H02", "H03", "H04"].includes(actionCode))
            return "intervention"; // Crisis/Recovery

        if (actionCode === "H06") return "acceleration"; // Accelerated Jump
        if (actionCode === "H07") return "backtrack"; // Critical Backtracking
        if (actionCode === "H08") return "graduation"; // Module Graduation
        if (feedbackData.status === "success") return "success";
        return "error";
    }

    // Get icon based on action code
    function getActionIcon() {
        switch (actionCode) {
            case "H01": // Visual Crisis Intervention
                return Video;
            case "H02": // Textual Remediation
                return FileText;
            case "H03": // Syntax Recovery
                return Code;
            case "H04": // Logic Recovery
                return Brain;
            case "H05": // Standard Promotion
                return CheckCircle2;
            case "H06": // Accelerated Jump
                return Zap;
            case "H07": // Critical Backtracking
                return ArrowDown;
            case "H08": // Module Graduation
                return Trophy;
            case "H09": // Gold Certificate
            case "H10": // Silver Certificate
            case "H11": // Bronze Certificate
                return Medal;
            default:
                return feedbackData.status === "success"
                    ? CheckCircle2
                    : XCircle;
        }
    }

    // Get icon color based on action code
    function getIconColor() {
        switch (actionCode) {
            case "H01":
            case "H02":
                return "text-amber-500";
            case "H03":
            case "H04":
                return "text-purple-500";
            case "H06":
                return "text-primary-500";
            case "H07":
                return "text-rose-500";
            case "H08":
                return "text-emerald-500";
            case "H09":
                return "text-yellow-500";
            case "H10":
                return "text-slate-400";
            case "H11":
                return "text-orange-600";
            default:
                return feedbackData.status === "success"
                    ? "text-emerald-500"
                    : "text-rose-500";
        }
    }

    // Get certificate details
    function getCertificateDetails() {
        switch (certification) {
            case "gold":
                return {
                    color: "bg-amber-400",
                    textColor: "text-yellow-600",
                    title: "SERTIFIKAT EMAS",
                    badge: "🥇",
                    subtitle: "Object-Oriented Architect",
                };
            case "silver":
                return {
                    color: "bg-slate-300",
                    textColor: "text-slate-600",
                    title: "SERTIFIKAT PERAK",
                    badge: "🥈",
                    subtitle: "Object-Oriented Developer",
                };
            case "bronze":
                return {
                    color: "bg-orange-400",
                    textColor: "text-orange-600",
                    title: "SERTIFIKAT PERUNGGU",
                    badge: "🥉",
                    subtitle: "Junior Object-Oriented Programmer",
                };
            default:
                return null;
        }
    }

    function handleTryAgain() {
        dispatch("tryAgain");
    }

    function handleNext() {
        dispatch("next");
    }

    $: variant = getModalVariant();
    $: IconComponent = getActionIcon();
    $: iconColor = getIconColor();
    $: certDetails = getCertificateDetails();
</script>

{#if show}
    <div
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm transition-all duration-300"
        role="dialog"
        aria-modal="true"
    >
        <div
            class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full mx-4 transform scale-100 transition-all overflow-hidden"
        >
            <!-- Certificate Variant -->
            {#if variant === "certificate" && certDetails}
                <div
                    class={`${certDetails.color} p-16 text-center text-white relative overflow-hidden`}
                >
                    <div class="absolute -top-10 -right-10 text-9xl opacity-10">
                        {certDetails.badge}
                    </div>
                    <div class="relative z-10">
                        <div
                            class="w-32 h-32 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl border-4 border-white/30"
                        >
                            <svelte:component
                                this={IconComponent}
                                size={64}
                                class="text-white"
                            />
                        </div>
                        <h2
                            class="text-5xl font-bold mb-3 tracking-widest drop-shadow-lg"
                        >
                            {certDetails.title}
                        </h2>
                        <div
                            class="inline-block bg-white/20 backdrop-blur-md px-6 py-2 rounded-full text-sm font-bold tracking-widest mb-4"
                        >
                            {certDetails.subtitle}
                        </div>
                        <p class="text-white/90 text-lg font-medium mt-4">
                            {feedbackData.message}
                        </p>
                    </div>
                </div>
                <div class="p-10 bg-white">
                    {#if xpEarned > 0}
                        <div
                            class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-center"
                        >
                            <div class="flex items-center justify-center gap-2">
                                <Star
                                    size={20}
                                    class="text-amber-500 fill-current"
                                />
                                <span class="text-lg font-bold text-amber-700"
                                    >+{xpEarned} XP</span
                                >
                            </div>
                        </div>
                    {/if}
                    <Button
                        variant="primary"
                        on:click={handleNext}
                        class="w-full py-4 uppercase tracking-widest text-base font-bold"
                    >
                        Lanjutkan <ArrowRight size={20} class="ml-2" />
                    </Button>
                </div>

                <!-- Acceleration Variant -->
            {:else if variant === "acceleration"}
                <div
                    class="bg-primary-600 p-12 text-center text-white relative overflow-hidden"
                >
                    <div
                        class="absolute top-0 left-0 w-full h-full opacity-10"
                    ></div>
                    <div class="relative z-10">
                        <div
                            class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl border-4 border-white/30 animate-pulse"
                        >
                            <svelte:component
                                this={IconComponent}
                                size={48}
                                class="text-white"
                            />
                        </div>
                        <Badge
                            variant="secondary"
                            size="lg"
                            class="mb-4 bg-white/20 text-white border-white/30"
                        >
                            <Zap size={16} class="mr-1" /> LOMPATAN KESULITAN
                        </Badge>
                        <h2 class="text-4xl font-bold mb-3 tracking-widest">
                            PERCEPATAN!
                        </h2>
                        <p class="text-primary-50 text-lg font-medium">
                            {feedbackData.message}
                        </p>
                    </div>
                </div>
                <div class="p-10 bg-white">
                    {#if xpEarned > 0}
                        <div
                            class="mb-6 p-4 bg-primary-50 border border-primary-200 rounded-xl"
                        >
                            <div class="flex items-center justify-center gap-3">
                                <Star
                                    size={20}
                                    class="text-blue-500 fill-current"
                                />
                                <span class="text-lg font-bold text-primary-700"
                                    >Bonus: +{xpEarned} XP</span
                                >
                            </div>
                        </div>
                    {/if}
                    {#if triggeredRule}
                        <div
                            class="mb-6 p-4 bg-slate-50 rounded-xl text-sm text-slate-600"
                        >
                            <div class="font-bold text-slate-700 mb-1">
                                Adaptive Rule Triggered:
                            </div>
                            <Badge variant="outline" size="sm"
                                >{triggeredRule.id}: {triggeredRule.name}</Badge
                            >
                        </div>
                    {/if}
                    <Button
                        variant="primary"
                        on:click={handleNext}
                        class="w-full py-4 uppercase tracking-widest text-base font-bold bg-primary-600 hover:bg-primary-700"
                    >
                        {nextAction}
                        <ArrowRight size={20} class="ml-2" />
                    </Button>
                </div>

                <!-- Intervention Variant (Crisis/Recovery) -->
            {:else if variant === "intervention"}
                <div class="p-12">
                    <div class="text-center mb-8">
                        <div
                            class={`w-24 h-24 ${actionCode === "H01" || actionCode === "H02" ? "bg-amber-100" : "bg-purple-100"} rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg`}
                        >
                            <svelte:component
                                this={IconComponent}
                                size={48}
                                class={iconColor}
                            />
                        </div>
                        <Badge
                            variant="outline"
                            size="lg"
                            class="mb-4 border-amber-300 text-amber-700"
                        >
                            <AlertTriangle size={16} class="mr-1" />
                            {interventionType?.includes("crisis")
                                ? "Intervensi Krisis"
                                : interventionType?.includes("recovery")
                                  ? "Pemulihan"
                                  : interventionType?.includes("persistent")
                                    ? "Safety Net"
                                    : "Rekomendasi Adaptif"}
                        </Badge>
                        <h2
                            class="text-3xl font-bold mb-3 text-slate-800 uppercase tracking-wide"
                        >
                            {feedbackData.status === "success"
                                ? "Bagus!"
                                : "Perlu Perbaikan"}
                        </h2>
                        <p class="text-lg text-slate-600 mb-6">
                            {feedbackData.message}
                        </p>
                    </div>

                    <!-- Recommendation Box -->
                    {#if recommendation}
                        <div
                            class="mb-8 p-6 bg-primary-50 border-2 border-primary-200 rounded-2xl"
                        >
                            <div
                                class="flex items-start gap-4 mb-4 pb-4 border-b border-primary-200"
                            >
                                <div
                                    class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center flex-shrink-0"
                                >
                                    <BookOpen
                                        size={20}
                                        class="text-primary-600"
                                    />
                                </div>
                                <div class="flex-1">
                                    <h3
                                        class="text-lg font-bold text-primary-900 mb-1"
                                    >
                                        Rekomendasi Pembelajaran
                                    </h3>
                                    <p class="text-sm text-primary-700">
                                        Sistem merekomendasikan metode berikut
                                        untuk meningkatkan pemahaman Anda
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                {#if actionCode === "H01"}
                                    <Video size={24} class="text-purple-600" />
                                    <span
                                        class="text-base font-bold text-slate-800"
                                        >{recommendation}</span
                                    >
                                {:else if actionCode === "H02"}
                                    <FileText
                                        size={24}
                                        class="text-primary-600"
                                    />
                                    <span
                                        class="text-base font-bold text-slate-800"
                                        >{recommendation}</span
                                    >
                                {:else if actionCode === "H03"}
                                    <Code size={24} class="text-purple-600" />
                                    <span
                                        class="text-base font-bold text-slate-800"
                                        >{recommendation}</span
                                    >
                                {:else if actionCode === "H04"}
                                    <Brain size={24} class="text-primary-600" />
                                    <span
                                        class="text-base font-bold text-slate-800"
                                        >{recommendation}</span
                                    >
                                {:else}
                                    <Target
                                        size={24}
                                        class="text-primary-600"
                                    />
                                    <span
                                        class="text-base font-bold text-slate-800"
                                        >{recommendation}</span
                                    >
                                {/if}
                            </div>
                        </div>
                    {/if}

                    <!-- Rule Info -->
                    {#if triggeredRule}
                        <div
                            class="mb-6 p-4 bg-slate-50 rounded-xl text-sm text-slate-600"
                        >
                            <div class="font-bold text-slate-700 mb-2">
                                Sistem Adaptif:
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <Badge variant="outline" size="sm"
                                        >{triggeredRule.id}</Badge
                                    >
                                    <span>{triggeredRule.name}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Badge
                                        variant="secondary"
                                        size="sm"
                                        class="bg-primary-100 text-primary-700"
                                        >{triggeredRule.action}</Badge
                                    >
                                    <span class="text-xs text-slate-500"
                                        >Priority: {triggeredRule.priority}</span
                                    >
                                </div>
                            </div>
                        </div>
                    {/if}

                    <Button
                        variant="primary"
                        on:click={handleNext}
                        class="w-full py-4 uppercase tracking-widest text-base font-bold"
                    >
                        {nextAction}
                        <ArrowRight size={20} class="ml-2" />
                    </Button>
                </div>

                <!-- Backtrack Variant -->
            {:else if variant === "backtrack"}
                <div class="p-12 text-center">
                    <div
                        class="w-24 h-24 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg"
                    >
                        <svelte:component
                            this={IconComponent}
                            size={48}
                            class={iconColor}
                        />
                    </div>
                    <Badge
                        variant="outline"
                        size="lg"
                        class="mb-4 border-rose-300 text-rose-700"
                    >
                        <ArrowDown size={16} class="mr-1" /> Penyesuaian Tingkat
                    </Badge>
                    <h2
                        class="text-3xl font-bold mb-3 text-slate-800 uppercase tracking-wide"
                    >
                        Mari Kembali ke Dasar
                    </h2>
                    <p class="text-lg text-slate-600 mb-8">
                        {feedbackData.message}
                    </p>

                    {#if recommendation}
                        <div
                            class="mb-8 p-6 bg-rose-50 border border-rose-200 rounded-2xl"
                        >
                            <div class="flex items-center justify-center gap-3">
                                <BookOpen size={24} class="text-rose-600" />
                                <span class="text-base font-bold text-slate-800"
                                    >{recommendation}</span
                                >
                            </div>
                        </div>
                    {/if}

                    {#if triggeredRule}
                        <div
                            class="mb-6 p-4 bg-slate-50 rounded-xl text-sm text-slate-600"
                        >
                            <Badge variant="outline" size="sm"
                                >{triggeredRule.id}: {triggeredRule.name}</Badge
                            >
                        </div>
                    {/if}

                    <Button
                        variant="primary"
                        on:click={handleNext}
                        class="w-full py-4 uppercase tracking-widest text-base font-bold bg-rose-600 hover:bg-rose-700"
                    >
                        {nextAction}
                        <ArrowRight size={20} class="ml-2" />
                    </Button>
                </div>

                <!-- Graduation Variant -->
            {:else if variant === "graduation"}
                <div
                    class="bg-emerald-600 p-12 text-center text-white relative overflow-hidden"
                >
                    <div
                        class="absolute top-0 right-0 p-8 opacity-10 animate-bounce"
                    >
                        <Trophy size={120} class="text-white" />
                    </div>
                    <div class="relative z-10">
                        <div
                            class="w-28 h-28 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl border-4 border-white/30"
                        >
                            <svelte:component
                                this={IconComponent}
                                size={56}
                                class="text-white"
                            />
                        </div>
                        <Badge
                            variant="secondary"
                            size="lg"
                            class="mb-4 bg-white/20 text-white border-white/30"
                        >
                            <Trophy size={16} class="mr-1" /> MODUL SELESAI
                        </Badge>
                        <h2 class="text-5xl font-bold mb-3 tracking-widest">
                            SELAMAT!
                        </h2>
                        <p class="text-emerald-50 text-xl font-medium">
                            {feedbackData.message}
                        </p>
                    </div>
                </div>
                <div class="p-10 bg-white">
                    {#if xpEarned > 0}
                        <div
                            class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-center"
                        >
                            <div class="flex items-center justify-center gap-2">
                                <Star
                                    size={20}
                                    class="text-emerald-500 fill-current"
                                />
                                <span class="text-lg font-bold text-emerald-700"
                                    >+{xpEarned} XP</span
                                >
                            </div>
                        </div>
                    {/if}
                    <Button
                        variant="primary"
                        on:click={handleNext}
                        class="w-full py-4 uppercase tracking-widest text-base font-bold bg-emerald-600 hover:bg-emerald-700"
                    >
                        {nextAction}
                        <ArrowRight size={20} class="ml-2" />
                    </Button>
                </div>

                <!-- Standard Success/Error Variant -->
            {:else}
                <div class="p-12 text-center">
                    <div class="text-8xl mb-6">
                        <svelte:component
                            this={IconComponent}
                            size={96}
                            class={iconColor + " mx-auto"}
                        />
                    </div>

                    <h2
                        class={`text-4xl font-bold mb-4 uppercase tracking-widest ${feedbackData.status === "success" ? "text-emerald-600" : "text-rose-600"}`}
                    >
                        {feedbackData.status === "success"
                            ? "BENAR!"
                            : "SALAH!"}
                    </h2>

                    <p class="text-lg text-slate-600 mb-8">
                        {feedbackData.message}
                    </p>

                    <!-- XP & Streak Display -->
                    {#if feedbackData.status === "success"}
                        <div class="mb-8 space-y-3">
                            {#if xpEarned > 0}
                                <div
                                    class="p-4 bg-primary-50 border border-primary-200 rounded-xl"
                                >
                                    <div
                                        class="flex items-center justify-center gap-2"
                                    >
                                        <Star
                                            size={20}
                                            class="text-primary-500 fill-current"
                                        />
                                        <span
                                            class="text-lg font-bold text-primary-700"
                                            >+{xpEarned} XP</span
                                        >
                                    </div>
                                </div>
                            {/if}
                            {#if streakBonus}
                                <div
                                    class="p-4 bg-orange-50 border border-orange-200 rounded-xl"
                                >
                                    <div
                                        class="flex items-center justify-center gap-2"
                                    >
                                        <TrendingUp
                                            size={20}
                                            class="text-orange-500"
                                        />
                                        <span
                                            class="text-base font-bold text-orange-700"
                                            >{streakBonus}</span
                                        >
                                    </div>
                                </div>
                            {/if}
                        </div>
                    {/if}

                    <!-- Rule Info (Debug) -->
                    {#if triggeredRule && feedbackData.status === "success"}
                        <div
                            class="mb-8 p-4 bg-slate-50 rounded-xl text-sm text-slate-600"
                        >
                            <Badge variant="outline" size="sm"
                                >{triggeredRule.id}: {triggeredRule.name}</Badge
                            >
                        </div>
                    {/if}

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        {#if feedbackData.status === "error" && nextActionType !== "material" && !recommendation}
                            <Button
                                variant="outline"
                                on:click={handleTryAgain}
                                class="px-8 py-3 uppercase tracking-widest text-sm font-bold"
                            >
                                <RotateCcw size={18} class="mr-2" /> Coba Lagi
                            </Button>
                        {/if}
                        <Button
                            variant="primary"
                            on:click={handleNext}
                            class="px-8 py-3 uppercase tracking-widest text-sm font-bold"
                        >
                            {nextAction}
                            <ArrowRight size={18} class="ml-2" />
                        </Button>
                    </div>
                </div>
            {/if}
        </div>
    </div>
{/if}
