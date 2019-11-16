<?php

namespace App\Component\Http;

class Route
{

    protected $method;
    protected $expr;
    protected $callable;

    /**
     * instanciate
     *
     * @param string $routeItem
     */
    public function __construct(string $routeItem)
    {
        $this->method = 'GET';
        $this->expr = '/^(*.)$/';
        $this->callable = function () {
        };
        if (strpos($routeItem, ';') !== false) {
            list(
                $this->method,
                $this->expr,
                $this->callable
            ) = explode(';', $routeItem);
        } else {
            $this->expr = $routeItem;
        }
    }

    /**
     * return regexp pattern
     *
     * @return string
     */
    public function getExpr(): string
    {
        return $this->expr;
    }

    /**
     * return required request method
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }
}
