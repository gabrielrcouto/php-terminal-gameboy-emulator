<?php

namespace GameBoy;

use Phar;

class HelpMessage
{
    public function __construct($argv)
    {
        if (count($argv) < 2 || in_array($argv[1], ['help', '--help', '-h'])) {
            $this->helpMessage();
        }


        if ($argv[1] === '--version') {
            $this->version();
        }
    }

    protected function helpMessage()
    {
        die($this->getHelpMessage());
    }

    protected function version()
    {
        die($this->getHead());
    }

    private function getHelpMessage()
    {
        return <<<HM
{$this->getHead()}

usage: php-gameboy [rom]         Load ROM and start
   or: php-gameboy [arguments]   See Arguments section

Arguments:
   -h, --help, help              Show help message
   --version                     Show version

HM;
    }

    private function getHead()
    {
        return <<<HEAD
                                                                               
                  _           _                     _            _             
             ___ | |_  ___   | |_  ___  ___  _____ |_| ___  ___ | |            
            | . ||   || . |  |  _|| -_||  _||     || ||   || .'|| |            
            |  _||_|_||  _|  |_|  |___||_|  |_|_|_||_||_|_||__,||_|            
            |_|       |_|                                                      
                                                                               
                       _                                 _       _             
 ___  ___  _____  ___ | |_  ___  _ _    ___  _____  _ _ | | ___ | |_  ___  ___ 
| . || .'||     || -_|| . || . || | |  | -_||     || | || || .'||  _|| . ||  _|
|_  ||__,||_|_|_||___||___||___||_  |  |___||_|_|_||___||_||__,||_|  |___||_|  
|___|                           |___|                                          

PHP-TERMINAL-GAMEBOY-EMULATOR - Version: {$this->getVersion()}

HEAD;
    }

    private function getVersion()
    {
        $path = Phar::running(true);
        Phar::interceptFileFuncs();

        return json_decode(file_get_contents($path. '/php-terminal-gameboy-emulator/composer.json'), true)['version'];
    }
}
