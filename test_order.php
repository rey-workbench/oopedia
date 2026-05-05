<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$q = \App\Models\Question::where('material_id', '01kqt8tymds2swgcc7r2bcq5nv')->orderBy('id')->get();
$s = $q->groupBy('difficulty')->map(function($g) { return $g->shuffle(); })->flatten(1)->pluck('difficulty')->toJson();
echo $s;
