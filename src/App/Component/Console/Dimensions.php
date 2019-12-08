<?php

namespace App\Component\Console;

class Dimensions
{

    /**
     * width
     *
     * @var int
     */
    protected $width;

    /**
     * height
     *
     * @var int
     */
    protected $height;

    /**
     * instanciate
     *
     * @param integer $width
     * @param integer $height
     */
    public function __construct(int $width = 0, int $height = 0)
    {
        $this->set($width, $height);
    }

    /**
     * set width and height
     *
     * @param integer $width
     * @param integer $height
     * @return Dimensions
     */
    public function set(int $width, int $height): Dimensions
    {
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    /**
     * returns width
     *
     * @return integer
     */
    public function width(): int
    {
        return $this->width;
    }

    /**
     * returns height
     *
     * @return integer
     */
    public function height(): int
    {
        return  $this->height;
    }
}
