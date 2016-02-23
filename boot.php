<?php

require_once __DIR__.'/vendor/autoload.php';

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

$filename = 'roms/'.$argv[1];
if (!file_exists($filename)) {
    throw new RuntimeException(sprintf('"%s" does not exist', $filename));
}

if (extension_loaded('xdebug')) {
    fwrite(STDERR, 'Running php-gameboy with Xdebug enabled reduces its speed considerably.'.PHP_EOL);
    fwrite(STDERR, 'You should consider to disable it before execute php-gameboy.'.PHP_EOL);
    sleep(1);
}

$rom = file_get_contents($filename);

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
