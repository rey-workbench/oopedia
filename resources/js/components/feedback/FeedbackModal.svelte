<script lang="ts">
    import Modal from '@/components/ui/Modal.svelte';
    import CertificateFeedback from './CertificateFeedback.svelte';
    import AccelerationFeedback from './AccelerationFeedback.svelte';
    import InterventionFeedback from './InterventionFeedback.svelte';
    import BacktrackFeedback from './BacktrackFeedback.svelte';
    import ResultFeedback from './ResultFeedback.svelte';
    import type { QuestionShowState } from '@/states/Mahasiswa/QuizState.svelte';
    import type { CertificateDetails } from './types';

    interface Props {
        state: QuestionShowState;
    }

    let { state }: Props = $props();

    let actionCode = $derived(state.feedbackData?.adaptiveResult?.triggered_rule?.action || null);
    let nextAction = $derived(
        state.feedbackData?.adaptiveResult?.new_state?.next_action_data?.label ||
            (state.feedbackData?.status === 'success' ? 'Soal Berikutnya' : 'Lihat Materi')
    );
    let nextActionType = $derived(
        state.feedbackData?.adaptiveResult?.new_state?.next_action_data?.type || 'question'
    );
    let recommendation = $derived(
        state.feedbackData?.adaptiveResult?.new_state?.recommendation || null
    );
    let certification = $derived(
        state.feedbackData?.adaptiveResult?.new_state?.certification || null
    );
    let xpEarned = $derived(state.feedbackData?.adaptiveResult?.global_xp_earned || 0);
    let streakBonus = $derived(state.feedbackData?.adaptiveResult?.streak_bonus || null);
    let interventionType = $derived(
        state.feedbackData?.adaptiveResult?.new_state?.intervention_type || null
    );

    function getModalVariant():
        | 'certificate'
        | 'acceleration'
        | 'intervention'
        | 'backtrack'
        | 'success'
        | 'error' {
        if (certification) return 'certificate';

        if (
            interventionType?.includes('crisis') ||
            interventionType?.includes('recovery') ||
            interventionType?.includes('persistent') ||
            interventionType?.includes('project_revision') ||
            interventionType?.includes('safety')
        )
            return 'intervention';

        if (['H01', 'H02', 'H03', 'H04'].includes(actionCode || '')) return 'intervention';
        if (actionCode === 'H06') return 'acceleration';
        if (actionCode === 'H07') return 'backtrack';
        if (actionCode === 'H08') return 'certificate';
        if (state.feedbackData?.status === 'success') return 'success';
        return 'error';
    }

    function getCertificateDetails(): CertificateDetails | null {
        if (!certification) return null;

        const certMap: Record<string, CertificateDetails> = {
            gold: {
                color: 'bg-gradient-to-br from-amber-400 via-amber-500 to-amber-600',
                title: 'SERTIFIKAT EMAS',
                badge: '🥇',
                subtitle: 'Master Of Object-Oriented Programming',
            },
            silver: {
                color: 'bg-gradient-to-br from-slate-300 via-slate-400 to-slate-500',
                title: 'SERTIFIKAT PERAK',
                badge: '🥈',
                subtitle: 'Senior Object-Oriented Programmer',
            },
            bronze: {
                color: 'bg-gradient-to-br from-orange-300 via-orange-400 to-orange-500',
                title: 'SERTIFIKAT PERUNGGU',
                badge: '🥉',
                subtitle: 'Junior Object-Oriented Programmer',
            },
        };

        return certMap[certification] || null;
    }

    let variant = $derived(getModalVariant());
    let certDetails = $derived(getCertificateDetails());
</script>

<Modal show={state.showFeedback} maxWidth="2xl" closeable={false}>
    <div class="bg-white/95 backdrop-blur-xl">
        {#if variant === 'certificate' && certDetails}
            <CertificateFeedback
                details={certDetails}
                message={state.feedbackData.message}
                {xpEarned}
                onContinue={() => state.handleNext()}
            />
        {:else if variant === 'acceleration'}
            <AccelerationFeedback
                message={state.feedbackData.message}
                {nextAction}
                {xpEarned}
                onContinue={() => state.handleNext()}
            />
        {:else if variant === 'intervention'}
            <InterventionFeedback
                message={state.feedbackData.message}
                status={state.feedbackData.status}
                {nextAction}
                {recommendation}
                onContinue={() => state.handleNext()}
            />
        {:else if variant === 'backtrack'}
            <BacktrackFeedback
                message={state.feedbackData.message}
                {nextAction}
                {recommendation}
                onContinue={() => state.handleNext()}
            />
        {:else if state.feedbackData}
            <ResultFeedback
                status={state.feedbackData.status}
                message={state.feedbackData.message}
                {nextAction}
                {nextActionType}
                {xpEarned}
                {streakBonus}
                {recommendation}
                onContinue={() => state.handleNext()}
                onTryAgain={() => state.handleTryAgain()}
            />
        {/if}
    </div>
</Modal>
