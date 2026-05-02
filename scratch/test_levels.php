<?php

use App\Http\Controllers\Mahasiswa\MaterialQuestionController;
use App\Models\Material;
use Illuminate\Contracts\Console\Kernel;
use Inertia\Response;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$material = Material::first();
echo 'Testing with Material ID: ' . $material->id . PHP_EOL;

$controller = $app->make(MaterialQuestionController::class);
try {
    $response = $controller->levels($material->id);
    if ($response instanceof Response) {
        echo 'Success! Received Inertia Response.' . PHP_EOL;
    } else {
        echo 'Success! Response received.' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    if (method_exists($e, 'getStatusCode')) {
        echo 'Status Code: ' . $e->getStatusCode() . PHP_EOL;
    }
}
