<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FindUnusedClasses extends Command
{
    protected $defaultPaths = [];

    protected $classNames = [];

    protected $controllerNames = [];

    protected $crudNames = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];

    protected $massiveString = '';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'findunused:classes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find unused classes';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->defaultPaths = collect([
            app_path(),
        ]);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->populateControllerNamesFromRoutes();
        $this->defaultPaths->each(function ($path) {
            $phpFiles = collect(File::allFiles($path))->filter(fn ($filename) => Str::endsWith($filename, '.php'))->each(function ($phpFile) {
                $fileContents = file_get_contents($phpFile);
                if (preg_match('/class\s+(\w+)/', $fileContents, $className) === 1) {
                    $this->classNames[$className[1]] = $phpFile->getPathName();
                    $fileContents                    = str_replace($className[1], Str::random(16), $fileContents);
                }
                $this->massiveString .= $fileContents;
            });
        });
        foreach ($this->classNames as $className => $files) {
            $matches = [];
            if (preg_match("/$className/", $this->massiveString, $matches) === 1 or $this->isARegisteredController($className)) {
                unset($this->classNames[$className]);
            }
        }
        dump($this->classNames);
    }

    public function populateControllerNamesFromRoutes()
    {
        $routes = \Route::getRoutes();
        foreach ($routes as $route) {
            $action = $route->getAction();

            // Some routes may not have a 'controller' key (closures, invokable controllers,
            // or routes defined with a different action key). Be defensive and skip
            // non-string controller actions.
            $controllerString = null;
            if (isset($action['controller']) && is_string($action['controller'])) {
                $controllerString = $action['controller'];
            } elseif (isset($action['uses']) && is_string($action['uses'])) {
                $controllerString = $action['uses'];
            }

            if (! $controllerString) {
                continue;
            }

            if (! str_contains($controllerString, 'App')) {
                continue;
            }

            // Support both "Controller@method" and invokable controllers (no '@')
            if (str_contains($controllerString, '@')) {
                [$controller, $method] = explode('@', $controllerString, 2);
            } else {
                $controller = $controllerString;
            }

            $this->controllerNames[] = class_basename($controller);
        }
    }

    public function isARegisteredController($className)
    {
        return in_array($className, $this->controllerNames);
    }

    public function ignoreCommonStuff($funcName, $fileName)
    {
        if ($funcName == 'handle' and preg_match('/(Middleware|Listeners|Commands)/', $fileName) === 1) {
            return true;
        }
        if ($funcName == 'broadcastOn' and preg_match('/Events/', $fileName) === 1) {
            return true;
        }

        return in_array($funcName, $this->crudNames) and Str::contains($fileName, 'Controller');
    }

    public function shouldConsider($filename)
    {
        if (Str::contains($filename, 'ServiceProvider')) {
            return false;
        }
        if (Str::contains($filename, 'Policies')) {
            return false;
        }
        if (Str::contains($filename, 'Observers')) {
            return false;
        }

        return true;
    }

    protected function mangleLaravelNames($fName)
    {
        $match = '';
        if (preg_match('/^scope(.+$)/', $fName, $match) === 1) {
            return Str::camel($match[1]);
        }
        if (preg_match('/^(get|set)(.+)Attribute$/', $fName, $match) === 1) {
            return Str::snake($match[2]);
        }

        return $fName;
    }
}
