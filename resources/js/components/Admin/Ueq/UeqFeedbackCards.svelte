<script>
    import Card from "@/components/ui/Card.svelte";
    import { MessageSquare, Lightbulb } from "lucide-svelte";

    export let survey;
</script>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <Card class="border-amber-100 bg-amber-50/20 shadow-xl">
        <h6
            class="text-[10px] font-bold uppercase tracking-widest text-amber-600 mb-4 flex items-center gap-2"
        >
            <MessageSquare size={14} /> Feedback Subjektif
        </h6>
        <p class="text-xs font-bold text-slate-700 leading-relaxed italic">
            "{survey.comments ||
                "Responden tidak meninggalkan catatan kualitatif."}"
        </p>
    </Card>
    <Card class="border-primary-100 bg-primary-50/20 shadow-xl">
        <h6
            class="text-[10px] font-bold uppercase tracking-widest text-primary-600 mb-4 flex items-center gap-2"
        >
            <Lightbulb size={14} /> Rekomendasi Fitur
        </h6>
        <p class="text-xs font-bold text-slate-700 leading-relaxed uppercase">
            "{survey.suggestions ||
                "Tidak ada proposal perbaikan dari responden."}"
        </p>
    </Card>
</div>
