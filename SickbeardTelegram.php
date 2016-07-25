<?php

use Symfony\Component\Console\Application;
use SickBeard\Telegram\Command\TelegramTest;
use SickBeard\Telegram\Command\SickbeardTest;
use SickBeard\Telegram\Command\HandleNotification;

// Include autoloader
require_once __DIR__ . '/vendor/autoload.php';


// Get version from composer
$composerFile = __DIR__ . '/composer.json';
$composerVersion = null;

if (is_readable($composerFile)) {
    $data = file_get_contents($composerFile);
    $json = json_decode($data);

    if (json_last_error() === JSON_ERROR_NONE) {
        if (property_exists($json, 'version')) {
            $composerVersion = (string) $json->version;
        }
    }
}

// Fire up app
$app = new Application;
$app->setName('Sickbeard Telegram notifier');
if ($composerVersion !== null) {
    $app->setVersion($composerVersion);
}

// TODO Add commands

$app->run();
