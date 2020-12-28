<?php

declare(strict_types=1);

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
    protected function cacheFileExpired(): bool
    {
        $this->cacheFilename = $this->getCacheFilename();
        return (file_exists($this->cacheFilename) === false)
            ? true
            : filemtime($this->cacheFilename) < (time() - $this->cacheTtl);
    }

    /**
     * returns cache content
     *
     * @return string
     */
    protected function getFileCache(): string
    {
        return file_get_contents($this->getCacheFilename());
    }

    /**
     * set cache content
     *
     * @param string $content
     * @return integer
     */
    protected function setFileCache(string $content): int
    {
        return file_put_contents(
            $this->getCacheFilename(),
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
    protected function clearFileCache(bool $fromRequest = false)
    {
        if ($fromRequest) {
            $filename = $this->getCacheFilename();
            if (is_writable($filename)) {
                @unlink($filename);
            }
        } else {
            $files = glob($this->getFileCachePath() . '*');
            $fileList = is_array($files)
                ? array_filter($files, 'is_file')
                : [];
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
        $path = $this->getFileCachePath();
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
    protected function getFileCachePath(): string
    {
        return dirname($this->request->getFilename()) . '/../cache/';
    }
}
