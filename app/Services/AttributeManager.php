<?php

namespace App\Services;

use App\Models\AttributeDefinition;

class AttributeManager
{
    /**
     * Get default attributes based on definitions.
     */
    public function getDefaultAttributes(): array
    {
        return AttributeDefinition::active()
            ->get()
            ->mapWithKeys(function($def) {
                return [$def->key => $def->getCastedDefaultValue()];
            })
            ->toArray();
    }

    /**
     * Merge current attributes with defaults to ensure all keys exist.
     */
    public function mergeWithDefaults(array $currentAttributes = []): array
    {
        $defaults = $this->getDefaultAttributes();
        return array_merge($defaults, $currentAttributes);
    }
}
