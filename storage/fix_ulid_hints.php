<?php

$dir = __DIR__ . '/../app/Services';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

$count = 0;
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());

        // Match `int $id`, `int $userId`, `int $materialId`, `int|string $userId`, etc.
        $newContent = preg_replace(
            '/(int\|string|int)\s+\$(userId|materialId|questionId|subMaterialId|studentId|id|moduleId)\b/',
            'string $$2',
            $content
        );

        if ($content !== $newContent) {
            file_put_contents($file->getPathname(), $newContent);
            echo 'Updated ' . $file->getPathname() . PHP_EOL;
            $count++;
        }
    }
}

echo "Done. Updated $count files.\n";
