<?php

namespace SickBeard\Telegram;

use Telegram\Bot\Api;

/**
 * Provides easy-access to the Telegram Client singleton
 *
 * @author Roelof Roos <github@roelof.io>
 * @license GPL-3.0
 */
class TelegramClient extends Api
{

    protected $config;

     /**
     * Instantiates a new Telegram super-class object.
     *
     * @param boolean $async Indicates if the request to Telegram will be
     *                       asynchronous (non-blocking).
     * @param HttpClientInterface $httpClientHandler Custom HTTP Client Handler.
     * @throws TelegramSDKException
     */
    public function __construct($async = false, $httpClientHandler = null)
    {
        $this->config = Config::get('telegram');
        parent::__construct($this->config->get('token'));
    }

    /**
     * @return Noodlehaus\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

}