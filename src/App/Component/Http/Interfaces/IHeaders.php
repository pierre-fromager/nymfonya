<?php

namespace App\Component\Http\Interfaces;

use App\Component\Http\Headers;

interface IHeaders
{

    const CONTENT_TYPE = 'Content-Type';
    const CONTENT_LENGTH = 'Content-Length';
    const ACCEPT_ENCODING = 'Accept-Encoding';
    const HEADER_ACA = 'Access-Control-Allow-';
    const HEADER_ACA_ORIGIN = self::HEADER_ACA . 'Origin';
    const HEADER_ACA_CREDENTIALS = self::HEADER_ACA . 'Credentials';
    const HEADER_ACA_METHODS = self::HEADER_ACA . 'Methods';
    const HEADER_ACA_HEADERS = self::HEADER_ACA . 'Headers';

    /**
     * instanciate
     */
    public function __construct();

    /**
     * add one header formaly done with given key and content
     *
     * @return string
     */
    public function add(string $key, string $content): Headers;

    /**
     * add multiples headers from assoc array and returns Headers instance
     *
     * @param array $headers
     * @return Headers
     */
    public function addMany(array $headers): Headers;

    /**
     * remove one header from his key and returns Headers instance
     *
     * @param string $key
     * @return Headers
     */
    public function remove(string $key): Headers;

    /**
     * returns all headers as assoc array
     *
     * @return array
     */
    public function get(): array;

    /**
     * returns all headers as normal array
     *
     * @return array
     */
    public function getRaw(): array;

    /**
     * send headers and returns Headers instance
     *
     * @return Headers
     */
    public function send(): Headers;
}
