<?php

namespace Minicli\Util;

class FileCache
{
    protected $cache_dir;
    protected $cache_expiry;

    /**
     * FileCache constructor.
     * @param string $cache_dir
     * @param int $cache_expiry
     */
    public function __construct($cache_dir, $cache_expiry = 60)
    {
        $this->cache_dir = $cache_dir;
        $this->cache_expiry = $cache_expiry;
    }

    /**
     * @param string $unique_identifier
     * @return string
     */
    public function getCacheFile($unique_identifier)
    {
        return $this->cache_dir . '/' . md5($unique_identifier) . '.json';
    }

    /**
     * @param string $unique_identifier
     * @return null|string
     */
    public function getCached($unique_identifier)
    {
        $cache_file = $this->getCacheFile($unique_identifier);

        if (is_file($cache_file)) {
            return file_get_contents($cache_file);
        }

        return null;
    }

    /**
     * @param string $unique_identifier
     * @return null|string
     */
    public function getCachedUnlessExpired($unique_identifier)
    {
        $cache_file = $this->getCacheFile($unique_identifier);

        // is it still valid?
        if (is_file($cache_file) && (time() - filemtime($cache_file) < 60*$this->cache_expiry)) {
            return file_get_contents($cache_file);
        }

        return null;
    }

    /**
     * @param string $content
     * @param string $unique_identifier
     */
    public function save($content, $unique_identifier)
    {
        $cache_file = $this->getCacheFile($unique_identifier);
        
        file_put_contents($cache_file, $content);
    }
}