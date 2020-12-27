<?php

/**
 * App\Component\Jwt\Interfaces
 *
 * @author pierrefromager
 */

namespace App\Component\Jwt\Interfaces;

use App\Component\Jwt\Token;

interface IToken
{
    const _SECRET = 'secret';
    const _ALGO = 'algorithm';
    const _CONFIG_KEY = 'jwt';
    const _IAT = 'iat';
    const _JTI = 'jti';
    const _ISS = 'iss';
    const _NBF = 'nbf';
    const _EXP = 'exp';
    const _DATA = 'data';
    const _DATA_ID = 'id';
    const _DATA_LOGIN = 'login';
    const _DATA_PASSWORD_HASH = 'password_hash';
    const _DATA_IAT_S = 'iat_s';
    const _DATA_NBF_S = 'nbf_s';
    const _DATA_EXP_S = 'exp_s';
    const _RANDOM_BYTES_LEN = 32;
    const _ISSUE_AT_DELAY = 10; // secs
    const _TTL = 60; // secs

    /**
     * return encoded token for userid login and password
     *
     * @param string $uid
     * @param string $login
     * @param string $password
     * @return string
     */
    public function encode(int $uid, string $login, string $password): string;

    /**
     * return decoded token
     *
     * @param string $token
     * @return mixed
     */
    public function decode(string $token);

    /**
     * set token issue at time
     *
     * @param integer $dateTime
     * @return Token
     */
    public function setIssueAt(int $dateTime): Token;

    /**
     * setIssueAtDelay
     *
     * @param int $delay
     */
    public function setIssueAtDelay(int $delay): Token;

    /**
     * set token ttl
     *
     * @param int $ttl
     */
    public function setTtl(int $ttl): Token;
}
