<?php

namespace App\Reuse\Controllers\Api;

use App\Http\Request;
use App\Interfaces\Controllers\IApi;

trait TRelay
{

    protected $apiRelayResponse;
    protected $apiRelayHttpCode;
    protected $apiRelayHeaders;
    protected $apiRelayOptionHeader = false;
    protected $apiRelayOptionVerbose = false;

    /**
     * make http request to url with method and headers
     * then set apiRelayResponse with reponse content
     * and apiRelayHttpCode with status code
     *
     * @param string $method
     * @param string $url
     * @param array $headers
     * @param array $datas
     * @return void
     */
    protected function apiRelayRequest(string $method, string $url, array $headers = [], $datas = [])
    {
        $cha = curl_init();
        if (false !== $cha) {
            curl_setopt($cha, CURLOPT_VERBOSE, false);
            curl_setopt($cha, CURLOPT_URL, $url);
            curl_setopt($cha, CURLOPT_POST, ($method == Request::METHOD_POST));
            curl_setopt($cha, CURLOPT_TIMEOUT, 300);
            curl_setopt($cha, CURLOPT_USERAGENT, IApi::USER_AGENT);
            curl_setopt($cha, CURLOPT_BUFFERSIZE, IApi::BUFFER_SIZE);
            curl_setopt($cha, CURLOPT_HTTPHEADER, $headers);
            if ($this->apiRelayOptionHeader) {
                curl_setopt($cha, CURLOPT_VERBOSE, 1);
                curl_setopt($cha, CURLOPT_HEADER, 1);
            }
            if (Request::METHOD_POST == $method && !empty($datas)) {
                curl_setopt(
                    $cha,
                    CURLOPT_POSTFIELDS,
                    http_build_query($datas)
                );
            }
            curl_setopt($cha, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($cha);
            $error = (false === $result);
            $this->apiRelayResponse = ($error) ? 'Api relay error' : $result;
            $curlInfoCode = ($error) ? 500 : curl_getinfo($cha, CURLINFO_HTTP_CODE);
            $this->apiRelayHttpCode = (false === $curlInfoCode) ? 500 : $curlInfoCode;
            if ($this->apiRelayOptionHeader && false === $error) {
                $this->apiRelayHeaders = [];
                $headerSize = curl_getinfo($cha, CURLINFO_HEADER_SIZE);
                $rawHeaders = substr($this->apiRelayResponse, 0, $headerSize);
                $this->apiRelayHeaders = explode("\r\n\r\n", $rawHeaders, 2);
                $this->apiRelayResponse = substr(
                    $this->apiRelayResponse,
                    $headerSize
                );
            }
            if (false === $error) {
                curl_close($cha);
            }
        }
    }
}
