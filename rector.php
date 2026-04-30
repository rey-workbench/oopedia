<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->withSkip([
        __DIR__ . '/bootstrap/cache',
        __DIR__ . '/storage',
        __DIR__ . '/vendor',
        // Skip dangerous renaming rules that break Inertia/Laravel logic
        RenameVariableToMatchMethodCallReturnTypeRector::class,
        RenameParamToMatchTypeRector::class,
    ])
    // Enable PHP 8.5 sets to fix deprecations and use modern features
    ->withPhpSets(php85: true)
    // Enable high levels of automation
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        earlyReturn: true,
        naming: true,
        codingStyle: true,
    );
