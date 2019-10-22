<?php

namespace App\Http;

use App\Http\Interfaces\ICookie;

class Cookie implements ICookie
{

    protected $cookie;

    /**
     * instanciate
     */
    public function __construct()
    {
        $this->refreshCookie();
    }

    /**
     * get cookie value from cookie name
     *
     * @param string $name
     * @return string
     */
    public function getCookie(string $name): string
    {
        return (isset($this->cookie[$name])) ? $this->cookie[$name] : '';
    }

    /**
     * set cookie value for cookie name and ttl
     *
     * @param string $name
     * @param string $value
     * @param integer $ttl
     * @return Cookie
     */
    public function setCookie(string $name, string $value, int $ttl): Cookie
    {
        setcookie($name, $value, time() + $ttl);
        return $this->refreshCookie();
    }

    /**
     * refresh cookie from global
     *
     * @return Cookie
     */
    protected function refreshCookie(): Cookie
    {
        $this->cookie = $_COOKIE;
        return $this;
    }
}
