<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika\Task;

use Paprika\Application as Paprika;
use Paprika\Event\RollbackEvent;

/**
 * Rollback task.
 *
 * This will rollback an application to its previous release.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class RollbackTask extends Task
{
    /**
     * The label of the environment that the application is deployed to.
     *
     * @var string
     */
    protected $environmentLabel;

    /**
     * Construct a rollback task.
     *
     * @param \Paprika\Application $paprika Paprika instance
     * @param string $environmentLabel Label of the environment that the application is deployed to.
     */
    public function __construct(Paprika $paprika, $environmentLabel)
    {
        parent::__construct($paprika);

        $this->environmentLabel = $environmentLabel;
    }

    /**
     * Run the task.
     *
     * @return void
     * @throws \Exception if the environment does not exist
     */
    public function run()
    {
        $event = new RollbackEvent($this);

        $paprika     = $this->paprika;
        $environment = $paprika->getEnvironment($this->environmentLabel);

        if (is_null($environment)) {
            throw new \Exception("Environment '" . $this->environmentLabel . "' does not exist!");
        }

        // Start the task.

        $paprika->dispatch('task.rollback.started', $event);
        $paprika->message('Rolling back "' . $paprika->getName() . '" (' . $environment->getLabel() . ') to its previous release...');

        // Run any pre-task commands for the environment.

        if ($environment->hasPreTaskCommands()) {
            foreach ($environment->getPreTaskCommands() as $command) {
                $response = $environment->getSshConnection()->exec($command);
                $paprika->sshCommandDebugMessage($command, $response);
            }
            $paprika->message('Finished running pre-task SSH commands.');
        }

        // Get the release we will roll back from.

        $command  = "basename `readlink -f " . $environment->getDir() . "/current`";
        $response = $environment->getSshConnection()->exec($command);
        $paprika->sshCommandDebugMessage($command, $response);

        if (preg_match('/^[0-9]{10}$/', trim($response))) {
            $rollBackFrom     = trim($response);
            $rollBackFromTime = new \DateTime('@' . $rollBackFrom);
        } else {
            $paprika->message('Current release does not appear to exist!');
            return;
        }

        $event->setRollBackFrom($rollBackFrom);

        $paprika->message(
            'Current release: ' . $rollBackFrom . ' (created on '
            . $rollBackFromTime->format('jS F Y \a\t H:i:s')
            . ')'
        );

        // Get a list of all releases.

        $command  = "ls -1 " . $environment->getDir() . "/releases | sort";
        $response = $environment->getSshConnection()->exec($command);
        $paprika->sshCommandDebugMessage($command, $response);

        $responseLines = explode("\n", trim($response));
        $releases      = array();

        foreach ($responseLines as $responseLine) {
            if (preg_match('/^\d{10}$/', trim($responseLine))) {
                $releases[] = trim($responseLine);
            }
        }

        if (empty($releases)) {
            $paprika->message('There do not appear to be any releases!');
            return;
        }

        // Determine the release we will roll back to.

        foreach ($releases as $k => $release) {
            if ($release == $rollBackFrom) {
                if (isset($releases[$k - 1])) {
                    $rollBackTo = $releases[$k - 1];
                }
                break;
            }
        }

        if (!isset($rollBackTo)) {
            $paprika->message('A previous release does not exist - there is nothing to roll back to!');
            return;
        }

        $event->setRollBackTo($rollBackTo);

        $rollBackToTime = new \DateTime('@' . $rollBackTo);

        $paprika->message(
            'Previous release: ' . $rollBackTo . ' (created on '
            . $rollBackToTime->format('jS F Y \a\t H:i:s')
            . ')'
        );

        // Activate that release.

        $command = "ln -sfn " . $environment->getDir() . "/releases/" . $rollBackTo . " " . $environment->getDir() . "/current" . "\n";
        $response = $environment->getSshConnection()->exec($command);
        $paprika->sshCommandDebugMessage($command, $response);

        $paprika->message('Activated previous release.');

        // Finish up.

        $paprika->dispatch('task.rollback.finished', $event);
        $paprika->message('Rollback complete!');
    }
}
