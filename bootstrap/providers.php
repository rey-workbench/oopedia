<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\BroadcastServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\RepositoryServiceProvider;
use App\Providers\ServiceServiceProvider;
use Arafa\DeadcodeDetector\DeadCodeServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    BroadcastServiceProvider::class,
    EventServiceProvider::class,
    RepositoryServiceProvider::class,
    ServiceServiceProvider::class,
    DeadCodeServiceProvider::class,
];
