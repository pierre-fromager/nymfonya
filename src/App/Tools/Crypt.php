<?php

namespace App\Tools;

use App\Config;

/**
 * Lib_Auth_Crypt
 *
 * @author Pierre Fromager <pf@pier_infor.fr>
 * @version 1.0
 *
 * This is a static lib to encrypt/decrypt string from a given crypto algorithm.
 * Using openssl we check if the chosen algo is available.
 * We do not use mcrypt because deprecated and unsecure.
 */

class Crypt
{

    const BIT_8 = '8bit';
    const ERR_MSG_UNSUPPORTED_METHOD = 'Unsupported openssl method ';
    const ERR_MSG_ENCRYPTION_FAIL = 'Encryption failure';
    const DEFAULT_ALGO = 'aes-256-ctr';

    private $method = self::DEFAULT_ALGO;
    private $key = '';

    /**
     * instanciate
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $configJwt = $config->getSettings('jwt');
        $this->key = $configJwt['secret'];
    }

    /**
     * setAlgo
     *
     * @param string $algo
     * @return Crypt
     * @throws Exception
     */
    public function setAlgo(string $algo): Crypt
    {
        if (!in_array($algo, openssl_get_cipher_methods())) {
            throw new \Exception(self::ERR_MSG_UNSUPPORTED_METHOD . $algo);
        }
        $this->method = $algo;
        return $this;
    }

    /**
     * setB64Key
     *
     * @param string $key
     * @return Crypt
     * @throws Exception
     */
    public function setB64Key(string $key): Crypt
    {
        $this->key = base64_decode($key);
        return $this;
    }

    /**
     * Encrypts (but does not authenticate) a message
     *
     * @param string $message - plaintext message
     * @param boolean $encode - set to TRUE to return a base64-encoded
     * @return string (raw binary)
     */
    public function encrypt(string $message, bool $encode = true): string
    {
        $nonceSize = openssl_cipher_iv_length($this->method);
        $nonce = openssl_random_pseudo_bytes($nonceSize);
        $ciphertext = openssl_encrypt(
            $message,
            $this->method,
            $this->key,
            OPENSSL_RAW_DATA,
            $nonce
        );
        if ($encode) {
            return base64_encode($nonce . $ciphertext);
        }
        return $nonce . $ciphertext;
    }

    /**
     * Decrypts (but does not verify) a message
     *
     * @param string $message - ciphertext message
     * @param boolean $encoded - are we expecting an encoded string?
     * @return string
     */
    public function decrypt(string $message, bool $encoded = true): string
    {
        if ($encoded) {
            $message = base64_decode($message, true);
            if ($message === false) {
                throw new \Exception(self::ERR_MSG_ENCRYPTION_FAIL);
            }
        }
        $nonceSize = openssl_cipher_iv_length($this->method);
        $nonce = mb_substr($message, 0, $nonceSize, self::BIT_8);
        $ciphertext = mb_substr($message, $nonceSize, null, self::BIT_8);
        $plaintext = openssl_decrypt(
            $ciphertext,
            $this->method,
            $this->key,
            OPENSSL_RAW_DATA,
            $nonce
        );
        return $plaintext;
    }

    /**
     * getVersionNumber
     *
     * @return int
     */
    public function getVersionNumber(): int
    {
        return OPENSSL_VERSION_NUMBER;
    }

    /**
     * getVersionText
     *
     * @return string
     */
    public function getVersionText(): string
    {
        return OPENSSL_VERSION_TEXT;
    }
}
