<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$mode = $argv[1] ?? 'list';
$actuallyDelete = in_array($mode, ['delete', 'true', '1'], true);

try {
    $unused = cleanup_unused_images($actuallyDelete);

    if (empty($unused)) {
        echo "No unused files found.\n";
        exit(0);
    }

    if ($actuallyDelete) {
        echo "Deleted files:\n";
    } else {
        echo "Unused files (list-only):\n";
    }

    foreach ($unused as $f) {
        echo $f.PHP_EOL;
    }

    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, 'Error: '.$e->getMessage().PHP_EOL);
    exit(1);
}
