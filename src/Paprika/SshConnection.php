<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika;

/**
 * SSH connection.
 *
 * Creates an SSH connection to a remote server and allows commands to be run
 * on it.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class SshConnection
{
    /**
     * Server host.
     *
     * @var string
     */
    protected $host;

    /**
     * Server port.
     *
     * @var integer
     */
    protected $port = 22;

    /**
     * Username to login as.
     *
     * @var string
     */
    protected $username;

    /**
     * Password to login with.
     *
     * @var string
     */
    protected $password;

    /**
     * SSH engine instance.
     *
     * @var \Net_SSH2
     */
    private $engine;

    /**
     * Create an SSH connection.
     *
     * Note: the actual connection happens lazily, just before the first
     * command is set to the server.
     *
     * @param string $host Server host
     */
    public function __construct($host)
    {
        $this->host = $host;
    }

    /**
     * Get the server host.
     *
     * @return string Host
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set the server port.
     *
     * @param integer $port Server port
     * @return \Paprika\SshConnection Fluent interface
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get the server port.
     *
     * @return integer Port
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set the username to login as.
     *
     * @param string $username Username
     * @return \Paprika\SshConnection Fluent interface
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the username to login as.
     *
     * @return string Username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the password to login with.
     *
     * @param string $password Password
     * @return \Paprika\SshConnection Fluent interface
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the password to login with.
     *
     * @return string Password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Connect and log in to the SSH server.
     *
     * @return void
     * @throws \Exception if the SSH connection fails
     */
    protected function connect()
    {
        set_error_handler(function($errno, $errstr) {
            throw new \Exception('Could not connect to SSH server ([' . $errno . '] ' . $errstr . ')');
        });

        $this->engine = new \Net_SSH2($this->host, $this->port);

        restore_error_handler();

        if (!$this->engine->login($this->username, $this->password)) {
            throw new \Exception('Could not login to SSH server');
        }
    }

    /**
     * Execute a command via the SSH connection and return any output.
     *
     * @param string $command Command to execute
     * @return string Command output
     */
    public function exec($command)
    {
        if (!isset($this->engine)) {
            $this->connect();
        }

        $this->engine->exec($command, false);

        $stdOut = '';

        while (true) {
            $line = $this->engine->_get_channel_packet(NET_SSH2_CHANNEL_EXEC);
            if ($line === true) {
                break;
            }
            $stdOut .= $line;
        }

        return $stdOut;
    }
}