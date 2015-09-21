<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika\Console;

use \Paprika\Application as Paprika;
use \Paprika\Console\Command\SelfUpdateCommand;
use \Paprika\Console\Command\PapifyCommand;
use \Paprika\Console\Command\CreatePluginCommand;
use \Paprika\Console\Command\DeployCommand;
use \Paprika\Console\Command\RollbackCommand;
use \Paprika\Console\Command\StatusCommand;

/**
 * Paprika CLI application.
 *
 * This is a CLI wrapper to the main Paprika functionality.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class Application extends \Symfony\Component\Console\Application
{
    /**
     * Paprika instance.
     *
     * @var \Paprika\Application
     */
    protected $paprika;

    /**
     * Application constructor.
     *
     * @param \Paprika\Application $paprika Paprika instance
     */
    public function __construct(Paprika $paprika = null)
    {
        parent::__construct('Paprika: Spicy PHP Deployments', Paprika::VERSION);

        $this->paprika = $paprika;

        if (substr(__FILE__, 0, 5) === 'phar:') {
            $this->add(new SelfUpdateCommand());
        }

        $this->add(new PapifyCommand());
        $this->add(new CreatePluginCommand());

        if (!is_null($paprika)) {
            $this->add(new DeployCommand());
            $this->add(new RollbackCommand());
            $this->add(new StatusCommand());
        }
    }

    /**
     * Get Paprika instance.
     *
     * @return \Paprika\Application Paprika
     */
    public function getPaprika()
    {
        return $this->paprika;
    }
}