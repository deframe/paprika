<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika\Event;

use Paprika\Task\StatusTask;

/**
 * Status event.
 *
 * This is an event specifically tailored for status tasks.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class StatusEvent extends Event
{
    /**
     * Current release.
     *
     * @var integer
     */
    protected $currentRelease;

    /**
     * Number of commits the repository is behind its origin.
     *
     * @var integer
     */
    protected $repoCommitsBehindOrigin;

    /**
     * Create status event.
     *
     * @param \Paprika\Task\StatusTask $task Task that fired the event
     */
    public function __construct(StatusTask $task)
    {
        parent::__construct($task);
    }

    /**
     * Set the current release.
     *
     * @param integer $release Release
     * @return \Paprika\Event\StatusEvent Fluent interface
     */
    public function setCurrentRelease($release)
    {
        $this->currentRelease = $release;

        return $this;
    }

    /**
     * Set the number of commits the repository is behind its origin.
     *
     * @param integer $commits Number of commits
     * @return \Paprika\Event\StatusEvent Fluent interface
     */
    public function setRepoCommitsBehindOrigin($commits)
    {
        $this->repoCommitsBehindOrigin = $commits;

        return $this;
    }

    /**
     * Get the current release.
     *
     * @return integer Current release
     */
    public function getCurrentRelease()
    {
        return $this->currentRelease;
    }

    /**
     * Get the number of commits the repository is behind its origin.
     *
     * @return integer Number of commits
     */
    public function getRepoCommitsBehindOrigin()
    {
        return $this->repoCommitsBehindOrigin;
    }
}