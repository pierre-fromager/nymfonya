<?php

namespace App\Component\Http\Interfaces;

use App\Component\Http\Cookie;

interface ICookie
{

    /**
     * get cookie string
     *
     * @param string $name
     * @return string
     */
    public function getCookie(string $name): string;

    /**
     * set cookie value for cookie name and ttl
     *
     * @param string $name
     * @param string $value
     * @param integer $ttl
     * @return Cookie
     */
    public function setCookie(string $name, string $value, int $ttl): Cookie;
}
