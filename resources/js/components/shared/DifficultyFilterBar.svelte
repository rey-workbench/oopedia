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
    >
        Semua
    </Button>

    <Button
        variant={difficulty === 'beginner' ? 'success' : 'outline'}
        onclick={() => onfilter('beginner')}
        size="sm"
        class={difficulty === 'beginner'
            ? ''
            : 'border-emerald-600 text-emerald-600 hover:bg-emerald-50'}
    >
        Beginner
    </Button>

    <Button
        variant={difficulty === 'medium' ? 'warning' : 'outline'}
        onclick={() => onfilter('medium')}
        size="sm"
        class={difficulty === 'medium' ? '' : 'border-amber-600 text-amber-600 hover:bg-amber-50'}
    >
        Medium
    </Button>

    <Button
        variant={difficulty === 'hard' ? 'danger' : 'outline'}
        onclick={() => onfilter('hard')}
        size="sm"
        class={difficulty === 'hard' ? '' : 'border-rose-600 text-rose-600 hover:bg-rose-50'}
    >
        Hard
    </Button>
</div>
