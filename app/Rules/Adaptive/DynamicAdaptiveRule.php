<?php

namespace App\Rules\Adaptive;

use App\Models\AdaptiveRule as AdaptiveRuleModel;
use App\Models\Material;
use App\Rules\Adaptive\Contracts\AdaptiveRuleInterface;

class DynamicAdaptiveRule implements AdaptiveRuleInterface
{
    public function __construct(protected AdaptiveRuleModel $model) {}

    public function getRuleId(): string
    {
        return $this->model->rule_code;
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
     * Forward chaining evaluation:
     * - All required_facts must be present in $facts
     * - None of forbidden_facts may be present in $facts
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

    public function apply(array $state, array $context): array
    {
        if (! $this->model->action_id) {
            return $state;
        }

        $instructions = $this->model->relationLoaded('action') && $this->model->action
            ? ($this->model->action->instructions ?? [])
            : [];

        return $this->executeAction($instructions, $state, $context);
    }

    private function executeAction(array $params, array $state, array $context): array
    {
        if (isset($params['unlock_next_module']) && $params['unlock_next_module']) {
            $state = $this->handleModuleUnlock($state, $context);
        }

        if (isset($params['certification'])) {
            $state = $this->handleCertification($state, $context, $params['certification']);
        }

        if (isset($params['badges']) && is_array($params['badges'])) {
            $current         = $state['badges'] ?? [];
            $state['badges'] = array_values(array_unique(array_merge($current, $params['badges'])));
        }

        foreach ($params as $key => $value) {
            if (in_array($key, ['unlock_next_module', 'certification', 'badges'])) {
                continue;
            }

            if (is_string($value) && (str_starts_with($value, '+') || str_starts_with($value, '-'))) {
                $current = data_get($state, str_replace('.', '.', $key), 0);
                data_set($state, $key, $current + (int) $value);
            } else {
                data_set($state, $key, $value);
            }
        }

        if (isset($params['message'])) {
            $state['message'] = $params['message'];
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
            if (! in_array($nextMaterial->module_id, $unlocked)) {
                $unlocked[]                = $nextMaterial->module_id;
                $state['unlocked_modules'] = $unlocked;
            }
        }

        return $state;
    }

    private function handleCertification(array $state, array $context, string $certification): array
    {
        $materialId = $context['material_id'] ?? null;
        if (! $materialId) {
            return $state;
        }

        $certifications              = $state['certifications'] ?? [];
        $certifications[$materialId] = $certification;
        $state['certifications']     = $certifications;

        return $state;
    }
}
