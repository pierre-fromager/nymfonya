<?php

declare(strict_types=1);

namespace App\Component;

use Nymfonya\Component\Config;

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

    /**
     * method is crypt algo
     *
     * @var String
     */
    private $method = self::DEFAULT_ALGO;

    /**
     * crypt key
     *
     * @var String
     */
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
     * encrypt content
     *
     * @param mixed $content
     * @param boolean $encode
     * @return mixed
     */
    public function encrypt($content, bool $encode = true)
    {
        $nonceSize = openssl_cipher_iv_length($this->method);
        $nonce = openssl_random_pseudo_bytes($nonceSize);
        $cryptedContent = openssl_encrypt(
            $content,
            $this->method,
            $this->key,
            OPENSSL_RAW_DATA,
            $nonce
        );
        if ($encode) {
            return base64_encode($nonce . $cryptedContent);
        }
        return $nonce . $cryptedContent;
    }

    /**
     * decrypt content
     *
     * @param mixed $content
     * @param boolean $encoded
     * @return mixed
     */
    public function decrypt($content, bool $encoded = true)
    {
        if ($encoded) {
            $content = @base64_decode($content, true);
            if ($content === false) {
                throw new \Exception(self::ERR_MSG_ENCRYPTION_FAIL);
            }
        }
        $nonceSize = openssl_cipher_iv_length($this->method);
        $nonce = mb_substr($content, 0, $nonceSize, self::BIT_8);
        $cryptedContent = mb_substr(
            $content,
            $nonceSize,
            null,
            self::BIT_8
        );
        $decrypted = openssl_decrypt(
            $cryptedContent,
            $this->method,
            $this->key,
            OPENSSL_RAW_DATA,
            $nonce
        );
        return $decrypted;
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
