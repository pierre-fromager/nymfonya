<?php

namespace App\Controllers;

use App\Interfaces\Controllers\IApi;
use App\Reuse\Controllers\AbstractApi;
use App\Container;
use App\Http\Response;

final class Config extends AbstractApi implements IApi
{

    const KEY_LENGTH = 64;
    const _TITLE = 'title';
    const _ACTION = 'action';
    const _DATAS = 'datas';
    const _HOWTO = 'howto';

    /**
     * instanciate
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    /**
     * help action
     *
     * @Role anonymous
     * @return Config
     */
    final public function help(): Config
    {
        $baseUri = $this->baseRootUri();
        $helpAction = [
            [
                self::_TITLE => 'App key generate',
                self::_ACTION => $baseUri . 'keygen',
                self::_HOWTO => 'Copy paste result in '
                    . 'config/$env jwt/secret'
            ]
        ];
        $this->response
            ->setCode(Response::HTTP_OK)
            ->setContent($helpAction);
        return $this;
    }

    /**
     * keygen action
     *
     * @Role anonymous
     * @return Config
     */
    final public function keygen(): Config
    {
        $this->response
            ->setCode(Response::HTTP_OK)
            ->setContent(
                $this->getActionItem(
                    'App key generate',
                    __FUNCTION__,
                    base64_encode(
                        openssl_random_pseudo_bytes(self::KEY_LENGTH)
                    )
                )
            );
        return $this;
    }

    /**
     * account action
     *
     * @Role anonymous
     * @return Config
     */
    final public function account(): Config
    {
        $line = 'Undefined function readline';
        if ($this->hasReadLine()) {
            $line = readline("Command: ");
        }
        $this->response
            ->setCode(Response::HTTP_OK)
            ->setContent(['error' => false, 'command' => $line]);
        return $this;
    }

    /**
     * return current base root uri
     *
     * @return string
     */
    protected function baseRootUri(): string
    {
        return dirname($this->request->getUri()) . '/';
    }

    /**
     * return array
     *
     * @param string $title
     * @param string $action
     * @param string $datas
     * @return array
     */
    protected function getActionItem(string $title, string $action, string $datas): array
    {
        return [
            [
                self::_TITLE => $title,
                self::_ACTION => $this->baseRootUri() . $action,
                self::_DATAS => $datas
            ]
        ];
    }

    /**
     * return true if php was configured with --readline option
     *
     * @return boolean
     */
    protected function hasReadLine(): bool
    {
        return function_exists("readline");
    }
}
