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

/**
 * CLI application papify command.
 *
 * This will create a boilerplate Papfile template that can be configured by the
 * user.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class PapifyCommand extends Command
{
    /**
     * Configure the command.
     *
     * @return \Paprika\Console\Command\PapifyCommand Fluent interface
     */
    protected function configure()
    {
        $this->setName('papify');
        $this->setDescription('Create a boilerplate Papfile');

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
        if (file_exists('Papfile')) {
            $output->writeln('Error: Papfile already exists!');
            return;
        }

        $paprikaDir = dirname(__FILE__)
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . '..';

        $papfileTemplate = $paprikaDir
            . DIRECTORY_SEPARATOR . 'data'
            . DIRECTORY_SEPARATOR . 'Papfile-template';

        copy($papfileTemplate, 'Papfile');

        $output->writeln('A boilerplate Papfile has been created in the current directory.');
    }
}