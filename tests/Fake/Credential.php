<?php

namespace Tests\Fake;

/**
 * Fake Credential returns fake login and password
 */
trait Credential
{

    #master
    protected $__fakeLoginOk = 'admin@domain.tld';
    protected $__fakePasswordOk = 'adminadmin';
    protected $__fakeLoginKo = 'bad@pier-infor.fr';
    protected $__fakePasswordKo = 'badpass';

    /**
     * returns fake success login
     */
    protected function loginOk()
    {
        return $this->__fakeLoginOk;
    }

    /**
     * returns fake success password
     */
    protected function passwordOk()
    {
        return $this->__fakePasswordOk;
    }

    /**
     * returns fake bad login
     */
    protected function loginKo()
    {
        return $this->__fakeLoginKo;
    }

    /**
     * returns fake bad password
     */
    protected function passwordKo()
    {
        return $this->__fakePasswordKo;
    }
}
