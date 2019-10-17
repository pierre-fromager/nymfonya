<?php

namespace App\Http;

use App\Http\Interfaces\IRequest;
use App\Http\Interfaces\IHeaders;
use App\Http\Session;

class Request extends Session implements IRequest
{

    protected $server;
    protected $method;
    protected $contentType;
    protected $isCli;
    protected $params;

    /**
     * instanciate
     */
    public function __construct()
    {
        $this->server = $_SERVER;
        $this->method = $this->getMethod();
        $this->setContentType(self::APPLICATION_JSON);
        $this->setParams();
        parent::__construct();
        $sapiName = php_sapi_name();
        $this->setIsCli($sapiName == self::_CLI || $sapiName == self::_CLID);
    }

    /**
     * returns http method
     *
     * @return string
     */
    public function getMethod(): string
    {
        return ($this->isCli)
            ? self::METHOD_TRACE
            : $this->getServer(self::REQUEST_METHOD);
    }

    /**
     * returns http param for a given key
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * returns http param for a given key
     *
     * @return array
     */
    public function getParam(string $key): string
    {
        return isset($this->params[$key])
            ? $this->params[$key]
            : '';
    }

    /**
     * returns active route
     *
     * @return string
     */
    public function getRoute(): string
    {
        return $this->getServer(self::SCRIPT_URL);
    }

    /**
     * return php script filename
     *
     * @return string
     */
    public function getFilename(): string
    {
        return $this->getServer(self::SCRIPT_FILENAME);
    }

    /**
     * return request uri
     *
     * @return string
     */
    public function getUri(): string
    {
        return ($this->isCli())
            ? $this->getArgs()
            : $this->getServer(self::REQUEST_URI);
    }

    /**
     * return request host
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->getServer(self::HTTP_HOST);
    }

    /**
     * return request client ip
     *
     * @return string
     */
    public function getIp(): string
    {
        return $this->getServer(self::REMOTE_ADDR);
    }

    /**
     * return request content type
     *
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * return request accept encoding
     *
     * @return string
     */
    public function getAcceptEncoding(): string
    {
        $headers = $this->getHeaders();
        return isset($headers[IHeaders::ACCEPT_ENCODING])
            ? $headers[IHeaders::ACCEPT_ENCODING]
            : '';
    }

    /**
     * return request headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return ($this->isCli) ? [] : getallheaders();
    }

    /**
     * build uri from cli args
     *
     * @return void
     */
    protected function getArgs(): string
    {
        return isset($this->server[self::_ARGV][1])
            ? $this->server[self::_ARGV][1]
            : '';
    }

    /**
     * return server value for a given key
     *
     * @param string $key
     * @return string
     */
    protected function getServer(string $key): string
    {
        return (isset($this->server[$key]))
            ? $this->server[$key]
            : '';
    }

    /**
     * isJsonAppContentType
     *
     * @return bool
     */
    protected function isJsonContentType(): bool
    {
        return strpos(
            strtolower($this->contentType),
            self::APPLICATION_JSON
        ) !== false;
    }

    /**
     * getInput
     *
     * @return array
     */
    protected function getInput(): array
    {
        $input = [];
        $inputContent = file_get_contents('php://input');
        if ($this->isJsonContentType()) {
            $input = json_decode($inputContent, true);
            if (json_last_error() !== 0) {
                $input = [];
            }
        } else {
            parse_str($inputContent, $input);
        }
        return $input;
    }

    /**
     * set method
     * essentially for testing purposes
     *
     * @param string $method
     * @return Request
     */
    protected function setMethod(string $method): Request
    {
        $this->method = $method;
        return $this;
    }

    /**
     * set true if we are running from cli
     * essentially for testing purposes
     *
     * @param boolean $isCli
     * @return Request
     */
    protected function setIsCli(bool $isCli): Request
    {
        $this->isCli = $isCli;
        if (false === $this->isCli()) {
            $this->startSession($this->getFilename());
        }
        return $this;
    }

    /**
     * return true id sapi mode
     * essentially for testing purposes
     *
     * @return boolean
     */
    public function isCli(): bool
    {
        return $this->isCli;
    }

    /**
     * set content type
     *
     * @param string $contentType
     * @return Request
     */
    protected function setContentType(string $contentType = ''): Request
    {
        $this->contentType = (empty($contentType))
            ? $this->getServer(IHeaders::CONTENT_TYPE)
            : $contentType;
        return $this;
    }

    /**
     * get params in cli mode
     *
     * @return array
     */
    protected function getCliParams(): array
    {
        $params = $this->getInput();
        if ($this->isCli) {
            $queryString = parse_url($this->getArgs(), PHP_URL_QUERY);
            parse_str($queryString, $queryParams);
            $params = array_merge($params, $queryParams);
        }
        return $params;
    }

    /**
     * set an entry in params for key value
     *
     * @param string $key
     * @param string $value
     * @return Request
     */
    protected function setParam(string $key, string $value): Request
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * set http params
     *
     * @return array
     */
    protected function setParams(): Request
    {
        if ($this->method === self::METHOD_GET) {
            $this->params = $_GET;
        } elseif ($this->method === self::METHOD_POST) {
            $this->params = ($this->isJsonContentType())
                ? $this->getInput()
                : $_POST;
        } elseif ($this->method === self::METHOD_TRACE) {
            $this->params = $this->getCliParams();
        } else {
            $this->params =  $this->getInput();
        }
        return $this;
    }
}
