<script>
    import App from "@/layouts/App.svelte";
    import Card from "@/components/ui/Card.svelte";
    import Button from "@/components/ui/Button.svelte";
    import Alert from "@/components/ui/Alert.svelte";
    import { Book, ArrowLeft } from "lucide-svelte";
    import ReviewSidebar from "@/components/Mahasiswa/Materials/Questions/ReviewSidebar.svelte";
    import ReviewQuestionList from "@/components/Mahasiswa/Materials/Questions/ReviewQuestionList.svelte";
    import { ReviewState } from "@/states/Mahasiswa/QuizState.svelte";

    export let material = {};
    export let materials = [];
    export let questions = [];
    export let difficulty = "all";

    const state = new ReviewState(material, materials, questions, difficulty);
</script>

<App title={`Review Soal - ${state.material.title}`}>
    <div class="container-fluid py-4 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar: Materials List -->
            <div class="lg:col-span-1">
                <ReviewSidebar
                    materials={state.materials}
                    currentMaterialId={state.material.id}
                />
            </div>

            <!-- Main Content: Questions Review -->
            <div class="lg:col-span-3">
                <Card>
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-slate-800 mb-2">
                            Review Soal {state.difficulty !== "all"
                                ? state.difficulty.charAt(0).toUpperCase() +
                                  state.difficulty.slice(1)
                                : "Semua Tingkat"}
                        </h3>
                        <p class="text-slate-600">
                            Berikut adalah review dari soal-soal yang telah Anda
                            kerjakan.
                        </p>

                        <div class="flex flex-wrap gap-2 mt-4">
                            <Button
                                variant={state.difficulty === "all"
                                    ? "primary"
                                    : "outline"}
                                on:click={() => state.filterDifficulty("all")}
                                size="sm"
                            >
                                Semua
                            </Button>

                            <Button
                                variant={state.difficulty === "beginner"
                                    ? "success"
                                    : "outline"}
                                on:click={() =>
                                    state.filterDifficulty("beginner")}
                                size="sm"
                                class={state.difficulty === "beginner"
                                    ? ""
                                    : "text-emerald-600 border-emerald-600 hover:bg-emerald-50"}
                            >
                                Beginner
                            </Button>

                            <Button
                                variant={state.difficulty === "medium"
                                    ? "warning"
                                    : "outline"}
                                on:click={() =>
                                    state.filterDifficulty("medium")}
                                size="sm"
                                class={state.difficulty === "medium"
                                    ? ""
                                    : "text-amber-600 border-amber-600 hover:bg-amber-50"}
                            >
                                Medium
                            </Button>

                            <Button
                                variant={state.difficulty === "hard"
                                    ? "danger"
                                    : "outline"}
                                on:click={() => state.filterDifficulty("hard")}
                                size="sm"
                                class={state.difficulty === "hard"
                                    ? ""
                                    : "text-rose-600 border-rose-600 hover:bg-rose-50"}
                            >
                                Hard
                            </Button>
                        </div>
                    </div>

                    <ReviewQuestionList questions={state.questions} />
                </Card>
            </div>
        </div>
    </div>
</App>
