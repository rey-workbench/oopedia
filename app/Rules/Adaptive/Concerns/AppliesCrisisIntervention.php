<?php

namespace App\Rules\Adaptive\Concerns;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

trait AppliesCrisisIntervention
{
    protected function applyVisualCrisis(array $state, string $message): array
    {
        return $this->setInterventionState(
            state: $state,
            recommendation: 'Ulas Materi',
            nextAction: AdaptiveConstants::ACTION_STUDY_VISUAL,
            message: $message,
            interventionType: AdaptiveConstants::INTERVENTION_VISUAL_CRISIS,
        );
    }

    protected function applyTextualCrisis(array $state, string $message): array
    {
        return $this->setInterventionState(
            state: $state,
            recommendation: 'Ulas Materi',
            nextAction: AdaptiveConstants::ACTION_STUDY_TEXTUAL,
            message: $message,
            interventionType: AdaptiveConstants::INTERVENTION_TEXTUAL_CRISIS,
        );
    }

    protected function applyPersistentVisualSafety(array $state, string $message): array
    {
        return $this->setInterventionState(
            state: $state,
            recommendation: 'Bantuan Komprehensif',
            nextAction: AdaptiveConstants::ACTION_STUDY_VISUAL,
            message: $message,
            interventionType: AdaptiveConstants::INTERVENTION_PERSISTENT_VISUAL_SAFETY,
            forceMaterialReview: true,
        );
    }

    protected function applyPersistentTextualSafety(array $state, string $message): array
    {
        return $this->setInterventionState(
            state: $state,
            recommendation: 'Bantuan Komprehensif',
            nextAction: AdaptiveConstants::ACTION_STUDY_TEXTUAL,
            message: $message,
            interventionType: AdaptiveConstants::INTERVENTION_PERSISTENT_TEXTUAL_SAFETY,
            forceMaterialReview: true,
        );
    }

    protected function applyVisualProjectRevision(array $state, array $facts): array
    {
        return $this->setInterventionState(
            state: $state,
            recommendation: 'Revisi Proyek - Ulas Materi',
            nextAction: AdaptiveConstants::ACTION_STUDY_VISUAL,
            message: $this->getProjectRevisionMessage($facts, 'visual'),
            interventionType: AdaptiveConstants::INTERVENTION_VISUAL_PROJECT,
        );
    }

    protected function applyTextualProjectRevision(array $state, array $facts): array
    {
        return $this->setInterventionState(
            state: $state,
            recommendation: 'Revisi Proyek - Ulas Materi',
            nextAction: AdaptiveConstants::ACTION_STUDY_TEXTUAL,
            message: $this->getProjectRevisionMessage($facts, 'textual'),
            interventionType: AdaptiveConstants::INTERVENTION_TEXTUAL_PROJECT,
        );
    }

    protected function applyFinalProjectVisualPersistent(array $state): array
    {
        return $this->setInterventionState(
            state: $state,
            recommendation: 'Revisi Proyek - Bantuan Visual',
            nextAction: AdaptiveConstants::ACTION_STUDY_VISUAL,
            message: 'Anda mengalami kesulitan berulang di Proyek Akhir. Mari ulas kembali materi secara visual sebelum mencoba lagi.',
            interventionType: AdaptiveConstants::INTERVENTION_FINAL_PROJECT_VISUAL_PERSISTENT,
            forceMaterialReview: true,
        );
    }

    protected function applyFinalProjectTextualPersistent(array $state): array
    {
        return $this->setInterventionState(
            state: $state,
            recommendation: 'Revisi Proyek - Bantuan Tekstual',
            nextAction: AdaptiveConstants::ACTION_STUDY_TEXTUAL,
            message: 'Anda mengalami kesulitan berulang di Proyek Akhir. Mari ulas kembali materi secara mendalam sebelum mencoba lagi.',
            interventionType: AdaptiveConstants::INTERVENTION_FINAL_PROJECT_TEXTUAL_PERSISTENT,
            forceMaterialReview: true,
        );
    }

    private function setInterventionState(
        array $state,
        string $recommendation,
        string $nextAction,
        string $message,
        string $interventionType,
        bool $forceMaterialReview = false,
    ): array {
        $state['recommendation']    = $recommendation;
        $state['next_action']       = $nextAction;
        $state['message']           = $message;
        $state['intervention_type'] = $interventionType;

        if ($forceMaterialReview) {
            $state['force_material_review'] = true;
        }

        return $state;
    }

    private function getProjectRevisionMessage(array $facts, string $style): string
    {
        $resolvedStyle = $this->getLearningStyle($facts) ?? $style;

        if ($this->hasNoError($facts) || ! $this->hasAnyError($facts)) {
            return $resolvedStyle === 'visual'
                ? 'Proyek Anda perlu perbaikan. Mari ulas kembali konsep fundamental secara visual.'
                : 'Proyek Anda perlu perbaikan. Mari ulas kembali penjelasan teori secara mendalam.';
        }

        if ($this->hasSyntaxError($facts)) {
            return $resolvedStyle === 'visual'
                ? 'Proyek Anda mengalami kendala pada penulisan kode (sintaks). Ayo ulas materi panduan visual koding!'
                : 'Ada kesalahan penulisan kode (sintaks) pada proyek Anda. Mari baca kembali dokumentasi teknis.';
        }

        if ($this->hasLogicError($facts)) {
            return $resolvedStyle === 'visual'
                ? 'Logika proyek Anda perlu diperbaiki. Mari lihat diagram alur dan konsep fundamental lagi.'
                : 'Logika pemrograman Anda perlu dipertajam. '
                    . 'Silakan ulas penjelasan teks mendalam mengenai konsep ini.';
        }

        return $resolvedStyle === 'visual'
            ? 'Proyek Anda perlu perbaikan. Mari ulas kembali konsep fundamental secara visual.'
            : 'Proyek Anda perlu perbaikan. Mari ulas kembali penjelasan teori secara mendalam.';
    }
}
