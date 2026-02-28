<script lang="ts">
    import Badge from "@/components/ui/Badge.svelte";
    import Button from "@/components/ui/Button.svelte";
    import type { QuestionShowState } from "@/states/Mahasiswa/QuizState.svelte";
    import {
        Video,
        FileText,
        Code,
        Brain,
        CheckCircle2,
        Trophy,
        XCircle,
        Target,
        RotateCcw,
        ArrowRight,
        Zap,
        ArrowDown,
        Medal,
        BookOpen,
        AlertTriangle,
        TrendingUp,
        Star,
    } from "lucide-svelte";

    let { state }: { state: QuestionShowState } = $props();

    let actionCode = $derived(
        state.feedbackData?.adaptiveResult?.triggered_rule?.action || null,
    );
    let nextAction = $derived(
        state.feedbackData?.adaptiveResult?.new_state?.next_action_data
            ?.label ||
            (state.feedbackData?.status === "success"
                ? "Lanjut"
                : "Lihat Materi"),
    );
    let nextActionType = $derived(
        state.feedbackData?.adaptiveResult?.new_state?.next_action_data?.type ||
            "question",
    );
    let recommendation = $derived(
        state.feedbackData?.adaptiveResult?.new_state?.recommendation || null,
    );
    let modalTriggeredRule = $derived(
        state.feedbackData?.adaptiveResult?.triggered_rule || null,
    );
    let certification = $derived(
        state.feedbackData?.adaptiveResult?.new_state?.certification || null,
    );
    let xpEarned = $derived(
        state.feedbackData?.adaptiveResult?.global_xp_earned || 0,
    );
    let streakBonus = $derived(
        state.feedbackData?.adaptiveResult?.streak_bonus || null,
    );
    let interventionType = $derived(
        state.feedbackData?.adaptiveResult?.new_state?.intervention_type ||
            null,
    );

    function getModalVariant() {
        if (certification) return "certificate";
        if (
            interventionType?.includes("crisis") ||
            interventionType?.includes("recovery") ||
            interventionType?.includes("persistent") ||
            interventionType?.includes("project_revision") ||
            interventionType?.includes("safety")
        )
            return "intervention";

        if (["H01", "H02", "H03", "H04"].includes(actionCode))
            return "intervention";
        if (actionCode === "H06") return "acceleration";
        if (actionCode === "H07") return "backtrack";
        if (actionCode === "H08") return "graduation";
        if (state.feedbackData?.status === "success") return "success";
        return "error";
    }

    function getActionIcon() {
        switch (actionCode) {
            case "H01":
                return Video;
            case "H02":
                return FileText;
            case "H03":
                return Code;
            case "H04":
                return Brain;
            case "H05":
                return CheckCircle2;
            case "H06":
                return Zap;
            case "H07":
                return ArrowDown;
            case "H08":
                return Trophy;
            case "H09":
            case "H10":
            case "H11":
                return Medal;
            default:
                return state.feedbackData?.status === "success"
                    ? CheckCircle2
                    : XCircle;
        }
    }

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
                return state.feedbackData?.status === "success"
                    ? "text-emerald-500"
                    : "text-rose-500";
        }
    }

    function getCertificateDetails() {
        switch (certification) {
            case "gold":
                return {
                    color: "bg-amber-400",
                    title: "SERTIFIKAT EMAS",
                    badge: "🥇",
                    subtitle: "Object-Oriented Architect",
                };
            case "silver":
                return {
                    color: "bg-slate-300",
                    title: "SERTIFIKAT PERAK",
                    badge: "🥈",
                    subtitle: "Object-Oriented Developer",
                };
            case "bronze":
                return {
                    color: "bg-orange-400",
                    title: "SERTIFIKAT PERUNGGU",
                    badge: "🥉",
                    subtitle: "Junior Object-Oriented Programmer",
                };
            default:
                return null;
        }
    }

    let variant = $derived(state.showFeedback ? getModalVariant() : null);
    let IconComponent = $derived(state.showFeedback ? getActionIcon() : null);
    let iconColor = $derived(state.showFeedback ? getIconColor() : null);
    let certDetails = $derived(
        state.showFeedback ? getCertificateDetails() : null,
    );
</script>

{#if state.showFeedback}
    <div
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm transition-all duration-300"
        role="dialog"
        aria-modal="true"
    >
        <div
            class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full mx-4 transform scale-100 transition-all overflow-hidden"
        >
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
                            {state.feedbackData.message}
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
                        onclick={() => state.handleNext()}
                        class="w-full py-4 uppercase tracking-widest text-base font-bold"
                    >
                        Lanjutkan <ArrowRight size={20} class="ml-2" />
                    </Button>
                </div>
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
                            {state.feedbackData.message}
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
                    {#if modalTriggeredRule}
                        <div
                            class="mb-6 p-4 bg-slate-50 rounded-xl text-sm text-slate-600"
                        >
                            <div class="font-bold text-slate-700 mb-1">
                                Adaptive Rule Triggered:
                            </div>
                            <Badge variant="outline" size="sm"
                                >{modalTriggeredRule.id}: {modalTriggeredRule.name}</Badge
                            >
                        </div>
                    {/if}
                    <Button
                        variant="primary"
                        onclick={() => state.handleNext()}
                        class="w-full py-4 uppercase tracking-widest text-base font-bold bg-primary-600 hover:bg-primary-700"
                    >
                        {nextAction}
                        <ArrowRight size={20} class="ml-2" />
                    </Button>
                </div>
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
                            {state.feedbackData.status === "success"
                                ? "Bagus!"
                                : "Perlu Perbaikan"}
                        </h2>
                        <p class="text-lg text-slate-600 mb-6">
                            {state.feedbackData.message}
                        </p>
                    </div>

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

                    {#if modalTriggeredRule}
                        <div
                            class="mb-6 p-4 bg-slate-50 rounded-xl text-sm text-slate-600"
                        >
                            <div class="font-bold text-slate-700 mb-2">
                                Sistem Adaptif:
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <Badge variant="outline" size="sm"
                                        >{modalTriggeredRule.id}</Badge
                                    >
                                    <span>{modalTriggeredRule.name}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <Badge
                                        variant="secondary"
                                        size="sm"
                                        class="bg-primary-100 text-primary-700"
                                        >{modalTriggeredRule.action}</Badge
                                    >
                                    <span class="text-xs text-slate-500"
                                        >Priority: {modalTriggeredRule.priority}</span
                                    >
                                </div>
                            </div>
                        </div>
                    {/if}

                    <Button
                        variant="primary"
                        on:click={() => state.handleNext()}
                        class="w-full py-4 uppercase tracking-widest text-base font-bold"
                    >
                        {nextAction}
                        <ArrowRight size={20} class="ml-2" />
                    </Button>
                </div>
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
                        {state.feedbackData.message}
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

                    {#if modalTriggeredRule}
                        <div
                            class="mb-6 p-4 bg-slate-50 rounded-xl text-sm text-slate-600"
                        >
                            <Badge variant="outline" size="sm"
                                >{modalTriggeredRule.id}: {modalTriggeredRule.name}</Badge
                            >
                        </div>
                    {/if}

                    <Button
                        variant="primary"
                        onclick={() => state.handleNext()}
                        class="w-full py-4 uppercase tracking-widest text-base font-bold bg-rose-600 hover:bg-rose-700"
                    >
                        {nextAction}
                        <ArrowRight size={20} class="ml-2" />
                    </Button>
                </div>
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
                            {state.feedbackData.message}
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
                        onclick={() => state.handleNext()}
                        class="w-full py-4 uppercase tracking-widest text-base font-bold bg-emerald-600 hover:bg-emerald-700"
                    >
                        {nextAction}
                        <ArrowRight size={20} class="ml-2" />
                    </Button>
                </div>
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
                        class={`text-4xl font-bold mb-4 uppercase tracking-widest ${state.feedbackData.status === "success" ? "text-emerald-600" : "text-rose-600"}`}
                    >
                        {state.feedbackData.status === "success"
                            ? "BENAR!"
                            : "SALAH!"}
                    </h2>

                    <p class="text-lg text-slate-600 mb-8">
                        {state.feedbackData.message}
                    </p>

                    {#if state.feedbackData.status === "success"}
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

                    {#if modalTriggeredRule && state.feedbackData.status === "success"}
                        <div
                            class="mb-8 p-4 bg-slate-50 rounded-xl text-sm text-slate-600"
                        >
                            <Badge variant="outline" size="sm"
                                >{modalTriggeredRule.id}: {modalTriggeredRule.name}</Badge
                            >
                        </div>
                    {/if}

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        {#if state.feedbackData.status === "error" && nextActionType !== "material" && !recommendation}
                            <Button
                                variant="outline"
                                onclick={() => state.handleTryAgain()}
                                class="px-8 py-3 uppercase tracking-widest text-sm font-bold"
                            >
                                <RotateCcw size={18} class="mr-2" /> Coba Lagi
                            </Button>
                        {/if}
                        <Button
                            variant="primary"
                            onclick={() => state.handleNext()}
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
