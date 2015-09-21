<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika\Event;

use Paprika\Task\DeployTask;

/**
 * Deploy event.
 *
 * This is an event specifically tailored for deploy tasks.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class DeployEvent extends Event
{
    /**
     * Release to be deployed.
     *
     * @var integer
     */
    protected $release;

    /**
     * Create deploy event.
     *
     * @param \Paprika\Task\DeployTask $task Task that fired the event
     */
    public function __construct(DeployTask $task)
    {
        parent::__construct($task);
    }

    /**
     * Set the release to be deployed.
     *
     * @param integer $release Release
     * @return \Paprika\Event\DeployEvent Fluent interface
     */
    public function setRelease($release)
    {
        $this->release = $release;

        return $this;
    }

    /**
     * Get the release to be deployed.
     *
     * @return integer Release
     */
    public function getRelease()
    {
        return $this->release;
    }
}