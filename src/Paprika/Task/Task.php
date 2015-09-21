<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika\Task;

use Paprika\Application as Paprika;

/**
 * Paprika task.
 *
 * Tasks constitute the main functionality of the application.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
abstract class Task
{
    /**
     * Paprika instance.
     *
     * @var \Paprika\Application
     */
    protected $paprika;

    /**
     * Task constructor.
     *
     * @param \Paprika\Application $paprika Paprika instance
     */
    public function __construct(Paprika $paprika)
    {
        $this->paprika = $paprika;
    }

    /**
     * Get Paprika instance.
     *
     * @return \Paprika\Application Paprika
     */
    public function getPaprika()
    {
        return $this->paprika;
    }

    /**
     * Run the task.
     *
     * @return void
     */
    public abstract function run();
}