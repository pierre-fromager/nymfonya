<?php

declare(strict_types=1);

namespace App\Interfaces\Controllers;

interface IRestful
{
    /**
     * should be called when Request GET method is used
     *
     * @param array $slugs
     * @return mixed
     */
    public function index(array $slugs = []);


    /**
     * should be called when Request POST method is used
     *
     * @param array $slugs
     * @return mixed
     */
    public function store(array $slugs = []);

    /**
     * should be called when Request PUT or PATCH method is used
     *
     * @param array $slugs
     * @return mixed
     */
    public function update(array $slugs = []);

    /**
     * should be called when Request DELETE method is used
     *
     * @param array $slugs
     * @return mixed
     */
    public function delete(array $slugs = []);
}
