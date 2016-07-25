<?php

namespace SickBeard\Telegram;

use Noodlehaus\Config as NoodlehausConfig;
use Noodlehaus\Exception\FileNotFoundException;

/**
 * Creates a Noodlehaus Config object.
 *
 * @author Roelof Roos <github@roelof.io>
 * @license GPL-3.0
 */
final class Config
{
    /**
     * @var String sprintf string to use to construct the path to config files.
     * First string should be path of system root, 2nd string should be
     * filename.
     */
    const ConfigBaseFile = '%s/%s.default.json';

    /**
     * @var String sprintf string to use to construct the path to config files.
     * First string should be path of system root, 2nd string should be
     * filename.
     */
    const ConfigUserFile = '?%s/%s.json';

    /**
     * Returns true if this name is valid, false otherwise
     *
     * @param string $name
     * @return boolean
     */
    public static function isValidName($name)
    {
        return is_string($name) && preg_match('/^[a-z0-9\-\_]+$/', $name);
    }

    /**
     * Returns the config. Returns NULL if the given name is not valid (only
     * a-z, 0-9, dashes and underscores are allowed). Throws exception if base
     * config doesn't exist.
     *
     * @param string $name
     * @return \Noddlehaus\Config|null
     */
    public static function get($name)
    {

        if(!self::isValidName($name)) {
            return null;
        }

        // Path in a separate var to keep the sprintfs below cleaner
        $path = dirname(__DIR__) . '/config';

        try {
            // Construct the config using a base and user file
            return new NoodlehausConfig([
                sprintf(self::ConfigBaseFile, $path, $name),
                sprintf(self::ConfigUserFile, $path, $name)
            ]);

        // Only the base file should throw an exception. This is bad,
        // but should not break the system, so only log the error.
        } catch(FileNotFoundException $e) {
            error_log(sprintf(
                'WARNING! Failed to read config file at "%s".',
                sprintf(self::ConfigBaseFile, $path, $name)
            ));

            throw $e;
        }
    }
}
