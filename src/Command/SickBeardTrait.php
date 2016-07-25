<?php

namespace SickBeard\Telegram\Command;

use Jleagle\SickBeard\Exceptions\SickBeardException;

use SickBeard\Telegram\SickBeardClient;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface as In;
use Symfony\Component\Console\Output\OutputInterface as Out;


/**
 * Contains simple methods to get information from Sickbeard.
 *
 * @author Roelof Roos <github@roelof.io>
 * @license GPL-3.0
 */
trait SickBeardTrait
{
    /**
     * Gets basic info
     *
     * @param SickBeard\Telegram\SickBeardClient $client
     * @return array|Exception
     */
    protected function getFeatures(SickBeardClient $client)
    {
        try {
            $features = $client->sickBeard();
            return $features;
        } catch(SickBeardException $e) {
            return $e;
        }
    }

    /**
     * Simple way to check if the API client works.
     *
     * @param SickBeard\Telegram\SickBeardClient $client
     * @return boolean
     */
    protected function testAccess(SickBeardClient $client) {
        return is_array($this->getFeatures($client));
    }

    /**
     * Returns info about a show
     *
     * @param string $id
     * @param SickBeard\Telegram\SickBeardClient $client
     * @return array|Exception
     */
    protected function getShow($id, SickBeardClient $client)
    {
        try {
            $show = $client->show($id);
            return $show;
        } catch(SickBeardException $e) {
            return $e;
        }
    }
}
