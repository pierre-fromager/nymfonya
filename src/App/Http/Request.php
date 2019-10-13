<?php

namespace App\Http;

use App\Http\Interfaces\IRequest;
use App\Http\Interfaces\IHeaders;
use App\Http\Session;

class Request extends Session implements IRequest
{

    protected $server;
    protected $method;
    protected $isCli;

    /**
     * instanciate
     */
    public function __construct()
    {
        $this->server = $_SERVER;
        $this->method = $this->getMethod();
        parent::__construct();
        $this->setIsCli(php_sapi_name() == self::_CLI);
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
     * returns http params
     *
     * @return array
     */
    public function getParams(): array
    {
        $params = [];
        switch ($this->getMethod()) {
            case self::METHOD_GET:
                $params = $_GET;
                break;
            case self::METHOD_POST:
                $params = ($this->isJsonContentType())
                    ? $this->getInput()
                    : $_POST;
                break;
            case self::METHOD_PUT:
            case self::METHOD_PATCH:
            case self::METHOD_DELETE:
            case self::METHOD_HEAD:
            case self::METHOD_OPTIONS:
                $params = $this->getInput();
                break;
            case self::METHOD_TRACE:
                $params = $this->getInput();
                if ($this->isCli) {
                    $qs = parse_url($this->getArgs(), PHP_URL_QUERY);
                    parse_str($qs, $qp);
                    $params = array_merge($params, $qp);
                }
                break;
        }
        return $params;
    }

    /**
     * returns http param for a given key
     *
     * @return array
     */
    public function getParam(string $key): string
    {
        return isset($this->getParams()[$key])
            ? $this->getParams()[$key]
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
        if (false === $this->isCli()) {
            return $this->getServer(self::REQUEST_URI);
        }
        return $this->getArgs();
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
        $ct = $this->getServer(IHeaders::CONTENT_TYPE);
        return (empty($ct))
            ? self::APPLICATION_JSON
            : $ct;
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
        $lct = strtolower($this->getContentType());
        return strpos($lct, self::APPLICATION_JSON) !== false;
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
        if (!$this->isCli()) {
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
    protected function isCli(): bool
    {
        return $this->isCli;
    }
}
