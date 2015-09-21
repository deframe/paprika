<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika\MessageHandler;

use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Console message handler.
 *
 * Outputs messages to the CLI.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class ConsoleMessageHandler extends MessageHandler
{
    /**
     * Console output engine.
     *
     * @var \Symfony\Component\Console\Output\ConsoleOutput
     */
    protected $consoleOutput;

    /**
     * Create a console message handler.
     *
     * @param \Symfony\Component\Console\Output\ConsoleOutput $consoleOutput Console output engine
     */
    public function __construct(ConsoleOutput $consoleOutput)
    {
        $this->consoleOutput = $consoleOutput;
    }

    /**
     * Handle message.
     *
     * @param string $message Message to handle
     * @return void
     */
    public function handle($message)
    {
        $this->consoleOutput->writeln($message);
    }
}