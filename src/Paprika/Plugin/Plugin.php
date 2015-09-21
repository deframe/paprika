<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika\Plugin;

use Paprika\Application as Paprika;

/**
 * Paprika plugin.
 *
 * Paprika plugins essentially listen for triggered events from tasks and
 * respond accordingly.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
interface Plugin
{
    /**
     * Plugin initialization.
     *
     * @param \Paprika\Application $paprika Paprika instance
     * @return void
     */
    public function init(Paprika $paprika);
}