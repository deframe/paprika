<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika\Task;

use Paprika\Application as Paprika;
use Paprika\Event\StatusEvent;

/**
 * Status task.
 *
 * This will display the current status of an environment.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class StatusTask extends Task
{
    /**
     * The label of the environment that the application is deployed to.
     *
     * @var string
     */
    protected $environmentLabel;

    /**
     * Construct a status task.
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
     * Get the label of the environment that the application is deployed to.
     *
     * @return string Environment label
     */
    public function getEnvironmentLabel()
    {
        return $this->environmentLabel;
    }

    /**
     * Run the task.
     *
     * @return void
     * @throws \Exception if the environment does not exist
     */
    public function run()
    {
        $event = new StatusEvent($this);

        $paprika     = $this->paprika;
        $environment = $paprika->getEnvironment($this->environmentLabel);

        if (is_null($environment)) {
            throw new \Exception("Environment '" . $this->environmentLabel . "' does not exist!");
        }

        // Start the task.

        $paprika->dispatch('task.status.started', $event);
        $paprika->message('Checking the status of "' . $paprika->getName() . '" on its ' . $environment->getLabel() . ' environment...');

        // Run any pre-task commands for the environment.

        if ($environment->hasPreTaskCommands()) {
            foreach ($environment->getPreTaskCommands() as $command) {
                $response = $environment->getSshConnection()->exec($command);
                $paprika->sshCommandDebugMessage($command, $response);
            }
            $paprika->message('Finished running pre-task SSH commands.');
        }

        // Determine the current release.

        $command  = "basename `readlink -f " . $environment->getDir() . "/current`";
        $response = $environment->getSshConnection()->exec($command);
        $paprika->sshCommandDebugMessage($command, $response);

        if (preg_match('/^[0-9]{10}$/', trim($response))) {
            $currentRelease     = trim($response);
            $currentReleaseTime = new \DateTime('@' . $currentRelease);
            $event->setCurrentRelease($currentRelease);
            $paprika->message(
                'Current release: ' . $currentRelease . ' (created on '
                . $currentReleaseTime->format('jS F Y \a\t H:i:s')
                . ')'
            );
        } else {
            $event->setCurrentRelease(null);
            $paprika->message('There is no current build in place!');
        }

        // Determine the status of the repository.

        $command = "cd " . $environment->getDir() . "/repo";
        $response = $environment->getSshConnection()->exec($command);
        $paprika->sshCommandDebugMessage($command, $response);

        if (trim($response) != '') {

            $event->setRepoCommitsBehindOrigin(null);
            $paprika->message('A working copy of the repository does not appear to exist on the server!');

        } else {

            $command = "cd " . $environment->getDir() . "/repo && git fetch && git status | grep -oE 'by ([0-9]+) commit' | grep -oE [0-9]+";
            $response = $environment->getSshConnection()->exec($command);
            $paprika->sshCommandDebugMessage($command, $response);

            $repoCommitsBehindOrigin = intval(trim($response));
            $event->setRepoCommitsBehindOrigin($repoCommitsBehindOrigin);

            if ($repoCommitsBehindOrigin > 0) {
                $paprika->message('The working copy of the repository is ' . $repoCommitsBehindOrigin . ' commits behind its origin.');
            } else {
                $paprika->message('The working copy of the repository is up-to-date with its origin.');
            }

        }

        // Finish up.

        $paprika->dispatch('task.status.finished', $event);
    }
}
