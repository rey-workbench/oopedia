<script lang="ts">
    import { fade } from 'svelte/transition';

    interface Props {
        title: string;
        description: string;
        image: string;
        tag?: string;
        class?: string;
        aspect?: 'square' | 'video' | 'tall' | 'wide';
    }

    let {
        title,
        description,
        image,
        tag,
        class: className = '',
        aspect = 'square',
    }: Props = $props();

    const aspectClasses = {
        square: 'aspect-square',
        video: 'aspect-video',
        tall: 'aspect-3/4',
        wide: 'aspect-16/10',
    };
</script>

<div
    class={`group border-cosmos-border relative overflow-hidden rounded-3xl border-2 bg-[#161616] transition-all duration-700 hover:border-white/20 ${aspectClasses[aspect]} ${className}`}
    in:fade={{ duration: 1000 }}
>
    <div class="absolute inset-0 overflow-hidden">
        <img
            src={image}
            alt={title}
            class="h-full w-full object-cover opacity-60 transition-transform duration-1000 group-hover:scale-110 group-hover:opacity-80"
        />
        <div
            class="absolute inset-0 bg-linear-to-t from-[#0c0c0c] via-[#0c0c0c]/20 to-transparent opacity-80"
        ></div>
    </div>

    <div class="absolute inset-0 flex flex-col justify-end p-8">
        {#if tag}
            <span
                class="mb-3 w-fit rounded-full bg-white/10 px-3 py-1 text-xs font-black tracking-widest text-white/50 uppercase backdrop-blur-md"
            >
                {tag}
            </span>
        {/if}

        <h3
            class="font-serif text-3xl leading-none text-white transition-transform duration-500 group-hover:-translate-y-1"
        >
            {title}
        </h3>

        <p
            class="mt-3 max-w-[80%] text-sm leading-relaxed text-white/40 transition-all duration-500 group-hover:text-white/60"
        >
            {description}
        </p>

        <div
            class="bg-primary-500/10 group-hover:bg-primary-500/20 absolute -right-20 -bottom-20 h-40 w-40 rounded-full blur-[80px] transition-all duration-700"
        ></div>
    </div>
</div>
