<?php

namespace SickBeard\Telegram\Command;

use SickBeard\Telegram\TelegramClient;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface as In;
use Symfony\Component\Console\Output\OutputInterface as Out;

use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Objects\Chat;
use Telegram\Bot\Objects\User;

/**
 * Contains simple methods to get information from Telegram and send messages.
 *
 * @author Roelof Roos <github@roelof.io>
 * @license GPL-3.0
 */
trait TelegramTrait
{
    /**
     * Gets the bot itself
     *
     * @param SickBeard\Telegram\TelegramClient $client
     * @return Telegram\Bot\Objects\User|Exception
     */
    protected function getMe(TelegramClient $client)
    {
        try {
            $me = $client->getMe();
            return $me;
        } catch(TelegramSDKException $e) {
            return $e;
        }
    }

    /**
     * Simple way to check if the token works.
     *
     * @param SickBeard\Telegram\TelegramClient $client
     * @return boolean
     */
    protected function testAccess(TelegramClient $client) {
        return $this->getMe($client) instanceof User;
    }

    /**
     * Returns info about a chat
     *
     * @param string $id
     * @param SickBeard\Telegram\TelegramClient $client
     * @return Telegram\Bot\Objects\Chat|Exception
     */
    protected function getChat($id, TelegramClient $client)
    {
        try {
            $chat = $client->getChat(['chat_id' => $id]);
            return $chat;
        } catch(TelegramSDKException $e) {
            return $e;
        }
    }

    /**
     * Returns info about a chat
     *
     * @param string|integer $chatId
     * @param string $message
     * @param SickBeard\Telegram\TelegramClient $client
     * @return boolean
     */
    protected function sendMessage($chatId, $message, TelegramClient $client
    ) {
        try {
            $chat = $client->sendMessage([
                'chat_id' => $chatId,
                'text' => (string) $message
            ]);
            return true;
        } catch(TelegramSDKException $e) {
            return $e;
        }
    }
}
