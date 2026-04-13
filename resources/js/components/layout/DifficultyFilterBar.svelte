<script lang="ts">
    import Button from '@/components/ui/Button.svelte';

    interface Props {
        difficulty?: string;
        onfilter: (difficulty: string) => void;
    }

    let { difficulty = 'all', onfilter }: Props = $props();
</script>

<div class="mt-4 flex flex-wrap gap-2">
    <Button
        variant={difficulty === 'all' ? 'primary' : 'outline'}
        onclick={() => onfilter('all')}
        size="sm"
        class="font-bold tracking-widest uppercase"
    >
        Semua
    </Button>

    <Button
        variant={difficulty === 'beginner' ? 'success' : 'outline'}
        onclick={() => onfilter('beginner')}
        size="sm"
        class="font-bold tracking-widest uppercase {difficulty === 'beginner'
            ? ''
            : 'border-emerald-600/20 text-emerald-600 hover:bg-emerald-50'}"
    >
        Beginner
    </Button>

    <Button
        variant={difficulty === 'medium' ? 'warning' : 'outline'}
        onclick={() => onfilter('medium')}
        size="sm"
        class="font-bold tracking-widest uppercase {difficulty === 'medium'
            ? ''
            : 'border-amber-600/20 text-amber-600 hover:bg-amber-50'}"
    >
        Medium
    </Button>

    <Button
        variant={difficulty === 'hard' ? 'danger' : 'outline'}
        onclick={() => onfilter('hard')}
        size="sm"
        class="font-bold tracking-widest uppercase {difficulty === 'hard'
            ? ''
            : 'border-rose-600/20 text-rose-600 hover:bg-rose-50'}"
    >
        Hard
    </Button>
</div>
