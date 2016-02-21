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

use Goutte\Client;

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
             ->setDescription('convert an octocat to terminal')
             ->addArgument(
                 'cat',
                 InputArgument::REQUIRED,
                 'Cat number, name, title, or "random"'
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
        $url = 'https://octodex.github.com';
        $cat = $input->getArgument('cat');

        $client = new Client();
        $crawler = $client->request('GET', $url);

        try {
            if (is_numeric($cat) || $cat == 'random') {
                $filter = $crawler->filter('.preview-image > img');
                $total = iterator_count($filter);

                if($cat == 'random') {
                    $cat = mt_rand(1, $total);
                }

                $image = $filter->eq($total - $cat)->attr('data-src');
            }

            else if (substr($cat, 0, 4) == 'the ') {
                $image = $crawler->filter("img[alt=\"$cat\"]")->attr('data-src');
            }

            else {
                $image = $crawler->filter("a[href=\"/$cat\"] > img")->attr('data-src');
            }
        }

        catch (InvalidArgumentException $exception) {
            throw new RuntimeException('Octocat not found at: ' . $url);
        }

        list($terminalWidth, $terminalHeight) = $this->getApplication()->getTerminalDimensions();

        $printer = new ImagePrinter(
            'https://octodex.github.com' . $image,
            $input->getOption('threshold'),
            $input->getOption('ratio'),
            $input->getOption('invert')
        );

        $printer->run($terminalWidth, $terminalHeight);
    }
}

$console = new Application();
$console->run();