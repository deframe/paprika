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
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CLI application create-plugin command.
 *
 * This will create a boilerplate Paprika plugin that can be customized by the
 * user.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class CreatePluginCommand extends Command
{
    /**
     * Configure the command.
     *
     * @return \Paprika\Console\Command\CreatePluginCommand Fluent interface
     */
    protected function configure()
    {
        $this->setName('create-plugin');
        $this->setDescription('Create a boilerplate Paprika plugin');

        $this->addArgument(
            'name',
            InputArgument::REQUIRED,
            'The name of the plugin (specified as CamelCaps)'
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
        $pluginName = $input->getArgument('name');

        if (file_exists($pluginName . '.php')) {
            $output->writeln('Error: ' . $pluginName . '.php already exists!');
            return;
        }

        $paprikaDir = dirname(__FILE__)
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . '..';

        $pluginTemplate = $paprikaDir
            . DIRECTORY_SEPARATOR . 'data'
            . DIRECTORY_SEPARATOR . 'Plugin-template';

        $templateContent = file_get_contents($pluginTemplate);
        $templateContent = str_replace('{{{ pluginName }}}', $pluginName, $templateContent);
        file_put_contents($pluginName . '.php', $templateContent);

        $output->writeln('A boilerplate plugin called has been created in ' . $pluginName . '.php. Remember to add it to your Papfile!');
    }
}