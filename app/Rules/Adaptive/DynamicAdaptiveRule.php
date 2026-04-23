<?php

namespace App\Rules\Adaptive;

use App\Models\AdaptiveRule as AdaptiveRuleModel;
use App\Models\Material;
use App\Rules\Adaptive\Constants\AdaptiveConstants as AC;
use App\Rules\Adaptive\Contracts\AdaptiveRuleInterface;

/**
 * Eksekutor Rule Dinamis.
 * Menangani logika forward chaining dan eksekusi aksi (H-Codes)
 * secara dinamis berdasarkan instruksi di database.
 */
class DynamicAdaptiveRule implements AdaptiveRuleInterface
{
    public function __construct(protected AdaptiveRuleModel $model) {}

    public function getRuleId(): string
    {
        return $this->model->rule_code;
    }

    public function getModel(): AdaptiveRuleModel
    {
        return $this->model;
    }

    public function getRuleName(): string
    {
        return $this->model->name;
    }

    public function getActionCode(): string
    {
        return $this->model->relationLoaded('action') && $this->model->action
            ? $this->model->action->code
            : 'H00';
    }

    public function getPriority(): int
    {
        return $this->model->priority;
    }

    /**
     * Evaluasi Rule (Forward Chaining)
     * - Semua required_facts wajib ada di $facts.
     * - Tidak boleh ada satu pun forbidden_facts di $facts.
     */
    public function evaluate(array $facts): bool
    {
        if (! $this->model->is_active) {
            return false;
        }

        $required  = $this->model->required_facts  ?? [];
        $forbidden = $this->model->forbidden_facts ?? [];

        foreach ($required as $code) {
            if (! in_array($code, $facts, true)) {
                return false;
            }
        }

        foreach ($forbidden as $code) {
            if (in_array($code, $facts, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Terapkan Aksi ke State Siswa
     */
    public function apply(array $state, array $context): array
    {
        if (! $this->model->action_id) {
            return $state;
        }

        $this->model->loadMissing('action');
        $instructions = $this->model->action->instructions ?? [];

        return $this->executeAction($instructions, $state, $context);
    }

    /**
     * Eksekusi Instruksi Aksi secara dinamis.
     * Mendukung dot-notation untuk nested state properties.
     */
    private function executeAction(array $params, array $state, array $context): array
    {
        // 1. Unlock Modul Otomatis jika aksi adalah Module Completion
        if (isset($params['next_action']) && $params['next_action'] === AC::ACTION_FINISH_MATERIAL) {
            $state = $this->handleModuleUnlock($state, $context);
        }

        // 2. Tangani Pemberian Sertifikat
        if (isset($params['award'])) {
            $state = $this->handleCertification($state, $context, $params['award']);
        }

        // 3. Tangani Gamifikasi (Badges/XP)
        if (isset($params['badges']) && is_array($params['badges'])) {
            $current   = $state['badges'] ?? [];
            $newBadges = array_values(array_unique(array_merge($current, $params['badges'])));
            $state['badges'] = $newBadges;
        }

        // 4. Update Properti State Dinamis
        foreach ($params as $key => $value) {
            if (in_array($key, ['next_action', 'award', 'badges', 'message'])) {
                continue;
            }

            // Dukung increment/decrement string: "+10" atau "-5"
            if (is_string($value) && (str_starts_with($value, '+') || str_starts_with($value, '-'))) {
                $currentValue = data_get($state, $key, 0);
                data_set($state, $key, $currentValue + (int) $value);
            } else {
                data_set($state, $key, $value);
            }
        }

        // 5. Lampirkan Pesan Feedback untuk UI
        if (isset($params['message'])) {
            $state['_feedback_message'] = $params['message'];
        }

        return $state;
    }

    private function handleModuleUnlock(array $state, array $context): array
    {
        $materialId = $context['material_id'] ?? null;
        if (! $materialId) {
            return $state;
        }

        $material     = Material::find($materialId);
        $nextMaterial = $material?->getNextMaterial();

        if ($nextMaterial && $nextMaterial->module_id) {
            $unlocked = $state['unlocked_modules'] ?? [];
            if (! in_array((string) $nextMaterial->module_id, $unlocked, true)) {
                $unlocked[]                = (string) $nextMaterial->module_id;
                $state['unlocked_modules'] = array_values(array_unique($unlocked));
            }
        }

        return $state;
    }

    private function handleCertification(array $state, array $context, string $award): array
    {
        $materialId = $context['material_id'] ?? null;
        if (! $materialId) {
            return $state;
        }

        $certs                   = $state['certifications'] ?? [];
        $certs[$materialId]      = $award;
        $state['certifications'] = $certs;

        return $state;
    }
}
