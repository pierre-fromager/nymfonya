<?php

namespace App\Http;

use \App\Http\Interfaces\IResponse;
use \App\Http\Headers;

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
     * headers list
     *
     * @var Headers
     */
    protected $headerManager;

    /**
     * instanciate
     */
    public function __construct()
    {
        $this->headerManager = new Headers();
        $this->headers = [];
        $this->isCli = php_sapi_name() == 'cli';
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
}
