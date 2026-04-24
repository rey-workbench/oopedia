<?php

namespace App\Rules\Adaptive;

use App\Models\AdaptiveRule as AdaptiveRuleModel;
use App\Models\Material;
use App\Rules\Adaptive\Constants\AdaptiveConstants as AC;
use App\Rules\Adaptive\Contracts\AdaptiveRuleInterface;

/**
 * Eksekutor Rule Dinamis – Pure Forward Chaining (Detective Model).
 *
 * Logika:
 *  1. evaluate()  – Cek semua required_facts tersedia di working memory.
 *  2. apply()     – Terapkan aksi ke state.
 *  3. getDeducedFacts() – Kembalikan Virtual Facts yang dihasilkan rule ini.
 *
 * Catatan: forbidden_facts DIHAPUS. Kita mengandalkan fakta positif saja.
 */
class DynamicAdaptiveRule implements AdaptiveRuleInterface
{
    public function __construct(protected AdaptiveRuleModel $model)
    {
    }

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
            : AC::ACTION_DEDUCTION;
    }

    public function getPriority(): int
    {
        return $this->model->priority;
    }

    /**
     * Pure Positive Evaluation (Forward Chaining).
     * Semua required_facts wajib ada di working memory.
     * Tidak ada logika negasi / forbidden.
     */
    public function evaluate(array $facts): bool
    {
        if (!$this->model->is_active) {
            return false;
        }

        $required = $this->model->required_facts ?? [];

        foreach ($required as $code) {
            if (!in_array($code, $facts, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Kembalikan fakta virtual yang dihasilkan rule ini.
     * Digunakan dalam inference loop untuk menambah working memory.
     * Fakta virtual didefinisikan di kolom `deduced_facts` (JSON).
     */
    public function getDeducedFacts(): array
    {
        return $this->model->deduced_facts ?? [];
    }

    /**
     * Terapkan Aksi ke State Siswa
     */
    public function apply(array $state, array $context): array
    {
        if (!$this->model->action_id) {
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

        // 2. Tangani Gamifikasi (Badges/XP)
        if (isset($params['badges']) && is_array($params['badges'])) {
            $current = $state['badges'] ?? [];
            $state['badges'] = array_values(array_unique(array_merge($current, $params['badges'])));
        }

        // 3. Tangani Sertifikat (Persistent)
        if (isset($params['certification'])) {
            $state = $this->handleCertification($params['certification'], $state, $context);
        }

        // 4. Update Properti State Dinamis
        foreach ($params as $key => $value) {
            if (in_array($key, ['next_action', 'badges', 'message', 'title', 'label'])) {
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

        // 4. Lampirkan Feedback untuk UI
        if (isset($params['next_action'])) {
            $state['next_action'] = $params['next_action'];
        }
        if (isset($params['message'])) {
            $state['_feedback_message'] = $params['message'];
        }
        if (isset($params['title'])) {
            $state['_feedback_title'] = $params['title'];
        }

        return $state;
    }

    private function handleModuleUnlock(array $state, array $context): array
    {
        $materialId = $context['material_id'] ?? null;
        if (!$materialId) {
            return $state;
        }

        $material = Material::find($materialId);
        $nextMaterial = $material?->getNextMaterial();

        if ($nextMaterial && $nextMaterial->module_id) {
            $unlocked = $state['unlocked_modules'] ?? [];
            if (!in_array((string) $nextMaterial->module_id, $unlocked, true)) {
                $unlocked[] = (string) $nextMaterial->module_id;
                $state['unlocked_modules'] = array_values(array_unique($unlocked));
            }
        }

        return $state;
    }

    private function handleCertification(string $tier, array $state, array $context): array
    {
        $materialId = $context['material_id'] ?? null;
        if (!$materialId) {
            return $state;
        }

        $certs = $state['certifications'] ?? [];
        // Format: [material_id => tier]
        $certs[(string) $materialId] = strtolower($tier);
        $state['certifications'] = $certs;

        return $state;
    }
}
