<?php

/**
 * App\Tools\Jwt\Interfaces
 *
 * @author pierrefromager
 */

namespace App\Tools\Jwt\Interfaces;

use App\Tools\Jwt\Token;

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

    public function encode(int $uid, string $login, string $password);

    public function decode(string $token = '');

    public function setIssueAt(int $dateTime): Token;

    public function setIssueAtDelay(int $delay): Token;

    public function setTtl(int $ttl): Token;
}
