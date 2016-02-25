<?php

<<<<<<< HEAD
foreach (['../../autoload.php', '../vendor/autoload.php', 'vendor/autoload.php'] as $autoload) {
    $autoload = __DIR__ . '/' . $autoload;
    if (file_exists($autoload)) {
        require_once $autoload;
        break;
    }
}
unset($autoload);

use Console\Application;

new Application();
=======
require_once('vendor/autoload.php');

use Console\Application;

$console = new Application();
>>>>>>> 40a9b73... Refactoring CORE, adding Symfony/Console
