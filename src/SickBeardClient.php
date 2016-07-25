<?php

namespace SickBeard\Telegram;

use Jleagle\SickBeard\SickBeard;

/**
 * Handles calls to SickBeard.
 *
 * @author Roelof Roos <github@roelof.io>
 * @license GPL-3.0
 */
class SickBeardClient extends SickBeard
{
    protected $config;

     /**
     * Creates a SickBeard API client from the configs.
     */
    public function __construct()
    {
        $this->config = Config::get('sickbeard');
        parent::__construct(
            $this->config->get('url'),
            $this->config->get('api-key')
        );
    }

    /**
     * @return Noodlehaus\Config
     */
    public function getConfig()
    {
        return $this->config;
    }
}