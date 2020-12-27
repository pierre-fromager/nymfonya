<?php

declare(strict_types=1);

namespace App\Component;

use Nymfonya\Component\Container;

class Pki
{

    /**
     * container
     *
     * @var Container
     */
    private $container;

    /**
     * instanciate
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * generate an return array of private and public key
     *
     * @return array
     */
    public function generateKeyPair(): array
    {
        $res = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        openssl_pkey_export($res, $privKey);
        return [$privKey, openssl_pkey_get_details($res)['key']];
    }

    /**
     * encrypt a message with private key
     *
     * @param string $message
     * @param string $privKey
     * @return string
     */
    public function encrypt(string $message, string $privKey): string
    {
        openssl_private_encrypt($message, $crypted, $privKey);
        return base64_encode($crypted);
    }


    /**
     * decrypt a message with public key
     *
     * @param string $message
     * @param string $pubKey
     * @return string
     */
    public function decrypt(string $message, string $pubKey): string
    {
        openssl_public_decrypt(base64_decode($message), $decrypted, $pubKey);
        return $decrypted;
    }

    /**
     * return true if message match crypted with a public key
     *
     * @param string $message
     * @param string $crypted
     * @param string $pubKey
     * @return boolean
     */
    public function validate(string $message, string $crypted, string $pubKey): bool
    {
        return $message == $this->decrypt($crypted, $pubKey);
    }
}
