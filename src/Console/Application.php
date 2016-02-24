<?php

namespace Console;

use Symfony\Component\Console\Application as App;
use Console\Command\Play;
use Phar;

class Application
{
    public function __construct()
    {
        $console = new App('PHP-TERMINAL-GAMEBOY-EMULATOR', $this->getVersion());

        $console->add(new Play('play'));

        $console->run();
    }

    private function getVersion()
    {
        $path = Phar::running(true);

        if ($path) {
            Phar::interceptFileFuncs();
            $path .= '/php-terminal-gameboy-emulator';
        } else {
            $path = __DIR__ . '/../..';
        }

        return json_decode(file_get_contents($path . '/composer.json'), true)['version'];
    }
}
