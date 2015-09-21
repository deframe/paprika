<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika\Event;

use Paprika\Task\RollbackTask;

/**
 * Rollback event.
 *
 * This is an event specifically tailored for rollback tasks.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class RollbackEvent extends Event
{
    /**
     * Release we will roll back from.
     *
     * @var integer
     */
    protected $rollBackFrom;

    /**
     * Release we will roll back to.
     *
     * @var integer
     */
    protected $rollBackTo;

    /**
     * Create rollback event.
     *
     * @param \Paprika\Task\RollbackTask $task Task that fired the event
     */
    public function __construct(RollbackTask $task)
    {
        parent::__construct($task);
    }

    /**
     * Set the release we will roll back from.
     *
     * @param integer $release Release
     * @return \Paprika\Event\RollbackEvent Fluent interface
     */
    public function setRollBackFrom($release)
    {
        $this->rollBackFrom = $release;

        return $this;
    }

    /**
     * Set the release we will roll back to.
     *
     * @param integer $release Release
     * @return \Paprika\Event\RollbackEvent Fluent interface
     */
    public function setRollBackTo($release)
    {
        $this->rollBackTo = $release;

        return $this;
    }

    /**
     * Get the release we will roll back from.
     *
     * @return integer Release
     */
    public function getRollBackFrom()
    {
        return $this->rollBackFrom;
    }

    /**
     * Get the release we will roll back to.
     *
     * @return integer Release
     */
    public function getRollBackTo()
    {
        return $this->rollBackTo;
    }
}