<?php

namespace App\Blakserv;

use Bestnetwork\Telnet\TelnetClient;

class Commands {

    public $blakserv;

    const HOST    = '127.0.0.1';
    const PORT    = 9998;
    const TIMEOUT = 5;

    const RELOAD_SYSTEM = [
        'command' => 'reload system',
        'success' => 'Loading game',
        'fail'    => "couldn't",
    ];

    const SAVE = [
        'command' => 'save game',
        'success' => 'done.',
        'fail'    => "Save time is (0)",
    ];

    const KICK_ALL = [
        'command' => 'kickoff all',
        'success' => 'kickoff all',
        'fail'    => "couldn't",
    ];

    const HANGUP_ALL = [
        'command' => 'hangup all',
        'success' => 'hang',
        'fail'    => "couldn't",
    ];

    const RECREATE_ALL = [
        'command' => 'send o 0 recreateall',
        'success' => 'Please wait about',
        'fail'    => "Failure",
    ];

    const LOCK = [
        'command' => 'lock',
        'success' => 'Locking game',
        'fail'    => "Failure",
    ];

    const UNLOCK = [
        'command' => 'unlock',
        'success' => 'Unlocking game',
        'fail'    => "Game isn't locked",
    ];

    const TERMINATE_SAVE = [
        'command' => 'terminate save',
        'success' => 'Terminating',
        'fail'    => "????",
    ];

    const TERMINATE_NOSAVE = [
        'command' => 'terminate nosave',
        'success' => 'Terminating',
        'fail'    => "????",
    ];

    /**
     * Packaged commands (routines)
     */
    const ROUTINE_RELOAD_AND_RECREATE = [
        'method' => 'reloadAndRecreate',
    ];
    const ROUTINE_SAVE                = [
        'method' => 'save',
    ];

    /**
     * This makes commands available from the command line as an argument...
     */
    const COMMANDS = [

        /**
         * Blakserv commands available to this script
         */
        self::RELOAD_SYSTEM,
        self::SAVE,
        self::KICK_ALL,
        self::RECREATE_ALL,
        self::LOCK,
        self::UNLOCK,
        self::TERMINATE_SAVE,
        self::TERMINATE_NOSAVE,

        /**
         * Routines available by arguments
         */
        self::ROUTINE_RELOAD_AND_RECREATE,
        self::ROUTINE_SAVE,
    ];

    public function __construct($custom_timeout = self::TIMEOUT)
    {
        $this->blakserv = new TelnetClient(self::HOST, self::PORT, $custom_timeout ?: self::TIMEOUT);
        $this->blakserv->connect();
    }

    public function __destruct()
    {
        try
        {
            $this->blakserv->disconnect();
        }
        catch ( \Exception $e )
        {
            return;
        }
    }

    public function exec($command = [])
    {
        return $this->blakserv->execute($command['command'], $command['success'], $command['fail']);
    }

    public function save()
    {
        $this->exec(self::SAVE);
    }

    public function reload()
    {
        $this->exec(self::RELOAD_SYSTEM);
    }

    public function kickAll()
    {
        $this->exec(self::KICK_ALL);
    }

    public function terminate($save = true)
    {
        $this->exec($save ? self::TERMINATE_SAVE : self::TERMINATE_NOSAVE);
    }

    public function reconnect()
    {
        $this->__construct();
    }

    public function reloadAndRecreate()
    {
        $this->exec(self::LOCK);
        sleep(2);
        $this->exec(self::HANGUP_ALL);
        $this->reconnect();
        sleep(2);
        $this->exec(self::RELOAD_SYSTEM);
        sleep(2);
        $this->exec(self::RECREATE_ALL);
        sleep(5);
        $this->exec(self::UNLOCK);
        sleep(2);
        $this->exec(self::SAVE);
        sleep(2);
    }

}
