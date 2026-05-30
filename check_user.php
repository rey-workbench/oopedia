<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$u = \App\Models\User::where('email', 'test@gmail.com')->first();
if (!$u) {
    echo "User not found\n";
} else {
    echo "Email: {$u->email}\n";
    $s = \App\Models\StudentState::where('user_id', $u->id)->first();
    if ($s) {
        echo "CERTS:\n";
        print_r($s->certifications);
    }
}
