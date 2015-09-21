<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Paprika\Application as Paprika;

/**
 * CLI application self-update command.
 *
 * This will update the current Paprika Phar file to the latest version of the
 * application.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class SelfUpdateCommand extends Command
{
    /**
     * Configure the command.
     *
     * @return \Paprika\Console\Command\CreatePluginCommand Fluent interface
     */
    protected function configure()
    {
        $this->setName('self-update');
        $this->setDescription('Update Paprika to the latest version');

        return $this;
    }

    /**
     * Execute the command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input Console input
     * @param \Symfony\Component\Console\Output\OutputInterface $output Console output
     * @return void
     * @throws \Exception if an error occurs
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $localFilename = realpath($_SERVER['argv'][0]) ?: $_SERVER['argv'][0];
        $tmpDir = sys_get_temp_dir();

        if (!is_writable($localFilename)) {
            $output->writeln('Error: The "' . $localFilename . '" file could not be written to');
            return;
        }

        if (!is_writable(dirname($tmpDir))) {
            $output->writeln('Error: The temporary directory (' . $tmpDir . ') is not writable');
            return;
        }

        $latestVersion = trim(file_get_contents(Paprika::DIST_URL . '/version'));

        if (Paprika::VERSION !== $latestVersion) {

            $output->writeln('Updating Paprika to version ' . $latestVersion . '...');

            $remoteFilename = Paprika::DIST_URL . '/paprika.phar';
            $tempLocalFilename = $tmpDir . '/' . basename($localFilename, '.phar') . '-temp.phar';

            $latestPhar = file_get_contents($remoteFilename);
            file_put_contents($tempLocalFilename, $latestPhar);

            if (!file_exists($tempLocalFilename)) {
                $output->writeln('Error: The download of the new Paprika version failed for an unexpected reason');
                return;
            }

            try {
                @chmod($tempLocalFilename, 0777 & ~umask());
                $phar = new \Phar($tempLocalFilename);
                unset($phar);
                rename($tempLocalFilename, $localFilename);
                $output->writeln('Done, you are now using the latest version of Paprika!');
            } catch (\Exception $e) {
                @unlink($tempLocalFilename);
                if (!$e instanceof \UnexpectedValueException && !$e instanceof \PharException) {
                    throw $e;
                }
                $output->writeln('Error: The download is corrupted (' . $e->getMessage() . '). Please re-run the self-update command to try again.');
            }

        } else {
            $output->writeln('You are using the latest version of Paprika! (' . $latestVersion . ')');
        }
    }
}