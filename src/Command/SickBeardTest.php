<?php

namespace SickBeard\Telegram\Command;

use Jleagle\SickBeard\Exceptions\SickBeardException;

use SickBeard\Telegram\SickBeardClient;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface as In;
use Symfony\Component\Console\Output\OutputInterface as Out;

/**
 * Tests if Telegram can send messages to users
 *
 * @author Roelof Roos <github@roelof.io>
 * @license GPL-3.0
 */
class SickBeardTest extends Command
{
    use SickBeardTrait;

    protected function configure()
    {
        $this
            ->setName('test:sickbeard')
            ->setDescription('Tests if sickbeard can be accessed for information.');
    }

    protected function execute(In $input, Out $output)
    {
        $client = new SickBeardClient;

        $output->writeln('Testing access token...');

        // Test 1: /me
        $me = $this->getFeatures($client);

        if ($me instanceof SickBeardException) {
            if ($me->getCode() === 403) {
                $output->writeln('<error>The access token appears to be invalid.</error>');
            } else {
                $output->writeln("Access check failed: <error>{$me->getMessage()}</>");
            }
            return false;
        }

        // Report OK
        $output->writeln('<info>Access token for SickBeard works!</>');

        // Retrieve Doctor Who
        $id = 78804;

        $output->writeln("[{$id}] Requesting info on <comment>{$id}</>...");

        $chat = $this->getShow($id, $client);

        if ($chat instanceof SickBeardException) {
            $output->writeln("[{$id}] <error>{$chat->getMessage()}</>");
            return false;
        }

        $output->writeln("[{$id}] Retrieved show <info>{$chat['show_name']}</>");

        return true;

    }
}