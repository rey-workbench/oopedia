<script>
    import App from "../../../../layouts/App.svelte";
    import Card from "../../../../components/ui/Card.svelte";
    import Button from "../../../../components/ui/Button.svelte";
    import Badge from "../../../../components/ui/Badge.svelte";
    import Alert from "../../../../components/ui/Alert.svelte";
    import { Link, router } from "@inertiajs/svelte";
    import {
        Book,
        FileText,
        HelpCircle,
        List,
        Check,
        X,
        Lightbulb,
        ArrowLeft,
    } from "lucide-svelte";

    export let material = {};
    export let materials = [];
    export let questions = []; // This seems to be a collection of questions with answers loaded
    export let difficulty = "all";
    export let isGuest = false;

    function filterDifficulty(d) {
        router.get(
            `/mahasiswa/materials/${material.id}/questions/review`,
            { difficulty: d },
            {
                preserveState: true,
                preserveScroll: true,
                only: ["questions", "difficulty"],
            },
        );
    }
</script>

<App title={`Review Soal - ${material.title}`}>
    <div class="container-fluid py-4 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar: Materials List -->
            <div class="lg:col-span-1">
                <Card class="sticky top-4">
                    <div class="mb-4">
                        <h5 class="font-bold text-lg text-slate-900">
                            <Book size={18} class="mr-2" />Daftar Materi
                        </h5>
                    </div>
                    <ul class="space-y-2">
                        {#each materials as m (m.id)}
                            <li>
                                <Link
                                    href={`/mahasiswa/materials/${m.id}`}
                                    class={`block p-3 rounded-lg transition-colors ${m.id === material.id ? "bg-blue-600 text-white" : "text-slate-700 hover:bg-slate-100"}`}
                                >
                                    <FileText size={16} class="mr-2" />{m.title}
                                </Link>
                            </li>
                        {/each}
                    </ul>
                </Card>
            </div>

            <!-- Main Content: Questions Review -->
            <div class="lg:col-span-3">
                <Card>
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-slate-800 mb-2">
                            Review Soal {difficulty !== "all"
                                ? difficulty.charAt(0).toUpperCase() +
                                  difficulty.slice(1)
                                : "Semua Tingkat"}
                        </h3>
                        <p class="text-slate-600">
                            Berikut adalah review dari soal-soal yang telah Anda
                            kerjakan.
                        </p>

                        <div class="flex flex-wrap gap-2 mt-4">
                            <Button
                                variant={difficulty === "all"
                                    ? "primary"
                                    : "outline"}
                                on:click={() => filterDifficulty("all")}
                                size="sm"
                            >
                                Semua
                            </Button>

                            <Button
                                variant={difficulty === "beginner"
                                    ? "success"
                                    : "outline"}
                                on:click={() => filterDifficulty("beginner")}
                                size="sm"
                                class={difficulty === "beginner"
                                    ? ""
                                    : "text-emerald-600 border-emerald-600 hover:bg-emerald-50"}
                            >
                                Beginner
                            </Button>

                            <Button
                                variant={difficulty === "medium"
                                    ? "warning"
                                    : "outline"}
                                on:click={() => filterDifficulty("medium")}
                                size="sm"
                                class={difficulty === "medium"
                                    ? ""
                                    : "text-amber-600 border-amber-600 hover:bg-amber-50"}
                            >
                                Medium
                            </Button>

                            <Button
                                variant={difficulty === "advanced"
                                    ? "danger"
                                    : "outline"}
                                on:click={() => filterDifficulty("advanced")}
                                size="sm"
                                class={difficulty === "advanced"
                                    ? ""
                                    : "text-rose-600 border-rose-600 hover:bg-rose-50"}
                            >
                                Advanced
                            </Button>
                        </div>
                    </div>

                    {#if questions.length > 0}
                        <div class="space-y-6">
                            {#each questions as question, index (question.id)}
                                <div
                                    class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow"
                                >
                                    <div
                                        class="flex justify-between items-center mb-6"
                                    >
                                        <span
                                            class="inline-flex items-center gap-2 font-bold text-slate-700"
                                        >
                                            <div
                                                class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm"
                                            >
                                                {index + 1}
                                            </div>
                                            Soal dari {questions.length}
                                        </span>
                                        <Badge
                                            variant={question.difficulty ===
                                            "beginner"
                                                ? "success"
                                                : question.difficulty ===
                                                    "medium"
                                                  ? "warning"
                                                  : "danger"}
                                        >
                                            {question.difficulty
                                                .charAt(0)
                                                .toUpperCase() +
                                                question.difficulty.slice(1)}
                                        </Badge>
                                    </div>

                                    <div class="space-y-6">
                                        <!-- Question Text -->
                                        <div>
                                            <h5
                                                class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2"
                                            >
                                                <HelpCircle
                                                    size={16}
                                                    class="text-blue-400"
                                                />
                                                Pertanyaan
                                            </h5>
                                            <div
                                                class="p-5 bg-slate-50 rounded-xl text-slate-800 leading-relaxed border border-slate-100"
                                            >
                                                {@html question.question_text}
                                            </div>
                                        </div>

                                        <!-- Answers -->
                                        <div>
                                            <h5
                                                class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2"
                                            >
                                                <List
                                                    size={16}
                                                    class="text-indigo-400"
                                                />
                                                Pilihan Jawaban
                                            </h5>
                                            <div class="grid grid-cols-1 gap-3">
                                                {#each question.answers as answer}
                                                    <div
                                                        class={`p-4 rounded-xl flex items-start gap-4 transition-all ${answer.is_correct ? "bg-emerald-50 border-2 border-emerald-200 shadow-sm" : "bg-white border-2 border-slate-50 text-slate-500"}`}
                                                    >
                                                        {#if answer.is_correct}
                                                            <div
                                                                class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center shrink-0 mt-0.5"
                                                            >
                                                                <Check
                                                                    size={14}
                                                                    class="text-white"
                                                                />
                                                            </div>
                                                        {:else}
                                                            <div
                                                                class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center shrink-0 mt-0.5"
                                                            >
                                                                <X
                                                                    size={14}
                                                                    class="text-slate-300"
                                                                />
                                                            </div>
                                                        {/if}
                                                        <div
                                                            class="flex-1 font-medium"
                                                        >
                                                            {answer.answer_text}
                                                        </div>
                                                    </div>
                                                    {#if answer.is_correct && answer.explanation}
                                                        <div
                                                            class="mt-2 p-5 bg-blue-50/50 border-l-4 border-blue-400 rounded-r-xl"
                                                        >
                                                            <div
                                                                class="flex items-center gap-2 font-bold text-blue-900 mb-1"
                                                            >
                                                                <Lightbulb
                                                                    size={16}
                                                                    class="text-blue-500"
                                                                />
                                                                Penjelasan:
                                                            </div>
                                                            <div
                                                                class="text-blue-800 text-sm leading-relaxed"
                                                            >
                                                                {@html answer.explanation}
                                                            </div>
                                                        </div>
                                                    {/if}
                                                {/each}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {/each}
                        </div>
                    {:else}
                        <Alert variant="info" icon="info-circle" title="Info">
                            Tidak ada soal yang tersedia untuk ditampilkan.
                        </Alert>
                    {/if}

                    <!-- Footer Actions -->
                    <div class="mt-6 flex gap-3 justify-center">
                        <Button
                            href={`/mahasiswa/materials/${material.id}/questions?difficulty=${difficulty}`}
                            variant="primary"
                            icon={ArrowLeft}
                        >
                            Kembali ke Soal
                        </Button>

                        <Button
                            href={`/mahasiswa/materials/${material.id}`}
                            variant="secondary"
                            icon={Book}
                        >
                            Kembali ke Materi
                        </Button>
                    </div>
                </Card>
            </div>
        </div>
    </div>
</App>
