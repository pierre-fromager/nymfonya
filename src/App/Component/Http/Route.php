<?php

namespace App\Component\Http;

use App\Component\Http\Interfaces\IRoute;

class Route implements IRoute
{

    /**
     * allowed request method
     *
     * @var string
     */
    protected $method;

    /**
     * uri validation regexp
     *
     * @var string
     */
    protected $expr;

    /**
     * slug collection
     *
     * @var array
     */
    protected $slugs;

    /**
     * instanciate
     *
     * @param string $routeItem
     */
    public function __construct(string $routeItem)
    {
        $this->method = 'GET';
        $this->expr = '/^(*.)$/';
        $this->slugs = [];
        if (strpos($routeItem, ';') !== false) {
            list(
                $this->method,
                $this->expr,
                $slugs
            ) = explode(';', $routeItem);
            $this->slugs = $this->parsedSlugs($slugs);
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

    /**
     * return slugs
     *
     * @return array
     */
    public function getSlugs(): array
    {
        return $this->slugs;
    }

    /**
     * return parsed slugs as string collection
     *
     * @param string $rawSlug
     * @return array
     */
    protected function parsedSlugs(string $rawSlug): array
    {
        if (is_null($rawSlug) || empty($rawSlug)) {
            return [];
        }
        return explode(',', $rawSlug);
    }
}
