<script lang="ts">
    import { Settings2 } from 'lucide-svelte';
    import Input from '@/components/ui/Input.svelte';

    let { form } = $props<{ form: any }>();
</script>

<div class="rounded-3xl border-2 border-b-6 border-slate-200 bg-white p-7 shadow-sm">
    <div class="flex items-center gap-3 mb-6">
        <div class="h-8 w-8 flex items-center justify-center rounded-lg bg-amber-50 text-amber-500 border-2 border-slate-100">
            <Settings2 size={16} />
        </div>
        <h3 class="text-[10px] font-black tracking-[0.2em] text-amber-500 uppercase">Rule Configuration</h3>
    </div>
    
    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-8">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Rule Master Title</span>
            <input 
                type="text" 
                bind:value={form.name} 
                placeholder="Contoh: Optimasi Pembelajaran Siswa Berprestasi"
                class="w-full text-lg font-black text-primary-500 placeholder:text-slate-200 focus:outline-none"
            />
        </div>
        <div class="col-span-4">
            <Input label="Priority Level" variant="white" type="number" bind:value={form.priority} error={form.errors.priority} className="font-mono text-sm py-2" placeholder="1-100" />
        </div>
    </div>
</div>
