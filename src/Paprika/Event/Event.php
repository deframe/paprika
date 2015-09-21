<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika\Event;

use Symfony\Component\EventDispatcher\Event as SymfonyEvent;
use Paprika\Task\Task;

/**
 * Paprika event.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
abstract class Event extends SymfonyEvent
{
    /**
     * The task that fired the event.
     *
     * @var \Paprika\Task\DeployTask
     */
    protected $task;

    /**
     * Create event.
     *
     * @param \Paprika\Task\Task $task Task that fired the event
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Get the task that fired the event.
     *
     * @return \Paprika\Task\Task Task
     */
    public function getTask()
    {
        return $this->task;
    }
}