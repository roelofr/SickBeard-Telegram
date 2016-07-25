# Sickbeard-Telegram

[![Build status][shield-build]][link-build]
[![Code coverage][shield-cover]][link-cover]
[![PHP 5.6+][shield-php]][link-php]
[![GPL-v3 license][shield-license]][license]

Reports SickBeard downloads to you or a Telegram group using the Telegram Bot
API.

## Requirements

 - PHP 5.6 or higher (5.5 and lower not officially supported)
 - Composer
 - cURL

## Installation

### Creating a bot.

Please contact [@BotFather][] to create a bot. As the bot will only be
sending messages, it doesn't need any privacy permissions or support inline use.
You'll need to ask for an access token using the `/token` command and keep this
on hand for a bit.

### Getting an API key from Sickbeard

You'll need to take note of the web address and API token of Sickbeard. This is
required to display names of shows that have been downloaded.

### Creating the configs

For both SickBeard and Telegram there are `.default.json` files. You can
override individual options by creating `sickbeard.json` and `telegram.json`
respectively.

The recommended contents of `sickbeard.json` and `telegram.json`:

`sickbeard.json`:
 -  `api-key` - The API key to connect with
 -  `host` - The hostname the server is available at, with protocol.

`telegram.json`:
 -  `token` - The access token, as received from [@BotFather][]
 -  `target` - An array of chats that should recieve updates. You can use the
    [@MyIdBot][] to get the ID of a user or group chat. Each chat will receive
    it's own message.

**Important**: Telegram won't let a bot initiate a conversation with a bot, nor
will it be allowed to add itself to a group. Therefore, you have to make sure
the bot is added to a group chat or a conversation has been started before
firing up the bot.

The application *will not* abort the notification process if one message fails
to send. It will simply flag it as a failure and continue like nothing has
happened.

### Testing it

To make sure everything works, you can use the tests built into the app.

 - To test the SickBeard connection, use `./app test:sickbeard`
 - To test the Telegram connection, use `./app test:telegram`
 - To run all tests at once, use `./app test`

### Enabling it

Since this isn't a built-in plugin for SickBeard, you'll need to edit your
`config.ini` and add the `app` file to your `extra_scripts` config property.

**Important:** You'll need to make this change when SickBeard is *not* running.
If you change it while SickBeard is running, your changes will be disregarded.

```ini
[General]
# More configs...
extra_scripts = "/path/to/repository/app"
```

## License

The program is licensed under [GPL-v3][license].

## Contributing

You're free to contribute. Please see the [styleguide][] for coding standards
and please use an editor that adheres to `.editorconfig` files, or
[install a plugin][editorconfig]. Please pick up issues if there are any, as
those are often higher priority than new features.

<!-- Shield images -->
[shield-build]: https://img.shields.io/travis/roelofr/Sickbeard-Telegram.svg
[shield-cover]: https://img.shields.io/coveralls/roelofr/Sickbeard-Telegram.svg
[shield-php]: https://img.shields.io/badge/PHP-5.6%2B-8892BF.svg
[shield-license]: https://img.shields.io/github/license/roelofr/Sickbeard-Telegram.svg

<!-- Shield links -->
[link-build]: https://travis-ci.org/roelofr/SickBeard-Telegram/settings
[link-cover]: https://coveralls.io/github/roelofr/SickBeard-Telegram
[link-php]: https://secure.php.net/supported-versions.php

<!-- Telegram chats -->
[@BotFather]: https://telegram.me/BotFather
[@MyIdBot]: https://telegram.me/MyIdbot

<!-- Other links -->
[license]: LICENSE
[styleguide]: STYLEGUIDE.md
[editorconfig]: http://editorconfig.org/#download

