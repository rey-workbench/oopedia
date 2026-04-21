<?php

function fixImports($dir)
{
    $files = glob('app/Rules/Adaptive/' . $dir . '/*.php');
    foreach ($files as $file) {
        $content = file_get_contents($file);

        // Add use statement for BaseAdaptiveRule if not exists
        if (strpos($content, 'use App\Rules\Adaptive\BaseAdaptiveRule;') === false) {
            $content = str_replace(
                "use App\Rules\Adaptive\Concerns\\",
                "use App\Rules\Adaptive\BaseAdaptiveRule;\n\nuse App\Rules\Adaptive\Concerns\\",
                $content,
            );
            file_put_contents($file, $content);
            echo "Fixed: $file\n";
        }
    }
}

$dirs = ['Crisis', 'Project', 'Certificate', 'Promotion', 'Remediation', 'EdgeCase'];
foreach ($dirs as $dir) {
    fixImports($dir);
}
echo "Done\n";
