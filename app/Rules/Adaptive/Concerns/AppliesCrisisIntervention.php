<?php

namespace App\Rules\Adaptive\Concerns;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

trait AppliesCrisisIntervention
{
    protected function applyVisualCrisis(array $state, string $message): array
    {
        $state['recommendation']    = 'Ulas Materi';
        $state['next_action']       = AdaptiveConstants::ACTION_STUDY_VISUAL;
        $state['message']           = $message;
        $state['intervention_type'] = AdaptiveConstants::INTERVENTION_VISUAL_CRISIS;

        return $state;
    }

    protected function applyTextualCrisis(array $state, string $message): array
    {
        $state['recommendation']    = 'Ulas Materi';
        $state['next_action']       = AdaptiveConstants::ACTION_STUDY_TEXTUAL;
        $state['message']           = $message;
        $state['intervention_type'] = AdaptiveConstants::INTERVENTION_TEXTUAL_CRISIS;

        return $state;
    }

    protected function applyPersistentVisualSafety(array $state, string $message): array
    {
        $state['recommendation']        = 'Bantuan Komprehensif';
        $state['next_action']           = AdaptiveConstants::ACTION_STUDY_VISUAL;
        $state['message']               = $message;
        $state['intervention_type']     = AdaptiveConstants::INTERVENTION_PERSISTENT_VISUAL_SAFETY;
        $state['force_material_review'] = true;

        return $state;
    }

    protected function applyPersistentTextualSafety(array $state, string $message): array
    {
        $state['recommendation']        = 'Bantuan Komprehensif';
        $state['next_action']           = AdaptiveConstants::ACTION_STUDY_TEXTUAL;
        $state['message']               = $message;
        $state['intervention_type']     = AdaptiveConstants::INTERVENTION_PERSISTENT_TEXTUAL_SAFETY;
        $state['force_material_review'] = true;

        return $state;
    }

    protected function applyVisualProjectRevision(array $state, array $facts): array
    {
        $state['recommendation']    = 'Revisi Proyek - Ulas Materi';
        $state['next_action']       = AdaptiveConstants::ACTION_STUDY_VISUAL;
        $state['intervention_type'] = AdaptiveConstants::INTERVENTION_VISUAL_PROJECT;
        $state['message']           = $this->getProjectRevisionMessage($facts, 'visual');

        return $state;
    }

    protected function applyTextualProjectRevision(array $state, array $facts): array
    {
        $state['recommendation']    = 'Revisi Proyek - Ulas Materi';
        $state['next_action']       = AdaptiveConstants::ACTION_STUDY_TEXTUAL;
        $state['intervention_type'] = AdaptiveConstants::INTERVENTION_TEXTUAL_PROJECT;
        $state['message']           = $this->getProjectRevisionMessage($facts, 'textual');

        return $state;
    }

    protected function applyFinalProjectVisualPersistent(array $state): array
    {
        $state['recommendation']        = 'Revisi Proyek - Bantuan Visual';
        $state['next_action']           = AdaptiveConstants::ACTION_STUDY_VISUAL;
        $state['message']               = 'Anda mengalami kesulitan berulang di Proyek Akhir. '
            . 'Mari ulas kembali materi secara visual sebelum mencoba lagi.';
        $state['intervention_type']     = AdaptiveConstants::INTERVENTION_FINAL_PROJECT_VISUAL_PERSISTENT;
        $state['force_material_review'] = true;

        return $state;
    }

    protected function applyFinalProjectTextualPersistent(array $state): array
    {
        $state['recommendation']        = 'Revisi Proyek - Bantuan Tekstual';
        $state['next_action']           = AdaptiveConstants::ACTION_STUDY_TEXTUAL;
        $state['message']               = 'Anda mengalami kesulitan berulang di Proyek Akhir. '
            . 'Mari ulas kembali materi secara mendalam sebelum mencoba lagi.';
        $state['intervention_type']     = AdaptiveConstants::INTERVENTION_FINAL_PROJECT_TEXTUAL_PERSISTENT;
        $state['force_material_review'] = true;

        return $state;
    }

    private function getProjectRevisionMessage(array $facts, string $style): string
    {
        if ($this->hasSyntaxError($facts)) {
            return $style === 'visual'
                ? 'Proyek Anda mengalami kendala pada penulisan kode (sintaks). Ayo ulas materi panduan visual koding!'
                : 'Ada kesalahan penulisan kode (sintaks) pada proyek Anda. Mari baca kembali dokumentasi teknis.';
        }
        if ($this->hasLogicError($facts)) {
            return $style === 'visual'
                ? 'Logika proyek Anda perlu diperbaiki. Mari lihat diagram alur dan konsep fundamental lagi.'
                : 'Logika pemrograman Anda perlu dipertajam. '
                    . 'Silakan ulas penjelasan teks mendalam mengenai konsep ini.';
        }

        return $style === 'visual'
            ? 'Proyek Anda perlu perbaikan. Mari ulas kembali konsep fundamental secara visual.'
            : 'Proyek Anda perlu perbaikan. Mari ulas kembali penjelasan teori secara mendalam.';
    }
}
