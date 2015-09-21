<?php

/*
 * This file is part of the Paprika package.
 *
 * (c) David Frame <deframe@cryst.co.uk>
 *
 */

namespace Paprika;

/**
 * Git repository.
 *
 * This class contains the various information required to work with a Git
 * repository.
 *
 * @author    David Frame <deframe@cryst.co.uk>
 * @copyright Copyright (c) David Frame <deframe@cryst.co.uk>
 */
class GitRepository
{
    /**
     * Repository location.
     *
     * @var string
     */
    protected $location;

    /**
     * Username for accessing the repository.
     *
     * @var string
     */
    protected $username = '';

    /**
     * Password for accessing the repository.
     *
     * @var string
     */
    protected $password = '';

    /**
     * Create a git repository instance.
     *
     * @param string $location Repository location
     */
    public function __construct($location)
    {
        $this->location = $location;
    }

    /**
     * Get the repository location.
     *
     * @return string Location
     */
    public function getLocation()
    {
        if(!empty($this->username) && !empty($this->password)) {
            $urlInfo = parse_url($this->location);
            $urlInfo['user'] = $this->username;
            $urlInfo['pass'] = $this->password;
            $location = http_build_url($urlInfo);
        } else {
            $location = $this->location;
        }
        return $location;
    }

    /**
     * Set the username required to access the repository.
     *
     * @param string $username Repository username
     * @return \Paprika\GitRepository Fluent interface
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the username required to access the repository.
     *
     * @return string Username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the password required to access the repository.
     *
     * @param string $password Repository password
     * @return \Paprika\GitRepository Fluent interface
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the password required to access the repository.
     *
     * @return string Password
     */
    public function getPassword()
    {
        return $this->password;
    }
}
