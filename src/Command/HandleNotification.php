<?php

namespace SickBeard\Telegram\Command;

use SickBeard\Telegram\TelegramClient;
use SickBeard\Telegram\SickBeardClient;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface as In;
use Symfony\Component\Console\Output\OutputInterface as Out;

/**
 * Tests if Telegram can send messages to users
 *
 * @author Roelof Roos <github@roelof.io>
 * @license GPL-3.0
 */
class HandleNotification extends Command
{
    use TelegramTrait, SickBeardTrait {
        TelegramTrait::testAccess insteadof SickBeardTrait;
        TelegramTrait::testAccess as telegramAccess;
        SickBeardTrait::testAccess as sickBeardAccess;
    }

    protected function configure()
    {
        $this
            ->setName('handle')
            ->setDescription('Handles notifications.');

        $args = [
            ['final-path', true, 'Final full path to the episode file'],
            ['original-path', true, 'Original name of the episode file'],
            ['show-id', true, 'Show tvdb id'],
            ['season', true, 'Season number'],
            ['episode', true, 'Episode number'],
            ['air-date', false, 'Episode air date']
        ];

        foreach ($args as list($name, $req, $desc)) {
            $this->addArgument(
                $name,
                $req ? InputArgument::REQUIRED : InputArgument::OPTIONAL,
                $desc
            );
        }
    }

    /**
     * Retrieves the name of a show from SickBeard
     *
     * @param string $id ID of the show to retrieve
     * @param SickBeard\Telegram\SickBeardClient $client Client to use
     * @return string|null
     */
    protected function getShowName($id, SickBeardClient $client)
    {
        $show = $this->getShow($id, $client);

        if ($show instanceof SickBeardException) {
            throw new \RuntimeException(sprintf(
                'Failed to get intel for %s: %s (%d)',
                $id, $show->getMessage(), $show->getCode()
            ), $show->getCode(), $show);
        }

        return isset($show['show_name']) ? $show['show_name'] : null;
    }

    /**
     * Sends a message to the recipients in the Telegram client
     *
     * @param string $message Message to send
     * @param SickBeard\Telegram\TelegramClient $client Client to use
     * @return boolean
     */
    protected function broadcastMessage($message, TelegramClient $client)
    {
        $recipients = $client->getConfig()->get('target');

        if (!is_array($recipients)) {
            throw new \UnexpectedValueException(sprintf(
                'Expected the target list to be an array, but got %s',
                gettype($recipients)
            ));
        }

        // Get recipient count
        $recipientCount = count($recipients);

        foreach ($recipients as $id) {
            $this->sendMessage($id, $message, $client);
        }

        return true;
    }

    protected function execute(In $input, Out $output)
    {
        $telegram = new TelegramClient;
        $sickbeard = new SickBeardClient;

        // STEP 1: Check access
        if ($output->isVeryVerbose()) {
            $output->writeln('Checking SickBeard and Telegram functionality...');
        }

        if (
            !$this->telegramAccess($telegram) ||
            !$this->sickBeardAccess($sickbeard)
        ) {
            throw new \RuntimeException(
                "Configuration for Telegram or SickBeard is invalid!"
            );
            return false;
        }
        if ($output->isVerbose()) {
            $output->writeln('SickBeard and Telegram function <info>OK</>.');
        }

        // STEP 2: Get arguments
        $showId = $input->getArgument('show-id');
        $seasonNo = $input->getArgument('season');
        $episodeNo = $input->getArgument('episode');

        if ($output->isVeryVerbose()) {
            $output->writeln('Validating arguments...');
        }

        $isInt = function($x) {
            if (is_int($x) || is_float($x) || ctype_digit($x)) {
                return true;
            }
            return false;
        };

        if ($showId === null || !$isInt($showId)) {
            throw new \InvalidArgumentException(
                'Expected show ID to be an integer, ' .
                'but it\'s missing or not an integer.',
                400
            );
        } elseif ($output->isVerbose()) {
            $output->writeln('Show ID <info>valid</>.');
        }

        if ($seasonNo === null || !$isInt($seasonNo)) {
            throw new \InvalidArgumentException(
                'Expected season number to be an integer, ' .
                'but it\'s missing or not an integer.',
                400
            );
        } elseif ($output->isVerbose()) {
            $output->writeln('Season number <info>valid</>.');
        }

        if ($episodeNo === null || !$isInt($episodeNo)) {
            throw new \InvalidArgumentException(
                'Expected episode to be an integer, ' .
                'but it\'s missing or not an integer.',
                400
            );
        } elseif ($output->isVerbose()) {
            $output->writeln('Episode number <info>valid</>.');
        }

        // Step 3: get name
        if ($output->isVeryVerbose()) {
            $output->writeln("Receiving details for <comment>{$showId}</>...");
        }

        try {
            $showName = $this->getShowName($showId, $sickbeard);
        } catch(\RuntimeException $e) {
            $showName = null;
        }

        // Step 3b: Fallback
        if (empty($showName)) {
            $showName = "Unknown Show ({$showId})";
        }

        // Debug
        if ($output->isVerbose()) {
            $output->writeln("Recieved information about <comment>{$showName}</>.");
        }

        // Step 4: Prepare message
        $message = sprintf(
            'Downloaded %s: S%02dE%02d.',
            $showName, $seasonNo, $episodeNo
        );

        // Step 5: Send message

        if ($output->isVeryVerbose()) {
            $output->writeln("Sending <comment>{$message}</>...");
        }
        $this->broadcastMessage($message, $telegram);

        $output->writeln('<comment>All messages sent!</>');

        return 0;
    }
}