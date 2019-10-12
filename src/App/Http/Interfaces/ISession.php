<?php

namespace App\Http\Interfaces;

use App\Http\Session;

interface ISession
{

    /**
     * start a session with a session name
     *
     * @param string $sessionName
     * @return Session
     */
    public function startSession(string $sessionName): Session;

    /**
     * set name value in session for a given key
     *
     * @param string $name
     * @param mixed $value
     * @param string $key
     */
    public function setSession(string $name, $value, $key = ''): Session;

    /**
     * remove a session entry name for a given key
     *
     * @param string $name
     * @param string $key
     */
    public function deleteSession(string $name, string $key = ''): Session;

    /**
     * return true if a session name/key entry exists
     *
     * @param string $name
     * @param string $key
     * @return boolean
     */
    public function hasSession(string $name, string $key = ''): bool;

    /**
     * return entry name/key session value
     *
     * @param string $name
     * @param string $key
     * @return mixed
     */
    public function getSession(string $name, string $key = '');
}
