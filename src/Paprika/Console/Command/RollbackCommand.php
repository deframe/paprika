<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Paprika\MessageHandler\MessageHandler;
use Paprika\MessageHandler\LogMessageHandler;
use Paprika\Task\RollbackTask;

/**
 * CLI application rollback command.
 *
 * This collects the appropriate arguments from the console and passes them
 * through to Paprika's Rollback task.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class RollbackCommand extends Command
{
    /**
     * Configure the command.
     *
     * @return \Paprika\Console\Command\RollbackCommand Fluent interface
     */
    protected function configure()
    {
        $this->setName('rollback');
        $this->setDescription('Rolls back a project to it\'s previous release');

        $this->addArgument(
            'environment',
            InputArgument::REQUIRED,
            'The environment on which the project is deployed'
        );

        $this->addOption(
            'logfile',
            null,
            InputOption::VALUE_REQUIRED,
            'Where should the output of this task be logged to?'
        );

        return $this;
    }

    /**
     * Execute the command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input Console input
     * @param \Symfony\Component\Console\Output\OutputInterface $output Console output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $paprika          = $this->getApplication()->getPaprika();
        $environmentLabel = $input->getArgument('environment');

        if (!$paprika->hasEnvironment($environmentLabel)) {
            $output->writeln("Error: Environment '" . $environmentLabel . "' does not exist!");
            return;
        }

        // Add log message handler if appropriate.

        $logfile = $input->getOption('logfile');

        if (!is_null($logfile)) {
            $paprika->addMessageHandler(
                new LogMessageHandler(getcwd() . DIRECTORY_SEPARATOR . $logfile),
                MessageHandler::TYPE_INFO | MessageHandler::TYPE_DEBUG
            );
        }

        // Create and run the rollback task.

        $task = new RollbackTask($paprika, $environmentLabel);
        $task->run();
    }
}