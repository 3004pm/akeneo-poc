<?php

namespace App\CacheBundle\Manager;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\CacheItem;

/**
 * Class RedisManager.
 *
 * @package App\CacheBundle\Manager
 * @author  Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class RedisManager
{
    /** @var AdapterInterface */
    protected $redisAdapter;

    /**
     * RedisManager constructor.
     *
     * @param AdapterInterface $redisAdapter
     */
    public function __construct(AdapterInterface $redisAdapter)
    {
        $this->redisAdapter = $redisAdapter;
    }

    /**
     * Set response data into cache.
     *
     * @param CacheItem $cacheItem
     * @param mixed     $dataToCache
     *
     * @return bool
     */
    public function setCacheData(CacheItem $cacheItem, $dataToCache): bool
    {
        $cacheItem->set(serialize($dataToCache));

        return $this->redisAdapter->save($cacheItem);
    }

    /**
     * Retrieve data from cache item.
     *
     * @param CacheItem $cacheItem
     *
     * @return mixed
     */
    public function getCacheData(CacheItem $cacheItem)
    {
        return unserialize($cacheItem->get());
    }

    /**
     * Retrieve cached item.
     *
     * @param string $cacheKey
     *
     * @return CacheItem
     * @throws InvalidArgumentException
     */
    public function getCacheItem(string $cacheKey): CacheItem
    {
        return $this->redisAdapter->getItem($cacheKey);
    }

    /**
     * Clear redis cache.
     *
     */
    public function clearCache(): void
    {
        $this->redisAdapter->clear();
    }
}