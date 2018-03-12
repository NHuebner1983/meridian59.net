<?php

/**
 * Before using this script for the first time, please run `composer install`.
 */

require "vendor/autoload.php";

use App\Blakserv\Commands;

$command = "";

if ( isset($_SERVER['argv']) )
{
    // CLI
    $command = trim(str_replace($_SERVER['SCRIPT_FILENAME'], '', join(" ", $_SERVER['argv'])));
}else{
    // Web - Kill it without authentication...
    dd("You are not allowed to run this script in a Web Browser.");
    die(); // paranoid?
    exit(); // wow just in case.. :)
}

if ( ! $command )
{
    dd("No commands to process...");
}

$available_commands = Commands::COMMANDS;

//dd($available_commands);

/**
 * Check if we are allowed to use this Blakserv function first.
 */
foreach($available_commands as $available_command)
{
    if ( isset($available_command['method']) && $available_command['method'] == $command)
    {
        $m59 = new Commands();
        $m59->$command();
        exit();
    }
}

/**
 * Check for allowed game commands
 */


dd(["AVAILABLE COMMANDS" => $available_commands]);
