<?php

namespace App\Console\Commands;

use App\Models\AdaptiveRule;
use Illuminate\Console\Command;

class CheckAdaptiveIntegrityCommand extends Command
{
    #[\Override]
    protected $signature = 'adaptive:check-integrity';

    #[\Override]
    protected $description = 'Checks for circular dependencies and logical gaps in the adaptive rule tree';

    public function handle(): int
    {
        $this->info('Starting Adaptive Rule Integrity Check...');

        // 1. Cycle Detection
        $rules    = AdaptiveRule::select(['id', 'name', 'required_fact_ids', 'deduced_fact_ids'])->get();
        $hasCycle = false;

        foreach ($rules as $rule) {
            if ($this->detectCycle($rule)) {
                $this->error(sprintf('Cycle detected starting from Node ID: %s (%s)', $rule->id, $rule->name));
                $hasCycle = true;
            }
        }

        if (! $hasCycle) {
            $this->info('✅ No circular dependencies found.');
        }

        // 2. Leaf Verification (All rules should have at least one action)
        $invalidLeafs = AdaptiveRule::whereJsonLength('action_ids', 0)
            ->get();

        if ($invalidLeafs->isNotEmpty()) {
            foreach ($invalidLeafs as $invalidLeaf) {
                $this->warn(sprintf('⚠️ Rule Node %s has no action_ids defined.', $invalidLeaf->id));
            }
        } else {
            $this->info('✅ All rules have action codes.');
        }

        return $hasCycle ? 1 : 0;
    }

    private function detectCycle(AdaptiveRule $adaptiveRule, array $visited = []): bool
    {
        if (in_array($adaptiveRule->id, $visited)) {
            return true;
        }

        $visited[] = $adaptiveRule->id;

        foreach ($adaptiveRule->children as $child) {
            if ($this->detectCycle($child, $visited)) {
                return true;
            }
        }

        return false;
    }
}
