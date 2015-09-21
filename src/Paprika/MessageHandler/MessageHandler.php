<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika\MessageHandler;

/**
 * Paprika message handler.
 *
 * Message handlers are simple classes that do something productive with the
 * messages passed to them from the Paprika application.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
abstract class MessageHandler
{
    /**
     * Info-based messages.
     */
    const TYPE_INFO = 1;

    /**
     * Debug-based messages.
     */
    const TYPE_DEBUG = 2;

    /**
     * Handle message.
     *
     * @param string $message Message to handle
     * @return void
     */
    abstract public function handle($message);
}