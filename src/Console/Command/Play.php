<?php

namespace Console\Command;

use GameBoy\Canvas\TerminalCanvas;
use GameBoy\Core;
use GameBoy\Keyboard;
use GameBoy\HelpMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Play extends Command
{
    protected $defaultName;

    public function __construct($defaultName)
    {
        $this->defaultName = $defaultName;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('play')
            ->addArgument('rom', InputArgument::REQUIRED, 'Rom file as *.gb, *.bgc')
            ->setDescription('Load ROM and start')
            ->setHelp($this->getHelpMessage())
        ;
    }

    private function getHelpMessage()
    {
        return <<<HM
usage: php-gameboy play [rom]         Load ROM and start
HM;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('rom');

        if (! file_exists($filename)) {
            throw new \RuntimeException(sprintf('"%s" does not exist', $filename));
        }

        $rom = file_get_contents($filename);

        $canvas = new TerminalCanvas();
        $core = new Core($rom, $canvas);
        $keyboard = new Keyboard($core);

        $core->start();

        if (($core->stopEmulator & 2) == 0) {
            throw new \RuntimeException('The GameBoy core is already running.');
        }

        if ($core->stopEmulator & 2 != 2) {
            throw new \RuntimeException('GameBoy core cannot run while it has not been initialized.');
        }

        $core->stopEmulator &= 1;
        $core->lastIteration = (int) (microtime(true) * 1000);

        while (true) {
            $core->run();
            $keyboard->check();
        }
    }
}
