<?php

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

foreach (User::with('role')->get() as $user) {
    echo $user->id . ': ' . ($user->role->role_name->value ?? 'null') . PHP_EOL;
}
