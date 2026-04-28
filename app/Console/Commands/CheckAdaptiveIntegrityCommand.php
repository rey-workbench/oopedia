<?php

namespace App\Console\Commands;

use App\Models\AdaptiveRule;
use Illuminate\Console\Command;

class CheckAdaptiveIntegrityCommand extends Command
{
    protected $signature = 'adaptive:check-integrity';

    protected $description = 'Checks for circular dependencies and logical gaps in the adaptive rule tree';

    public function handle(): int
    {
        $this->info('Starting Adaptive Rule Integrity Check...');

        // 1. Cycle Detection
        $rules    = AdaptiveRule::all();
        $hasCycle = false;

        foreach ($rules as $rule) {
            if ($this->detectCycle($rule)) {
                $this->error("Cycle detected starting from Node ID: {$rule->id} ({$rule->name})");
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
            foreach ($invalidLeafs as $leaf) {
                $this->warn("⚠️ Rule Node {$leaf->id} has no action_ids defined.");
            }
        } else {
            $this->info('✅ All rules have action codes.');
        }

        return $hasCycle ? 1 : 0;
    }

    private function detectCycle(AdaptiveRule $node, array $visited = []): bool
    {
        if (in_array($node->id, $visited)) {
            return true;
        }

        $visited[] = $node->id;

        foreach ($node->children as $child) {
            if ($this->detectCycle($child, $visited)) {
                return true;
            }
        }

        return false;
    }
}
