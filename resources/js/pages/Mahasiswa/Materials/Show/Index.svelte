<script>
    import App from "../../../../layouts/App.svelte";
    import PageHeader from "../../../../components/ui/PageHeader.svelte";
    import Card from "../../../../components/ui/Card.svelte";
    import Button from "../../../../components/ui/Button.svelte";
    import { Link, page } from "@inertiajs/svelte";
    import { ArrowLeft, BookOpen, Layers, Puzzle, Info, ArrowRight } from "lucide-svelte";
    import {
        getGradientClass,
        getTextClass,
        getBgClass,
        getShadowClass,
        getIcon,
        getBadgeLabel,
    } from "../../../../utils/contentTypeStyles";

    export let material = {};

    // Handling subMaterials properly if it comes as an array or object
    $: subMaterials = material.subMaterials || material.sub_materials || [];
    
    // Check if user was redirected from adaptive system
    $: fromAdaptive = $page.props?.flash?.from_adaptive || false;
</script>

<App title={material?.title || "Material"}>
    <div class="space-y-12">
        <!-- Header Section -->
        <PageHeader
            title={material?.title || "Loading..."}
            subtitle="Kuasai konsep fondasi hingga tingkat lanjut Pemrograman Berorientasi Objek."
        >
            <div slot="actions">
                <Button
                    href="/mahasiswa/materials"
                    variant="ghost"
                    icon={ArrowLeft}
                >
                    Kembali ke Materi
                </Button>
            </div>

            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 shadow-inner"
                    >
                        <BookOpen size={16} />
                    </div>
                    <span
                        class="text-[10px] font-bold uppercase tracking-widest text-slate-500"
                    >
                        {material?.creator?.name || "Admin System"}
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 shadow-inner"
                    >
                        <Layers size={16} />
                    </div>
                    <span
                        class="text-[10px] font-bold uppercase tracking-widest text-slate-500"
                    >
                        {subMaterials.length} Sub-Materi
                    </span>
                </div>
            </div>
        </PageHeader>

        <!-- Adaptive System Alert -->
        {#if fromAdaptive}
            <Card class="border-l-4 border-blue-500 bg-blue-50">
                <div class="flex items-start gap-4">
                    <div
                        class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center shrink-0"
                    >
                        <Info size={24} class="text-blue-600" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-blue-900 mb-1">
                            Rekomendasi Sistem Adaptif
                        </h3>
                        <p class="text-sm text-blue-700 leading-relaxed">
                            Sistem merekomendasikan Anda untuk mengulas kembali materi ini. 
                            Pilih sub-materi yang ingin dipelajari untuk memperkuat pemahaman.
                        </p>
                    </div>
                </div>
            </Card>
        {/if}

        <!-- Sub-Materials Grid -->
        <div>
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2
                        class="text-3xl font-bold text-slate-900 tracking-widest mb-2"
                    >
                        Daftar Sub-Materi
                    </h2>
                    <p class="text-slate-500 font-medium">
                        Pilih sub-materi untuk memulai pembelajaran
                    </p>
                </div>
            </div>

            {#if subMaterials.length === 0}
                <Card class="p-20 text-center">
                    <div
                        class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6"
                    >
                        <BookOpen size={48} class="text-slate-200" />
                    </div>
                    <h3
                        class="text-xl font-bold tracking-widest text-slate-900 mb-2"
                    >
                        Belum Ada Sub-Materi
                    </h3>
                    <p class="text-slate-400 text-sm max-w-xs mx-auto mb-6">
                        Sub-materi untuk topik ini sedang dalam pengembangan.
                    </p>
                    <Button
                        href="/mahasiswa/materials"
                        variant="outline"
                        icon={ArrowLeft}
                        class="mx-auto"
                    >
                        Kembali ke Daftar Materi
                    </Button>
                </Card>
            {:else}
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
                >
                    {#each subMaterials as subMaterial (subMaterial.id)}
                        <Card
                            padding="p-0"
                            class="group hover:shadow-2xl transition-all duration-300 overflow-hidden"
                        >
                            <!-- Header with Icon -->
                            <div
                                class={`relative h-48 bg-gradient-to-br ${getGradientClass(subMaterial.jenis_konten)} flex items-center justify-center shrink-0`}
                            >
                                <div class="absolute inset-0 bg-black/10"></div>
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"
                                ></div>

                                <!-- Center Icon -->
                                <div class="relative z-10">
                                    <svelte:component
                                        this={getIcon(subMaterial.jenis_konten)}
                                        size={64}
                                        class="text-white/20 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500"
                                    />
                                </div>

                                <!-- Order Badge (Top Left) -->
                                <div
                                    class="absolute top-4 left-4 w-10 h-10 bg-white rounded-xl shadow-lg flex items-center justify-center"
                                >
                                    <span
                                        class={`text-lg font-bold ${getTextClass(subMaterial.jenis_konten)}`}
                                        >{subMaterial.order}</span
                                    >
                                </div>

                                <!-- Bottom Badges -->
                                <div
                                    class="absolute bottom-5 left-5 right-5 flex justify-between items-center"
                                >
                                    <div
                                        class="px-3 py-1.5 bg-white/10 backdrop-blur-md rounded-xl text-white text-[9px] font-bold uppercase tracking-widest border border-white/20"
                                    >
                                        {getBadgeLabel(
                                            subMaterial.jenis_konten,
                                        )}
                                    </div>
                                    <div
                                        class={`flex items-center gap-2 px-3 py-1.5 ${getBgClass(subMaterial.jenis_konten)} rounded-xl text-white text-[9px] font-bold uppercase tracking-widest shadow-xl ${getShadowClass(subMaterial.jenis_konten)}`}
                                    >
                                        <Puzzle size={14} />
                                        {subMaterial.questions
                                            ? subMaterial.questions.length
                                            : 0} Soal
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-6 flex-1 flex flex-col">
                                <div
                                    class="min-h-[3.5rem] mb-3 flex items-start"
                                >
                                    <h3
                                        class={`text-xl font-bold text-slate-900 group-hover:${getTextClass(subMaterial.jenis_konten)} transition-colors line-clamp-2`}
                                    >
                                        {subMaterial.title}
                                    </h3>
                                </div>

                                <div class="min-h-[4.5rem] mb-6">
                                    <p
                                        class="text-sm text-slate-600 line-clamp-3 leading-relaxed"
                                    >
                                        {subMaterial.content || ""}
                                    </p>
                                </div>

                                <div class="mt-auto">
                                    <Button
                                        href={`/mahasiswa/materials/${material.id}/submaterials/${subMaterial.id}`}
                                        variant="primary"
                                        class="w-full"
                                        icon={BookOpen}
                                    >
                                        Lihat Materi
                                    </Button>
                                </div>
                            </div>
                        </Card>
                    {/each}
                </div>
            {/if}
        </div>

        <!-- Material Content Section (Optional) -->
        {#if material.content}
            <Card>
                <div class="prose prose-slate max-w-none">
                    <h3 class="text-2xl font-bold text-slate-900 mb-4">
                        Tentang Materi Ini
                    </h3>
                    <div class="text-slate-600 leading-relaxed">
                        <!-- Render HTML content safely needed? Assuming material.content is safe HTML or plain text. 
                 If simple text, use white-space: pre-wrap. If HTML, use {@html}. 
                 Blade used {!! nl2br(e($material->content)) !!} which escapes then converts nl to br.
                 So it's safe text with line breaks. -->
                        {#each (material.content || "").split("\n") as paragraph}
                            <p class="mb-2">{paragraph}</p>
                        {/each}
                    </div>
                </div>
            </Card>
        {/if}
    </div>
</App>
