<script>
    import App from "../../../../layouts/App.svelte";
    import PageHeader from "../../../../components/ui/PageHeader.svelte";
    import Card from "../../../../components/ui/Card.svelte";
    import Button from "../../../../components/ui/Button.svelte";
    import Badge from "../../../../components/ui/Badge.svelte";
    import ProgressBar from "../../../../components/ui/ProgressBar.svelte";
    import { ArrowLeft, Cpu, MessageSquare, Lightbulb } from "lucide-svelte";

    export let user;
    export let survey;

    const aspects = [
        {
            name: "annoying_enjoyable",
            left: "Menyebalkan",
            right: "Menyenangkan",
        },
        {
            name: "not_understandable_understandable",
            left: "Tidak dipahami",
            right: "Dapat dipahami",
        },
        { name: "creative_dull", left: "Kreatif", right: "Monoton" },
        { name: "easy_difficult", left: "Mudah", right: "Sulit" },
        { name: "valuable_inferior", left: "Bermanfaat", right: "Inferior" },
        { name: "boring_exciting", left: "Membosankan", right: "Menarik" },
        {
            name: "not_interesting_interesting",
            left: "Tidak menarik",
            right: "Menarik",
        },
        {
            name: "unpredictable_predictable",
            left: "Unpredictable",
            right: "Predictable",
        },
        { name: "fast_slow", left: "Cepat", right: "Lambat" },
        {
            name: "inventive_conventional",
            left: "Inovatif",
            right: "Konvensional",
        },
        {
            name: "obstructive_supportive",
            left: "Menghambat",
            right: "Mendukung",
        },
        { name: "good_bad", left: "Baik", right: "Buruk" },
        { name: "complicated_easy", left: "Rumit", right: "Sederhana" },
        { name: "unlikable_pleasing", left: "Unlikable", right: "Pleasing" },
        { name: "usual_leading_edge", left: "Biasa saja", right: "Terdepan" },
        { name: "unpleasant_pleasant", left: "Unpleasant", right: "Pleasant" },
        { name: "secure_not_secure", left: "Aman", right: "Tidak aman" },
        {
            name: "motivating_demotivating",
            left: "Memotivasi",
            right: "Demotivating",
        },
        {
            name: "meets_expectations_does_not_meet",
            left: "Meets Expect.",
            right: "Doesn't Meet",
        },
        {
            name: "inefficient_efficient",
            left: "Tidak efisien",
            right: "Efisien",
        },
        { name: "clear_confusing", left: "Jelas", right: "Membingungkan" },
        {
            name: "impractical_practical",
            left: "Tidak praktis",
            right: "Praktis",
        },
        {
            name: "organized_cluttered",
            left: "Terorganisir",
            right: "Berantakan",
        },
        {
            name: "attractive_unattractive",
            left: "Menarik",
            right: "Tidak menarik",
        },
        { name: "friendly_unfriendly", left: "Ramah", right: "Tidak ramah" },
        {
            name: "conservative_innovative",
            left: "Konservatif",
            right: "Inovatif",
        },
    ];

    const dimensions = {
        "Daya Tarik":
            (survey.annoying_enjoyable +
                survey.good_bad +
                survey.unlikable_pleasing +
                survey.unpleasant_pleasant +
                survey.attractive_unattractive +
                survey.friendly_unfriendly) /
            6,
        Kejelasan:
            (survey.not_understandable_understandable +
                survey.easy_difficult +
                survey.complicated_easy +
                survey.clear_confusing) /
            4,
        Efisiensi:
            (survey.fast_slow +
                survey.inefficient_efficient +
                survey.impractical_practical +
                survey.organized_cluttered) /
            4,
        Keandalan:
            (survey.unpredictable_predictable +
                survey.obstructive_supportive +
                survey.secure_not_secure +
                survey.meets_expectations_does_not_meet) /
            4,
        Stimulasi:
            (survey.valuable_inferior +
                survey.boring_exciting +
                survey.not_interesting_interesting +
                survey.motivating_demotivating) /
            4,
        Kebaruan:
            (survey.creative_dull +
                survey.inventive_conventional +
                survey.usual_leading_edge +
                survey.conservative_innovative) /
            4,
    };
</script>

<App title={`Detail Survey: ${user.name}`}>
    <div class="max-w-6xl mx-auto space-y-12 pb-20">
        <PageHeader
            title="Profil Forensik UEQ"
            subtitle={`Eksplorasi mendalam metrik pengalaman sistem untuk pengguna ${user.name}.`}
        >
            <div slot="actions">
                <Button
                    href="/admin/ueq-survey"
                    variant="ghost"
                    icon={ArrowLeft}>KEMBALI KE DAFTAR</Button
                >
            </div>
        </PageHeader>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="space-y-8">
                <Card
                    class="border-slate-100 shadow-2xl overflow-hidden rounded-[2.5rem]"
                >
                    <div
                        class="bg-slate-900 -mx-8 -mt-8 p-10 text-center space-y-4"
                    >
                        <div
                            class="w-24 h-24 rounded-[2.5rem] bg-white mx-auto flex items-center justify-center text-3xl font-bold text-slate-900 shadow-2xl"
                        >
                            {user.name.charAt(0)}
                        </div>
                        <div>
                            <h4
                                class="text-xl font-bold tracking-widest text-white uppercase mb-1"
                            >
                                {user.name}
                            </h4>
                            <p
                                class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.3em] font-poppins"
                            >
                                {survey.nim || "MEMBER ID UNDEFINED"}
                            </p>
                        </div>
                    </div>

                    <div class="pt-8 space-y-8">
                        <div class="grid grid-cols-2 gap-4">
                            <div
                                class="bg-slate-50 p-4 rounded-2xl border border-slate-100"
                            >
                                <p
                                    class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-1"
                                >
                                    Segmentasi
                                </p>
                                <p class="text-xs font-bold text-slate-900">
                                    {survey.class}
                                </p>
                            </div>
                            <div
                                class="bg-slate-50 p-4 rounded-2xl border border-slate-100"
                            >
                                <p
                                    class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-1"
                                >
                                    Integritas
                                </p>
                                <Badge variant="success" size="xs"
                                    >VALID DATA</Badge
                                >
                            </div>
                        </div>

                        <div
                            class="bg-primary-50/50 p-6 rounded-3xl border border-primary-100"
                        >
                            <p
                                class="text-[9px] font-bold uppercase tracking-widest text-primary-600 mb-3"
                            >
                                Timestamp Akusisi
                            </p>
                            <div
                                class="flex items-center gap-3 text-sm font-bold text-primary-900"
                            >
                                <Cpu size={14} />
                                {new Date(survey.created_at).toLocaleString(
                                    "id-ID",
                                    {
                                        day: "numeric",
                                        month: "long",
                                        year: "numeric",
                                        hour: "2-digit",
                                        minute: "2-digit",
                                    },
                                )}
                            </div>
                        </div>
                    </div>
                </Card>

                <Card
                    padding="p-0"
                    class="border-slate-100 shadow-2xl overflow-hidden rounded-[2.5rem]"
                >
                    <div
                        slot="header"
                        class="px-8 py-6 border-b border-slate-50 bg-slate-50/50"
                    >
                        <h6
                            class="mb-0 font-bold uppercase tracking-widest text-[10px] text-slate-400"
                        >
                            Agregasi Dimensi
                        </h6>
                    </div>
                    <div class="divide-y divide-slate-50">
                        {#each Object.entries(dimensions) as [name, score]}
                            <div
                                class="px-8 py-6 flex flex-col gap-3 group hover:bg-slate-50 transition-colors"
                            >
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-[10px] font-bold text-slate-500 uppercase tracking-widest"
                                        >{name}</span
                                    >
                                    <span
                                        class="text-xs font-bold text-slate-900"
                                        >{score.toFixed(2)}</span
                                    >
                                </div>
                                <ProgressBar
                                    value={(score / 7) * 100}
                                    size="xs"
                                    showPercentage={false}
                                    variant={score >= 5
                                        ? "success"
                                        : score >= 3
                                          ? "warning"
                                          : "danger"}
                                />
                            </div>
                        {/each}
                    </div>
                </Card>
            </div>

            <div class="lg:col-span-2 space-y-12">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <Card
                        class="border-amber-100 bg-amber-50/20 rounded-[2.5rem] shadow-xl"
                    >
                        <h6
                            class="text-[10px] font-bold uppercase tracking-widest text-amber-600 mb-4 flex items-center gap-2"
                        >
                            <MessageSquare size={14} /> Feedback Subjektif
                        </h6>
                        <p
                            class="text-xs font-bold text-slate-700 leading-relaxed italic"
                        >
                            "{survey.comments ||
                                "Responden tidak meninggalkan catatan kualitatif."}"
                        </p>
                    </Card>
                    <Card
                        class="border-primary-100 bg-primary-50/20 rounded-[2.5rem] shadow-xl"
                    >
                        <h6
                            class="text-[10px] font-bold uppercase tracking-widest text-primary-600 mb-4 flex items-center gap-2"
                        >
                            <Lightbulb size={14} /> Rekomendasi Fitur
                        </h6>
                        <p
                            class="text-xs font-bold text-slate-700 leading-relaxed uppercase"
                        >
                            "{survey.suggestions ||
                                "Tidak ada proposal perbaikan dari responden."}"
                        </p>
                    </Card>
                </div>

                <Card
                    padding="p-0"
                    class="border-slate-100 shadow-2xl overflow-hidden rounded-[2.5rem]"
                >
                    <div
                        slot="header"
                        class="px-8 py-6 border-b border-slate-50"
                    >
                        <h6
                            class="mb-0 font-bold uppercase tracking-widest text-[10px] text-slate-400"
                        >
                            Matriks Skor Psikometrik
                        </h6>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th
                                        class="px-8 py-6 text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                        >Aspek Evaluasi</th
                                    >
                                    <th
                                        class="px-8 py-6 text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                        >Peta Skalar (1-7)</th
                                    >
                                    <th
                                        class="px-8 py-6 text-right text-[10px] font-bold text-slate-400 uppercase tracking-widest"
                                        >Rentang Semantik</th
                                    >
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                {#each aspects as aspect}
                                    {@const val = survey[aspect.name]}
                                    <tr
                                        class="group hover:bg-slate-50 transition-colors"
                                    >
                                        <td class="px-8 py-6">
                                            <span
                                                class="text-[10px] font-bold text-slate-900 uppercase tracking-widest"
                                                >{aspect.name.replace(
                                                    /_/g,
                                                    " ",
                                                )}</span
                                            >
                                        </td>
                                        <td class="px-8 py-6">
                                            <div
                                                class="flex items-center justify-center gap-1"
                                            >
                                                {#each Array(7) as _, i}
                                                    <div
                                                        class={`w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-bold transition-all
                            ${val == i + 1 ? "bg-primary-600 text-white shadow-lg shadow-primary-900/20 scale-110" : "bg-slate-100 text-slate-300"}`}
                                                    >
                                                        {i + 1}
                                                    </div>
                                                {/each}
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <div
                                                class="flex flex-col items-end"
                                            >
                                                <span
                                                    class="text-[8px] font-bold uppercase text-slate-300 mb-1"
                                                    >Semantik Map:</span
                                                >
                                                <div
                                                    class="text-[10px] font-bold text-slate-600 flex items-center gap-2"
                                                >
                                                    <span
                                                        class={val <= 3
                                                            ? "text-primary-600"
                                                            : ""}
                                                        >{aspect.left}</span
                                                    >
                                                    <span
                                                        class="w-px h-3 bg-slate-200"
                                                    ></span>
                                                    <span
                                                        class={val >= 5
                                                            ? "text-primary-600"
                                                            : ""}
                                                        >{aspect.right}</span
                                                    >
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                {/each}
                            </tbody>
                        </table>
                    </div>
                </Card>
            </div>
        </div>
    </div>
</App>
