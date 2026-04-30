<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FindUnusedMethods extends Command
{
    protected Collection $defaultPaths;

    protected $functionNames = [];

    protected $massiveString = '';

    protected $crudNames = [
        'edit',
        'update',
        'create',
        'store',
        'destroy',
        'index',
        'show',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    #[\Override]
    protected $signature = 'findunused:methods';

    /**
     * The console command description.
     *
     * @var string
     */
    #[\Override]
    protected $description = 'Find unused methods';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->defaultPaths = collect([
            app_path(),
            resource_path('views'),
        ]);
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->defaultPaths->each(function ($path): void {
            collect(File::allFiles($path))->filter(fn ($filename): bool => Str::endsWith($filename, '.php') && $this->shouldConsider($filename->getPathName()))->each(function ($phpFile): void {
                $fileContents = file_get_contents($phpFile->getPathname());
                $this->massiveString .= $fileContents;
                $functionNames = [];
                preg_match_all('/function\s+([^ ]+?)\s*\(/', $fileContents, $functionNames);
                foreach ($functionNames[1] as $fName) {
                    if ($this->ignoreCommonStuff($fName, $phpFile->getPathName())) {
                        continue;
                    }

                    $this->functionNames[$fName][] = $phpFile->getPathName();
                }
            });
        });

        foreach (array_keys($this->functionNames) as $fName) {
            $matches   = [];
            $realFname = $this->mangleLaravelNames($fName);
            if (preg_match(sprintf('/(->|::)%s/', $realFname), (string) $this->massiveString, $matches) === 1) {
                unset($this->functionNames[$fName]);
                continue;
            }
        }

        dump($this->functionNames);
    }

    public function ignoreCommonStuff($funcName, $fileName)
    {
        if ($funcName == 'handle' && preg_match('/(Middleware|Listeners|Commands)/', (string) $fileName) === 1) {
            return true;
        }

        if ($funcName == 'broadcastOn' && preg_match('/Events/', (string) $fileName) === 1) {
            return true;
        }

        return in_array($funcName, $this->crudNames) && Str::contains($fileName, 'Controller');
    }

    public function shouldConsider($filename)
    {
        if (Str::contains($filename, 'ServiceProvider')) {
            return false;
        }

        if (Str::contains($filename, 'Policies')) {
            return false;
        }

        return ! Str::contains($filename, 'Observers');
    }

    protected function mangleLaravelNames($fName)
    {
        $match = '';
        if (preg_match('/^scope(.+$)/', (string) $fName, $match) === 1) {
            return Str::camel($match[1]);
        }

        if (preg_match('/^(get|set)(.+)Attribute$/', (string) $fName, $match) === 1) {
            return Str::snake($match[2]);
        }

        return $fName;
    }
}
