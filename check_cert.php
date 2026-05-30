<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$u = \App\Models\User::where('email', 'sertifikat@gmail.com')->first();
if (!$u) die("User not found\n");
$s = \App\Models\StudentState::where('user_id', $u->id)->first();
if (!$s) die("State not found\n");
echo "ADAPTIVE STATE:\n";
print_r($s->adaptive_state);
echo "\nCERTIFICATIONS:\n";
print_r($s->certifications);
echo "\n";
