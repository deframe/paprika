<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika\Plugin;

use Paprika\Application as Paprika;
use Paprika\Event\DeployEvent;

/**
 * Laravel plugin.
 *
 * Adds Laravel-specific deployment functionality.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class LaravelPlugin implements Plugin
{
    /**
     * Plugin initialization.
     *
     * @param \Paprika\Application $paprika Paprika instance
     * @return void
     */
    public function init(Paprika $paprika)
    {
        $eventDispatcher = $paprika->getEventDispatcher();

        // Deploy event listeners.

        $eventDispatcher->addListener('task.deploy.created_release', function(DeployEvent $event) use ($paprika) {

            $release = $event->getRelease();

            $task        = $event->getTask();
            $paprika     = $task->getPaprika();
            $environment = $paprika->getEnvironment($task->getEnvironmentLabel());

            $releaseDir = $environment->getDir() . '/releases/' . $release;

            $command = "chmod 777 -R " . $releaseDir . "/app/storage" . "\n";
            $response = $environment->getSshConnection()->exec($command);
            $paprika->sshCommandDebugMessage($command, $response);
            $paprika->message('Set writable permissions on /app/storage directory.');

        });
    }
}