<?php

namespace App\Component\Http\Interfaces;

interface IRequest
{

    const _CLI = 'cli';
    const _CLID = 'phpdbg';
    const _ARGV = 'argv';
    const METHOD_GET = 'GET';
    const METHOD_HEAD = 'HEAD';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_CONNECT = 'CONNECT';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_TRACE = 'TRACE';
    const METHOD_PATCH = 'PATCH';
    const REQUEST_METHOD = 'REQUEST_METHOD';
    const SCRIPT_URL = 'SCRIPT_URL';
    const SCRIPT_FILENAME = 'SCRIPT_FILENAME';
    const REQUEST_URI = 'REQUEST_URI';
    const HTTP_HOST = 'HTTP_HOST';
    const CONTENT_TYPE = 'CONTENT_TYPE';
    const REMOTE_ADDR = 'REMOTE_ADDR';
    const APPLICATION_JSON = 'application/json';

    /**
     * instanciate
     */
    public function __construct();

    /**
     * returns http method
     *
     * @return string
     */
    public function getMethod(): string;


    /**
     * returns http params
     *
     * @return array
     */
    public function getParams(): array;


    /**
     * returns http param for a given key
     *
     * @return array
     */
    public function getParam(string $key): string;


    /**
     * returns active route
     *
     * @return string
     */
    public function getRoute(): string;


    /**
     * return php script filename
     *
     * @return string
     */
    public function getFilename(): string;


    /**
     * return request uri
     *
     * @return string
     */
    public function getUri(): string;

    /**
     * return request host
     *
     * @return string
     */
    public function getHost(): string;

    /**
     * return client ip
     *
     * @return string
     */
    public function getIp(): string;

    /**
     * return request content type
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * return request headers
     *
     * @return array
     */
    public function getHeaders(): array;
}
