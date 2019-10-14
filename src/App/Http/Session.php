<?php

namespace App\Http;

use App\Http\Cookie;
use App\Http\Interfaces\ISession;

class Session extends Cookie implements ISession
{

    protected $session;

    /**
     * instanciate
     */
    public function __construct()
    {
        $this->session = &$_SESSION;
        parent::__construct();
    }

    /**
     * start a session with a session name
     *
     * @param string $sessionName
     * @return Session
     */
    public function startSession(string $sessionName): Session
    {
        $isActive = session_status() === PHP_SESSION_ACTIVE ? true : false;
        if (!$isActive) {
            session_name(sha1($sessionName));
            session_start();
        }
        return $this;
    }

    /**
     * set
     *
     * @param string $name
     * @param mixed $value
     * @param string $key
     */
    public function setSession(string $name, $value, $key = ''): Session
    {
        if ($key) {
            if (!$this->hasSession($name, $key) || !is_array($this->session[$name])) {
                $this->session[$name] = [];
            }
            $this->session[$name][$key] = $value;
        } else {
            $this->session[$name] = $value;
        }
        return $this;
    }

    /**
     * deleteSession
     *
     * @param string $name
     * @param string $key
     */
    public function deleteSession(string $name, string $key = ''): Session
    {
        if ($key) {
            unset($this->session[$name][$key]);
        } else {
            unset($this->session[$name]);
        }
        return $this;
    }

    /**
     * hasSession
     *
     * @param string $name
     * @param string $key
     * @return boolean
     */
    public function hasSession(string $name, string $key = ''): bool
    {
        if (!$key) {
            return (isset($this->session[$name])
                && !empty($this->session[$name]));
        }
        return (isset($this->session[$name][$key])
            && !empty($this->session[$name][$key]));
    }

    /**
     * getSession
     *
     * @param string $name
     * @param string $key
     * @return mixed
     */
    public function getSession(string $name, string $key = '')
    {
        if (!$key) {
            return $this->hasSession($name)
                ? $this->session[$name]
                : '';
        }
        return
            $this->hasSession($name, $key)
            ? $this->session[$name][$key]
            : '';
    }
}
