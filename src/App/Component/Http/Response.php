<?php

namespace App\Component\Http;

use \App\Component\Http\Interfaces\IResponse;
use \App\Component\Http\Headers;

class Response implements IResponse
{

    /**
     * response content
     *
     * @var mixed
     */
    protected $content;

    /**
     * http status code
     *
     * @var Integer
     */
    protected $code;

    /**
     * header manager
     *
     * @var Headers
     */
    protected $headerManager;

    /**
     * is cli
     *
     * @var Boolean
     */
    protected $isCli;

    /**
     * instanciate
     */
    public function __construct()
    {
        $this->headerManager = new Headers();
        $this->code = self::HTTP_NOT_FOUND;
        $this->content = '';
        $sapiName = php_sapi_name();
        $this->setIsCli($sapiName == self::_CLI || $sapiName == self::_CLID);
    }

    /**
     * returns header manager
     *
     * @return Headers
     */
    public function getHeaderManager(): Headers
    {
        return $this->headerManager;
    }

    /**
     * set response content
     *
     * @param mixed $content
     * @return Response
     */
    public function setContent($content): Response
    {
        $this->content = (is_string($content))
            ? $content
            : json_encode($content);
        $this->headerManager->add(
            Headers::CONTENT_LENGTH,
            (string) strlen($this->content)
        );
        return $this;
    }

    /**
     * return content string
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * set http code response
     *
     * @param integer $code
     * @return Response
     */
    public function setCode(int $code): Response
    {
        $this->code = $code;
        return $this;
    }

    /**
     * return http code response
     *
     * @return integer
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * send response content to output
     *
     * @return Response
     */
    public function send(): Response
    {
        if ($this->isCli) {
            echo $this->content;
            return $this;
        }
        $this->headerManager->send();
        http_response_code($this->code);
        echo $this->content;
        return $this;
    }

    /**
     * set true if we are running from cli
     * essentially for testing purposes
     *
     * @param boolean $isCli
     * @return Response
     */
    protected function setIsCli(bool $isCli): Response
    {
        $this->isCli = $isCli;
        return $this;
    }
}
