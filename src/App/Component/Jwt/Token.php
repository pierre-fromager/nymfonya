<?php

/**
 * Description of App\Component\Jwt\Token
 *
 * @author pierrefromager
 */

namespace App\Component\Jwt;

use Firebase\JWT\JWT as Fjwt;
use Nymfonya\Component\Config;
use Nymfonya\Component\Http\Request;

class Token implements Interfaces\IToken
{
    /**
     * token time value to be issue
     *
     * @var int
     */
    private $issueAt;

    /**
     * token time to live
     *
     * @var int
     */
    private $ttl;

    /**
     * token delay before issue
     *
     * @var int
     */
    private $issueAtDelay;

    /**
     * config
     *
     * @var Config
     */
    private $config;

    /**
     * request
     *
     * @var Request
     */
    private $request;

    /**
     * token
     *
     * @var String
     */
    private $token;

    /**
     * instanciate
     *
     * @param Config $config
     * @param Request $request
     */
    public function __construct(Config $config, Request $request)
    {
        $this->config = $config;
        $this->request = $request;
        $this->token = '';
        $this->setIssueAt(0);
        $this->setIssueAtDelay(0);
        $this->setTtl(0);
    }

    /**
     * set token string
     *
     * @param string $token
     * @return Token
     */
    protected function setToken(string $token): Token
    {
        $this->token = $token;
        return $this;
    }

    /**
     * get token string
     *
     * @return string
     */
    protected function getToken(): string
    {
        return $this->token;
    }

    /**
     * encode
     *
     * @param string $uid
     * @param string $login
     * @param string $password
     * @return string
     */
    public function encode(int $uid, string $login, string $password): string
    {
        $this->setToken(Fjwt::encode(
            $this->getToEncodePayload($uid, $login, $password),
            $this->getConfigSecret(),
            $this->getConfigAlgo()
        ));
        return $this->token;
    }

    /**
     * return to encode payload datas
     *
     * @param integer $uid
     * @param string $login
     * @param string $password
     * @return array
     */
    protected function getToEncodePayload(
        int $uid,
        string $login,
        string $password
    ): array {
        $tokenId = base64_encode(
            openssl_random_pseudo_bytes(self::_RANDOM_BYTES_LEN)
        );
        $issuedAt = time() - 100;
        $notBefore = $issuedAt + $this->issueAtDelay; //Adding 10 seconds
        $expire = $notBefore + $this->ttl; // Adding 60 seconds
        $serverName = $this->request->getHost();
        return [
            self::_IAT => $issuedAt, // Issued at: time when the token was generated
            self::_JTI => $tokenId, // Json Token Id: an unique identifier for the token
            self::_ISS => $serverName, // Issuer
            self::_NBF => $notBefore, // Not before
            self::_EXP => $expire, // Expire
            self::_DATA => [// Data related to the signer user
                self::_DATA_ID => $uid, // userid from the users table
                self::_DATA_LOGIN => $login, // User name
                self::_DATA_PASSWORD_HASH => password_hash($password, PASSWORD_DEFAULT),
                self::_DATA_IAT_S => strftime('%c', $issuedAt),
                self::_DATA_NBF_S => strftime('%c', $notBefore),
                self::_DATA_EXP_S => strftime('%c', $expire), // Expire
            ]
        ];
    }

    /**
     * decode
     *
     * @param string $token
     * @return mixed
     */
    public function decode(string $token)
    {
        return Fjwt::decode(
            $token,
            $this->getConfigSecret(),
            [$this->getConfigAlgo()]
        );
    }

    /**
     * return secret from jwt config
     *
     * @return string
     */
    protected function getConfigSecret(): string
    {
        return $this->getConfig()[self::_SECRET];
    }

    /**
     * return algo from jwt config
     *
     * @return string
     */
    protected function getConfigAlgo(): string
    {
        return $this->getConfig()[self::_ALGO];
    }

    /**
     * set token issue at time
     *
     * @param integer $dateTime
     * @return Token
     */
    public function setIssueAt(int $dateTime): Token
    {
        $this->issueAt = ($dateTime > 0) ? $dateTime : time();
        return $this;
    }

    /**
     * setIssueAtDelay
     *
     * @param int $delay
     */
    public function setIssueAtDelay(int $delay): Token
    {
        $this->issueAtDelay = ($delay > 0) ? $delay : self::_ISSUE_AT_DELAY;
        return $this;
    }

    /**
     * set token ttl
     *
     * @param int $ttl
     */
    public function setTtl(int $ttl): Token
    {
        $this->ttl = ($ttl > 0) ? $ttl : self::_TTL;
        return $this;
    }

    /**
     * get token config
     *
     * @return array
     */
    protected function getConfig(): array
    {
        return $this->config->getSettings(
            self::_CONFIG_KEY
        );
    }
}
