<?php

namespace SickBeard\Telegram\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface as In;
use Symfony\Component\Console\Output\OutputInterface as Out;


/**
 * Tests both Telegram and SickBeard.
 *
 * @author Roelof Roos <github@roelof.io>
 * @license GPL-3.0
 */
class GlobalTest extends Command
{
    protected function configure()
    {
        $this
            ->setName('test')
            ->setDescription('Tests both Telegram and SickBeard.');
    }

    protected function execute(In $input, Out $output)
    {
        $res = [];
        $output->writeln('Testing SickBeard...');

        $cmdSickBeard = $this->getApplication()->find('test:sickbeard');
        $cmdTelegram = $this->getApplication()->find('test:telegram');

        $res['sb'] = $cmdSickBeard->run(new ArrayInput([]), $output);
        $res['tg'] = $cmdTelegram->run(new ArrayInput([]), $output);

        $printRes = function($name, $label) use ($res, $output) {
            if ($res[$name] === 0) {
                $output->writeln("{$label}: <info>OK!</>");
            } else {
                $output->writeln("{$label}: <error>Failed</>");
            }
        };

        $output->writeln('');
        $output->writeln('Tests finished');
        $printRes('sb', 'SickBeard');
        $printRes('tg', 'Telegram');
    }
}