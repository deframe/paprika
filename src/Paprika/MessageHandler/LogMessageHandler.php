<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika\MessageHandler;

/**
 * Log message handler.
 *
 * Outputs messages to a log.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class LogMessageHandler extends MessageHandler
{
    /**
     * Log filename.
     *
     * @var string
     */
    protected $filename;

    /**
     * Create a log message handler.
     *
     * @param string $filename Log filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Handle message.
     *
     * @param string $message Message to handle
     * @return void
     */
    public function handle($message)
    {
        $logfile = fopen($this->filename, 'a');
        fwrite($logfile, '[' . date('d/m/y H:i:s') . '] ' . $message . "\n");
        fclose($logfile);
    }
}