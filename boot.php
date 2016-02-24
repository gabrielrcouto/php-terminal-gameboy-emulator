<?php

foreach (['../../autoload.php', '../vendor/autoload.php', 'vendor/autoload.php'] as $autoload) {
    $autoload = __DIR__ . '/' . $autoload;
    if (file_exists($autoload)) {
        require_once $autoload;
        break;
    }
}
unset($autoload);

use Console\Application;

$console = new Application();
