<?php

require_once __DIR__ . '/vendor/autoload.php';

use GameBoy\Canvas\TerminalCanvas;
use GameBoy\Core;
use GameBoy\Keyboard;

set_exception_handler(function (Exception $exception) {
    fwrite(STDERR, $exception->getMessage().PHP_EOL);
    exit(254);
});

if (count($argv) < 2) {
    throw new RuntimeException('You need to pass the ROM file name (Ex: drmario.rom)');
}

$rom = file_get_contents('roms/' . $argv[1]);

$canvas = new TerminalCanvas();
$core = new Core($rom, $canvas);
$keyboard = new Keyboard($core);

$core->start();

if (($core->stopEmulator & 2) == 0) {
    throw new RuntimeException('The GameBoy core is already running.');
}

if ($core->stopEmulator & 2 != 2) {
    throw new RuntimeException('GameBoy core cannot run while it has not been initialized.');
}

$core->stopEmulator &= 1;
$core->lastIteration = (int) (microtime(true) * 1000);

while (true) {
    $core->run();
    $keyboard->check();
}
