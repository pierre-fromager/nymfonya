<?php

namespace App\Reuse\Controllers\Api;

trait TFileCache
{

    protected $cacheTtl = 60 * 5;
    protected $cacheFilename = '';

    /**
     * return true if cache is expired
     *
     * @return boolean
     */
    protected function cacheExpired(): bool
    {
        $this->cacheFilename = $this->getCacheFilename();
        return (file_exists($this->cacheFilename) == false)
            ? true
            : filemtime($this->cacheFilename) < (time() - $this->cacheTtl);
    }

    /**
     * returns cache content
     *
     * @return string
     */
    protected function getCache(): string
    {
        return file_get_contents($this->cacheFilename);
    }

    /**
     * set cache content
     *
     * @param string $content
     * @return integer
     */
    protected function setCache(string $content): int
    {
        return file_put_contents(
            $this->cacheFilename,
            $content,
            LOCK_EX
        );
    }

    /**
     * clear cache dir
     *
     * @param bool $fromRequest
     * @return void
     */
    protected function clearCache(bool $fromRequest = false)
    {
        if ($fromRequest) {
            @unlink($this->getCacheFilename());
        } else {
            $fileList = array_filter(
                glob($this->getCachePath() . '*'),
                'is_file'
            );
            $counter = count($fileList);
            for ($c = 0; $c < $counter; $c++) {
                unlink($fileList[$c]);
            }
        }
    }

    /**
     * returns cache filename from request uri
     *
     * @return string
     */
    protected function getCacheFilename(): string
    {
        $path = $this->getCachePath();
        if (!file_exists($path)) {
            mkdir($path);
        }
        $filename = md5($this->request->getUri());
        return $path . $filename;
    }

    /**
     * returns cache path from request script filename
     *
     * @return string
     */
    protected function getCachePath(): string
    {
        return dirname($this->request->getFilename()) . '/../cache/';
    }
}
