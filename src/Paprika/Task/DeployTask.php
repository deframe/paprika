<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika\Task;

use Paprika\Application as Paprika;
use Paprika\Event\DeployEvent;

/**
 * Deployment task.
 *
 * This will deploy an application to a given environment.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class DeployTask extends Task
{
    /**
     * The label of the environment that the application will be deployed to.
     *
     * @var string
     */
    protected $environmentLabel;

    /**
     * Construct a deployment task.
     *
     * @param \Paprika\Application $paprika Paprika instance
     * @param string $environmentLabel Label of the environment that the application will be deployed to.
     */
    public function __construct(Paprika $paprika, $environmentLabel)
    {
        parent::__construct($paprika);

        $this->environmentLabel = $environmentLabel;
    }

    /**
     * Get the label of the environment that the application will be deployed to.
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
        $event = new DeployEvent($this);

        $paprika     = $this->paprika;
        $environment = $paprika->getEnvironment($this->environmentLabel);

        if (is_null($environment)) {
            throw new \Exception("Environment '" . $this->environmentLabel . "' does not exist!");
        }

        $release = time();
        $event->setRelease($release);

        // Start the task.

        $paprika->dispatch('task.deploy.started', $event);
        $paprika->message('Deploying "' . $paprika->getName() . '" to its ' . $environment->getLabel() . ' environment...');

        // Run any pre-task commands for the environment.

        if ($environment->hasPreTaskCommands()) {
            foreach ($environment->getPreTaskCommands() as $command) {
                $response = $environment->getSshConnection()->exec($command);
                $paprika->sshCommandDebugMessage($command, $response);
            }
            $paprika->message('Finished running pre-task SSH commands.');
        }

        // Update (or create) the repository on the remote server.

        $command = "mkdir -p " . $environment->getDir() . "/repo && ls -A " . $environment->getDir() . "/repo | wc -l";
        $response = $environment->getSshConnection()->exec($command);
        $paprika->sshCommandDebugMessage($command, $response);

        if (trim($response) === '0') {
            $command = "git clone " . $paprika->getGitRepository()->getLocation() . " " . $environment->getDir() . "/repo";
            $response = $environment->getSshConnection()->exec($command);
            $paprika->sshCommandDebugMessage($command, $response);
        }

        $command = "cd " . $environment->getDir() . "/repo && git checkout " . $environment->getGitBranch();
        $response = $environment->getSshConnection()->exec($command);
        $paprika->sshCommandDebugMessage($command, $response);

        $paprika->dispatch('task.deploy.updated_repository', $event);
        $paprika->message('Refreshed / created the remote repository.');

        // Create release.

        $command = "mkdir -p " . $environment->getDir() . "/releases/" . $release;
        $response = $environment->getSshConnection()->exec($command);
        $paprika->sshCommandDebugMessage($command, $response);

        $command = "cd " . $environment->getDir() . "/repo && git checkout-index -f -a --prefix=../releases/" . $release . "/";
        $response = $environment->getSshConnection()->exec($command);
        $paprika->sshCommandDebugMessage($command, $response);

        $paprika->dispatch('task.deploy.created_release', $event);
        $paprika->message('Created release.');

        // Create shared file symlinks.

        $command = "mkdir -p " . $environment->getDir() . "/shared";
        $response = $environment->getSshConnection()->exec($command);
        $paprika->sshCommandDebugMessage($command, $response);

        $sharedFileSymlinks = $paprika->getSharedFileSymlinks();

        if (!empty($sharedFileSymlinks)) {
            foreach ($sharedFileSymlinks as $source => $target) {
                $command = "mkdir -p " . $environment->getDir() . "/shared/" . $source
                    . " && mkdir -p `dirname " . $environment->getDir() . "/releases/" . $release . "/" . $target . "`"
                    . " && ln -sfn " . $environment->getDir() . "/shared/" . $source . " " . $environment->getDir() . "/releases/" . $release . "/" . $target;
                $response = $environment->getSshConnection()->exec($command);
                $paprika->sshCommandDebugMessage($command, $response);
            }
            $paprika->dispatch('task.deploy.created_shared_file_symlinks', $event);
            $paprika->message('Created shared file symlinks.');
        }

        // Activate the release.

        $command = "ln -sfn " . $environment->getDir() . "/releases/" . $release . " " . $environment->getDir() . "/current";
        $response = $environment->getSshConnection()->exec($command);
        $paprika->sshCommandDebugMessage($command, $response);

        $paprika->dispatch('task.deploy.activated_release', $event);
        $paprika->message('Activated release.');

        // Ensure we only retain the requested number of releases.

        if ($environment->getReleasesToRetain() > 0) {

            $command  = "ls -1 " . $environment->getDir() . "/releases | sort -r";
            $response = $environment->getSshConnection()->exec($command);
            $paprika->sshCommandDebugMessage($command, $response);

            $responseLines = explode("\n", trim($response));
            $releaseNumber = 0;

            foreach ($responseLines as $responseLine) {
                if (preg_match('/^\d{10}$/', trim($responseLine))) {
                    $release = trim($responseLine);
                    $releaseNumber ++;
                    if ($releaseNumber > $environment->getReleasesToRetain()) {
                        $command  = "rm -rf " . $environment->getDir() . "/releases/" . $release;
                        $response = $environment->getSshConnection()->exec($command);
                        $paprika->sshCommandDebugMessage($command, $response);
                    }
                }
            }

            $paprika->dispatch('task.deploy.removed_old_releases', $event);
            $paprika->message('Removed old releases.');

        }

        // Finish up.

        $paprika->dispatch('task.deploy.finished', $event);
        $paprika->message('Deployment complete!');
    }
}
