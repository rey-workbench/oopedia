<script>
    import Card from "@/components/ui/Card.svelte";
    import Badge from "@/components/ui/Badge.svelte";
    import { Cpu } from "lucide-svelte";

    export let user;
    export let survey;
</script>

<Card class="border-slate-100 shadow-2xl overflow-hidden rounded-[2.5rem]">
    <div class="bg-slate-900 -mx-8 -mt-8 p-10 text-center space-y-4">
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
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <p
                    class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-1"
                >
                    Segmentasi
                </p>
                <p class="text-xs font-bold text-slate-900">
                    {survey.class}
                </p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <p
                    class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-1"
                >
                    Integritas
                </p>
                <Badge variant="success" size="xs">VALID DATA</Badge>
            </div>
        </div>

        <div class="bg-primary-50/50 p-6 rounded-3xl border border-primary-100">
            <p
                class="text-[9px] font-bold uppercase tracking-widest text-primary-600 mb-3"
            >
                Timestamp Akusisi
            </p>
            <div
                class="flex items-center gap-3 text-sm font-bold text-primary-900"
            >
                <Cpu size={14} />
                {new Date(survey.created_at).toLocaleString("id-ID", {
                    day: "numeric",
                    month: "long",
                    year: "numeric",
                    hour: "2-digit",
                    minute: "2-digit",
                })}
            </div>
        </div>
    </div>
</Card>
