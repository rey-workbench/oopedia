<?php

function updateNamespace($file, $oldNs, $newNs)
{
    $content = file_get_contents($file);
    $content = str_replace('namespace ' . $oldNs . ';', 'namespace ' . $newNs . ';', $content);
    file_put_contents($file, $content);
}

$dirs = ['Crisis', 'Project', 'Certificate', 'Promotion', 'Remediation', 'EdgeCase'];
foreach ($dirs as $dir) {
    foreach (glob('app/Rules/Adaptive/' . $dir . '/*.php') as $file) {
        updateNamespace($file, 'App\Rules\Adaptive', 'App\Rules\Adaptive\\' . $dir);
    }
}
echo "Done\n";
