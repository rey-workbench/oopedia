<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearLogCommand extends Command
{
    #[\Override]
    protected $signature = 'log:clear';

    #[\Override]
    protected $description = 'Clear Laravel log file';

    public function handle(): void
    {
        exec('echo "" > ' . storage_path('logs/laravel.log'));
        $this->info('Logs have been cleared!');
    }
}
