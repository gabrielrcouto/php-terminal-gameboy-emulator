#!/usr/bin/env php
<?php

/*
 * This file is part of php-drawille
 *
 * (c) Jeff Welch <whatthejeff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Command\Command as ConsoleCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends ConsoleApplication
{
    public function __construct() {
        parent::__construct(basename(__FILE__), '1.0');
        $this->add(new Command);
    }

    protected function getCommandName(InputInterface $input) {
        return basename(__FILE__);
    }

    protected function getDefaultInputDefinition()
    {
        return new InputDefinition(array(
            new InputOption('--help',    '-h', InputOption::VALUE_NONE, 'Display this help message.'),
            new InputOption('--version', '-V', InputOption::VALUE_NONE, 'Display this application version.')
        ));
    }
}

class Command extends ConsoleCommand
{
    protected function configure()
    {
        $this->setName(basename(__FILE__))
             ->setDescription('convert an image to terminal')
             ->addArgument(
                 'image',
                 InputArgument::REQUIRED,
                 'Image file path/url'
             )
             ->addOption(
                 'threshold',
                 't',
                 InputOption::VALUE_REQUIRED,
                 'Color threshold',
                 382.5
             )
             ->addOption(
                 'ratio',
                 'r',
                 InputOption::VALUE_REQUIRED,
                 'Image resize ratio'
             )
             ->addOption(
                 'invert',
                 'i',
                 InputOption::VALUE_NONE,
                 'Invert colors'
             );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($terminalWidth, $terminalHeight) = $this->getApplication()->getTerminalDimensions();

        $printer = new ImagePrinter(
            $input->getArgument('image'),
            $input->getOption('threshold'),
            $input->getOption('ratio'),
            $input->getOption('invert')
        );

        $printer->run($terminalWidth, $terminalHeight);
    }
}

$console = new Application();
$console->run();