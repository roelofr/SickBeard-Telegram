<?php

namespace SickBeard\Telegram\Command;

use SickBeard\Telegram\TelegramClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface as In;
use Symfony\Component\Console\Output\OutputInterface as Out;

use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Chat;

/**
 * Tests if Telegram can send messages to users
 *
 * @author Roelof Roos <github@roelof.io>
 * @license GPL-3.0
 */
class TelegramTest extends Command
{
    use TelegramTrait;

    protected function configure()
    {
        $this
            ->setName('test:telegram')
            ->setDescription('Tests if telegram can send messages to users.');
    }

    protected function execute(In $input, Out $output)
    {
        $client = new TelegramClient;

        $output->writeln('Testing access token...');

        // Test 1: /me
        $me = $this->getMe($client, $output);

        if ($me instanceof TelegramResponseException) {
            if ($me->getCode() === 403) {
                $output->writeln('<error>The access token appears to be invalid.</error>');
            } else {
                $output->writeln("<info>/me</> failed: <error>{$me->getMessage()}</>");
            }
            return false;
        }

        // Report OK
        $output->writeln('<info>Access token for Telegram works!</>');

        // Get recipients
        $recipients = $client->getConfig()->get('target');

        if (!is_array($recipients)) {
            throw new \UnexpectedValueException(sprintf(
                'Expected $recipients to be an array, but got %s.',
                gettype($recipients)
            ));
        }

        // Get recipient count
        $recipientCount = count($recipients);

        $output->writeln("Sending message to <info>{$recipientCount}</> recievers.");

        $message = 'Aww yeah, you set up the plugin!';

        $count = 0;
        foreach ($recipients as $id) {
            $count++;

            $chat = $this->getChat($id, $client, $output);

            if ($chat instanceof TelegramSDKException) {
                $output->writeln("[{$id}] <error>{$chat->getMessage()}</>");
                continue;
            }

            if (!empty($chat->getFirstName())) {
                $name = $chat->getFirstName();
            } elseif (!empty($chat->getTitle())) {
                $name = $chat->getTitle();
            } elseif (!empty($chat->getUsername())) {
                $name = $chat->getUsername();
            } else {
                $name = "unknown";
            }

            $output->writeln("[{$id}] Chatting with {$name}");


            if ($chat) {
                if($this->sendMessage($id, $message, $client)) {
                    $output->writeln("[{$id}] Sent <info>OK</>");
                } else {
                    $output->writeln("[{$id}] Sent <comment>failed</>");
                }
            }
        }

        return true;
    }
}