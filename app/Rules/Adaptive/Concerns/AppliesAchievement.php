<?php

namespace App\Rules\Adaptive\Concerns;

use App\Rules\Adaptive\Constants\AdaptiveConstants;

trait AppliesAchievement
{
    protected function applyModuleGraduation(array $state, array $context): array
    {
        $state['next_action'] = AdaptiveConstants::ACTION_FINISH_MATERIAL;
        $state['message']     = 'Selamat! Anda telah menguasai seluruh materi modul ini dengan sempurna.';
        $state['achievement'] = AdaptiveConstants::ACHIEVEMENT_MODULE_COMPLETED;

        return $this->applyModuleProgress($state, $context);
    }

    protected function applyGoldCertificate(array $state, array $context): array
    {
        $state['next_action']                   = AdaptiveConstants::ACTION_ISSUE_CERTIFICATE;
        $state['message']                       = 'Luar Biasa! Anda layak mendapatkan Sertifikat EMAS sebagai Object-Oriented Architect.';
        $state['certification']                 = AdaptiveConstants::CERT_GOLD;
        $state['achievement']                   = AdaptiveConstants::ACHIEVEMENT_GOLD_CERTIFICATE;
        $state['gamification_data']['badges'][] = AdaptiveConstants::BADGE_GOLD_ARCHITECT;

        return $this->applyModuleProgress($state, $context);
    }

    protected function applySilverCertificate(array $state, array $context): array
    {
        $state['next_action']                   = AdaptiveConstants::ACTION_ISSUE_CERTIFICATE;
        $state['message']                       = 'Selamat! Anda layak mendapatkan Sertifikat PERAK sebagai Object-Oriented Developer.';
        $state['certification']                 = AdaptiveConstants::CERT_SILVER;
        $state['achievement']                   = AdaptiveConstants::ACHIEVEMENT_SILVER_CERTIFICATE;
        $state['gamification_data']['badges'][] = AdaptiveConstants::BADGE_SILVER_DEVELOPER;

        return $this->applyModuleProgress($state, $context);
    }

    protected function applyBronzeCertificate(array $state, array $context): array
    {
        $state['next_action']                   = AdaptiveConstants::ACTION_ISSUE_CERTIFICATE;
        $state['message']                       = 'Bagus! Anda layak mendapatkan Sertifikat PERUNGGU sebagai Junior Object-Oriented Programmer.';
        $state['certification']                 = AdaptiveConstants::CERT_BRONZE;
        $state['achievement']                   = AdaptiveConstants::ACHIEVEMENT_BRONZE_CERTIFICATE;
        $state['gamification_data']['badges'][] = AdaptiveConstants::BADGE_BRONZE_JUNIOR;

        return $this->applyModuleProgress($state, $context);
    }

    private function applyModuleProgress(array $state, array $context): array
    {
        if (isset($context['module_id'])) {
            $moduleProgress                             = $state['adaptive_state']['module_progress'] ?? [];
            $moduleProgress[$context['module_id']]      = 100;
            $state['adaptive_state']['module_progress'] = $moduleProgress;
        }

        return $state;
    }
}
