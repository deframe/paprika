<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Paprika\MessageHandler\MessageHandler;
use Paprika\Plugin\Plugin;
use Paprika\Event\Event;

/**
 * Paprika application.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class Application
{
    /**
     * Paprika version.
     */
    const VERSION = '1.0.0';

    /**
     * Paprika distribution URL.
     */
    const DIST_URL = 'http://[domain]/paprika';

    /**
     * Name of the application.
     *
     * @var string
     */
    protected $name;

    /**
     * Git repository associated with the application.
     *
     * @var \Paprika\GitRepository
     */
    protected $gitRepository;

    /**
     * Shared file symlinks.
     *
     * @var array
     */
    protected $sharedFileSymlinks = array();

    /**
     * Plugins.
     *
     * @var array
     */
    protected $plugins = array();

    /**
     * Environments.
     *
     * @var array
     */
    protected $environments = array();

    /**
     * Application event dispatcher.
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * Message handlers.
     *
     * @var array
     */
    protected $messageHandlers = array();

    /**
     * Paprika application constructor.
     *
     * @param string $name Name of the application
     * @param \Paprika\GitRepository Git repository associated with the application
     */
    public function __construct($name, \Paprika\GitRepository $gitRepository)
    {
        $this->name            = $name;
        $this->gitRepository   = $gitRepository;

        $this->eventDispatcher = new EventDispatcher();
    }

    /**
     * Get the name of the application.
     *
     * @return string Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the Git repository associated with the application.
     *
     * @return \Paprika\GitRepository
     */
    public function getGitRepository()
    {
        return $this->gitRepository;
    }

    /**
     * Add a shared file symlink.
     *
     * @param string $source Symlink source
     * @param string $target Symlink target
     * @return \Paprika\Application Fluent interface
     */
    public function addSharedFileSymlink($source, $target)
    {
        $this->sharedFileSymlinks[$source] = $target;

        return $this;
    }

    /**
     * Get shared file symlinks.
     *
     * @return array Symlinks
     */
    public function getSharedFileSymlinks()
    {
        return $this->sharedFileSymlinks;
    }

    /**
     * Register a plugin.
     *
     * @param \Paprika\Plugin\Plugin $plugin Plugin
     * @return \Paprika\Application Fluent interface
     */
    public function registerPlugin(Plugin $plugin)
    {
        $this->plugins[] = $plugin;

        $plugin->init($this);

        return $this;
    }

    /**
     * Add a deployment environment.
     *
     * @param \Paprika\Environment $environment Environment
     */
    public function addEnvironment(Environment $environment)
    {
        $this->environments[] = $environment;
    }

    /**
     * Get the deployment environment with a given label.
     *
     * @param string $label Environment label
     * @return \Paprika\Environment Matching environment
     */
    public function getEnvironment($label)
    {
        foreach ($this->environments as $environment) {
            if ($environment->getLabel() == $label) {
                $returnEnvironment = $environment;
                break;
            }
        }

        return isset($returnEnvironment) ? $returnEnvironment : null;
    }

    /**
     * Determine if a environment with a given label exists.
     *
     * @param string $label Environment label
     * @return boolean Whether the environment exists or not
     */
    public function hasEnvironment($label)
    {
        return $this->getEnvironment($label) !== null;
    }

    /**
     * Determine if the application has one or more environments.
     *
     * @return boolean Whether one or more environments exist
     */
    public function hasEnvironments()
    {
        return !empty($this->environments);
    }

    /**
     * Get the event dispatcher.
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcher Event dispatcher
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * Dispatch a Paprika event.
     *
     * This is a convenient wrapper to the event dispatcher instance!
     *
     * @param string $eventName Name of the event
     * @param \Paprika\Event\Event $event Event to dispatch
     */
    public function dispatch($eventName, Event $event = null)
    {
        $this->eventDispatcher->dispatch($eventName, $event);
    }

    /**
     * Add message handler.
     *
     * @param \Paprika\MessageHandler\MessageHandler $messageHandler Message handler
     * @param integer $messageType Type of messages that should be passed to the handler
     * @return \Paprika\Application Fluent interface
     */
    public function addMessageHandler(MessageHandler $messageHandler, $messageType)
    {
        $this->messageHandlers[] = array(
            'type'    => $messageType,
            'handler' => $messageHandler
        );
    }

    /**
     * Accept a message.
     *
     * @param string $message Message to accept
     * @param integer $messageType Type of message
     * @return void
     */
    public function message($message, $messageType = null)
    {
        foreach ($this->messageHandlers as $messageHandler) {
            if (is_null($messageType)
                || ($messageHandler['type'] & $messageType) == $messageType)
            {
                $messageHandler['handler']->handle($message);
            }
        }
    }

    /**
     * Accept an SSH command debug message.
     *
     * @param string $command Command that was executed
     * @param string $response Command response
     * @return void
     */
    public function sshCommandDebugMessage($command, $response)
    {
        $response = str_replace("\n", " ", trim($response));

        $message = '[SSH COMMAND] ' . trim($command)
            . ' [SSH RESPONSE] ' . ($response ?: 'N/a');

        $this->message($message, MessageHandler::TYPE_DEBUG);
    }
}