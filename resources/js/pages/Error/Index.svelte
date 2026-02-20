<script>
    import ErrorCard from "@/components/Error/ErrorCard.svelte";
    import { ErrorState } from "@/states/Error/ErrorState.svelte";

    export let status;

    const state = new ErrorState(status);
</script>

<ErrorCard status={state.status} />
