# README

This system was built in PHP and is intended to run in Linux using PHP 7.

Before doing anything make sure you have `composer` installed. Then run `composer install` from the httpdocs directory which contains `composer.json` and `composer.lock`.

# Blakserv.php

**Important**
`blakserv.php` connects to the Maintenance IP/Port using a PHP Telnet class. You must have access to Blakserv's telnet terminal before blakserv will work correctly.

To test your telnet connection, type:  `telnet 127.0.0.1 9998`  then you should see "Connected and Escape ]"...  type:   `show config` to see that you are connected.

To run a blakserv command, try this:   `php blakserv.php save`

You must edit `app\Blakserv\Commands.php` and put in your Maintenance IP and Port (right now it's set to 127.0.0.1 / 9998).

