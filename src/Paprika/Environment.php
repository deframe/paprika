<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika;

use \Paprika\SshConnection;

/**
 * Paprika environment.
 *
 * An environment is essentially an object representation of where a project
 * can or may be deployed to!
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class Environment
{
    /**
     * Label that identifies the environment.
     *
     * @var string
     */
    protected $label;

    /**
     * Git branch that is / will be deployed to the environment.
     *
     * @var string
     */
    protected $gitBranch;

    /**
     * SSH connection used to access the environment.
     *
     * @var \Paprika\SshConnection
     */
    protected $sshConnection;

    /**
     * Pre-task commands. These are executed immediately before any tasks are
     * run.
     */
    protected $preTaskCommands = array();

    /**
     * The directory within the environment where the project is / will be
     * located.
     *
     * @var string
     */
    protected $dir;

    /**
     * The number of deployment releases to retain for this environment.
     *
     * @var integer
     */
    protected $releasesToRetain = 5;

    /**
     * Create an environment.
     *
     * @param string $label Environment label
     * @param string $gitBranch Git branch that is / will be deployed to the environment
     * @param \Paprika\SshConnection $sshConnection SSH connection used to access the environment
     * @param string $dir The directory within the environment where the project is / will be located
     */
    public function __construct($label, $gitBranch, SshConnection $sshConnection, $dir)
    {
        $this->label         = $label;
        $this->gitBranch     = $gitBranch;
        $this->sshConnection = $sshConnection;
        $this->dir           = $dir;
    }

    /**
     * Get the label that identifies the environment.
     *
     * @return string Label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get the Git branch that is / will be deployed to the environment.
     *
     * @return string
     */
    public function getGitBranch()
    {
        return $this->gitBranch;
    }

    /**
     * Get the SSH connection used to access the environment.
     *
     * @return \Paprika\SshConnection
     */
    public function getSshConnection()
    {
        return $this->sshConnection;
    }

    /**
     * Add a pre-task command.
     *
     * @param string $command Command to execute
     * @return \Paprika\Environment Fluent interface
     */
    public function addPreTaskCommand($command)
    {
        $this->preTaskCommands[] = $command;
    }

    /**
     * Get the pre-task commands.
     *
     * @return array Commands
     */
    public function getPreTaskCommands()
    {
        return $this->preTaskCommands;
    }

    /**
     * Determine if the environment has one or more pre-task commands.
     *
     * @return boolean Whether one or more pre-task commands exist
     */
    public function hasPreTaskCommands()
    {
        return !empty($this->preTaskCommands);
    }

    /**
     * Get the directory within the environment where the project is / will be
     * located.
     *
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * Set the number of deployment releases to retain for this environment.
     *
     * @param integer $number Number of releases
     * @return \Paprika\Environment Fluent interface
     */
    public function setReleasesToRetain($number)
    {
        $this->releasesToRetain = $number;

        return $this;
    }

    /**
     * Get the number of deployment releases to retain for this environment.
     *
     * @return integer Number of releases
     */
    public function getReleasesToRetain()
    {
        return $this->releasesToRetain;
    }
}
