<?php

namespace App\Rules\Adaptive\Concerns;

use App\Models\Material;
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
        return $this->applyCertificate(
            state: $state,
            context: $context,
            certification: AdaptiveConstants::CERT_GOLD,
            achievement: AdaptiveConstants::ACHIEVEMENT_GOLD_CERTIFICATE,
            badge: AdaptiveConstants::BADGE_GOLD_ARCHITECT,
            message: 'Luar Biasa! Anda layak mendapatkan Sertifikat EMAS sebagai Object-Oriented Architect.',
        );
    }

    protected function applySilverCertificate(array $state, array $context): array
    {
        return $this->applyCertificate(
            state: $state,
            context: $context,
            certification: AdaptiveConstants::CERT_SILVER,
            achievement: AdaptiveConstants::ACHIEVEMENT_SILVER_CERTIFICATE,
            badge: AdaptiveConstants::BADGE_SILVER_DEVELOPER,
            message: 'Selamat! Anda layak mendapatkan Sertifikat PERAK sebagai Object-Oriented Developer.',
        );
    }

    protected function applyBronzeCertificate(array $state, array $context): array
    {
        return $this->applyCertificate(
            state: $state,
            context: $context,
            certification: AdaptiveConstants::CERT_BRONZE,
            achievement: AdaptiveConstants::ACHIEVEMENT_BRONZE_CERTIFICATE,
            badge: AdaptiveConstants::BADGE_BRONZE_JUNIOR,
            message: 'Bagus! Anda layak mendapatkan Sertifikat PERUNGGU sebagai Junior Object-Oriented Programmer.',
        );
    }

    private function applyCertificate(
        array $state,
        array $context,
        string $certification,
        string $achievement,
        string $badge,
        string $message,
    ): array {
        $state['next_action']                   = AdaptiveConstants::ACTION_ISSUE_CERTIFICATE;
        $state['message']                       = $message;
        $state['certification']                 = $certification;
        $state['achievement']                   = $achievement;
        $state['gamification_data']['badges'][] = $badge;
        $state                                  = $this->recordCertification($state, $context, $certification);

        return $this->applyModuleProgress($state, $context);
    }

    private function recordCertification(array $state, array $context, string $certification): array
    {
        $materialId = (string) ($context['material_id'] ?? '');

        if ($materialId === '') {
            return $state;
        }

        $learningProfile = $state['learning_profile']         ?? [];
        $certifications  = $learningProfile['certifications'] ?? [];

        if (! is_array($certifications)) {
            $certifications = [];
        }

        $certifications[$materialId]       = $certification;
        $learningProfile['certifications'] = $certifications;
        $state['learning_profile']         = $learningProfile;

        return $state;
    }

    private function applyModuleProgress(array $state, array $context): array
    {
        if (isset($context['module_id'])) {
            $moduleId = $context['module_id'];

            // 1. Mark current module as 100% complete in adaptive_state
            $moduleProgress                             = $state['adaptive_state']['module_progress'] ?? [];
            $moduleProgress[$moduleId]                  = 100;
            $state['adaptive_state']['module_progress'] = $moduleProgress;

            // 2. Unlock NEXT module in learning_profile
            $currentMaterial = Material::find($context['material_id'] ?? null);
            if ($currentMaterial) {
                $nextMaterial = $currentMaterial->getNextMaterial();
                if ($nextMaterial && $nextMaterial->module_id) {
                    $learningProfile      = $state['learning_profile']           ?? [];
                    $unlockedModules      = $learningProfile['unlocked_modules'] ?? [];
                    $nextModuleId         = $nextMaterial->module_id;

                    if (! in_array($nextModuleId, $unlockedModules)) {
                        $unlockedModules[]                   = $nextModuleId;
                        $learningProfile['unlocked_modules'] = array_values(array_unique($unlockedModules));
                        $state['learning_profile']           = $learningProfile;
                    }
                }
            }
        }

        return $state;
    }
}
