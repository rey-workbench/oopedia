<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

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

        $this->info('No state columns require cleanup at this time.');

        $this->info('Cleanup complete.');
    }
}
