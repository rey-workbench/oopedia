<?php

namespace App\Console\Commands;

use App\Models\StudentState;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class AdaptiveStateCleanupCommand extends Command
{
    #[\Override]
    protected $signature = 'adaptive:cleanup {--force : Proceed without confirmation}';

    #[\Override]
    protected $description = 'Reset student state columns to defaults (for maintenance/debug)';

    public function handle(): void
    {
        if (! $this->option('force') && ! $this->confirm('This will reset student state data. Continue?')) {
            return;
        }

        $this->info('Resetting wrong_streak for all students...');

        StudentState::chunk(200, function (Collection $states): void {
            /** @var StudentState $state */
            foreach ($states as $state) {
                // Only reset aggregated wrong_streak (never truncate real stats)
                if ($state->wrong_streak > 20) {
                    $state->update(['wrong_streak' => 0]);
                }
            }
        });

        $this->info('Cleanup complete.');
    }
}
