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
                color: 'bg-amber-400',
                title: 'SERTIFIKAT EMAS',
                badge: '🥇',
                subtitle: 'Master Of Object-Oriented Programming',
            },
            silver: {
                color: 'bg-slate-400',
                title: 'SERTIFIKAT PERAK',
                badge: '🥈',
                subtitle: 'Senior Object-Oriented Programmer',
            },
            bronze: {
                color: 'bg-orange-400',
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

{#if state.showFeedback}
    {#if variant === 'certificate' && certDetails}
        <Modal show={true} maxWidth="2xl" closeable={false}>
            <div id="certificate-feedback-modal">
                <CertificateFeedback
                    details={certDetails}
                    message={state.feedbackData.message}
                    {xpEarned}
                    onContinue={() => state.handleNext()}
                />
            </div>
        </Modal>
    {:else if variant === 'acceleration'}
        <Modal show={true} maxWidth="2xl" closeable={false}>
            <div id="acceleration-feedback-modal">
                <AccelerationFeedback
                    message={state.feedbackData.message}
                    {nextAction}
                    {xpEarned}
                    onContinue={() => state.handleNext()}
                />
            </div>
        </Modal>
    {:else if variant === 'intervention'}
        <Modal show={true} maxWidth="2xl" closeable={false}>
            <div id="intervention-feedback-modal">
                <InterventionFeedback
                    message={state.feedbackData.message}
                    status={state.feedbackData.status as 'success' | 'wrong'}
                    {nextAction}
                    {recommendation}
                    onContinue={() => state.handleNext()}
                />
            </div>
        </Modal>
    {:else if variant === 'backtrack'}
        <Modal show={true} maxWidth="2xl" closeable={false}>
            <div id="backtrack-feedback-modal">
                <BacktrackFeedback
                    message={state.feedbackData.message}
                    {nextAction}
                    {recommendation}
                    onContinue={() => state.handleNext()}
                />
            </div>
        </Modal>
    {:else if state.feedbackData}
        <ResultFeedback
            status={state.feedbackData.status as 'success' | 'wrong'}
            message={state.feedbackData.message}
            {nextAction}
            {xpEarned}
            {streakBonus}
            {recommendation}
            onContinue={() => state.handleNext()}
            onTryAgain={() => state.handleTryAgain()}
        />
    {/if}
{/if}
