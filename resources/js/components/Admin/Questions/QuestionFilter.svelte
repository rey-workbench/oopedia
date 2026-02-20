<script>
    import { Search } from "lucide-svelte";

    export let state;
</script>

<div class="flex flex-col md:flex-row gap-6 items-end">
    <div class="flex-1 space-y-2">
        <label
            for="search"
            class="text-[10px] font-bold uppercase text-slate-400 font-poppins ml-2"
            >Pencarian Soal</label
        >
        <div class="relative group">
            <input
                type="text"
                id="search"
                bind:value={state.search}
                on:input={state.handleSearch}
                placeholder="Cari teks soal atau identitas..."
                class="w-full bg-white border-2 border-slate-100 rounded-[2rem] px-8 py-4 text-xs font-bold uppercase tracking-widest text-slate-900 group-hover:border-primary-400 focus:border-primary-600 focus:outline-none transition-all duration-300 shadow-xl shadow-slate-100"
            />
            <Search
                size={20}
                strokeWidth={2.5}
                class="absolute right-8 top-1/2 -translate-y-1/2 text-slate-300 group-hover:text-primary-600 transition-colors"
            />
        </div>
    </div>

    <div class="w-full md:w-64 space-y-2">
        <label
            for="difficulty"
            class="text-[10px] font-bold uppercase text-slate-400 font-poppins ml-2"
            >Tingkat Kesulitan</label
        >
        <select
            id="difficulty"
            bind:value={state.difficulty}
            on:change={() => state.setDifficulty(state.difficulty)}
            class="w-full bg-white border-2 border-slate-100 rounded-[1.5rem] px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-900 focus:border-primary-600 focus:outline-none transition-all cursor-pointer shadow-xl shadow-slate-100"
        >
            <option value="">SEMUA LEVEL</option>
            <option value="beginner">BEGINNER</option>
            <option value="medium">MEDIUM</option>
            <option value="hard">HARD</option>
        </select>
    </div>
</div>
