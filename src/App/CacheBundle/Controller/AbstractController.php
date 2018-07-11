<?php

namespace App\CacheBundle\Controller;

use App\CacheBundle\Manager\RedisManager;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as ParentController;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class AbstractController.
 *
 * @package App\CacheBundle\Controller
 * @author  Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
abstract class AbstractController extends ParentController
{
    /** @var RedisManager */
    protected $redisManager;
    /** @var NormalizerInterface */
    protected $normalizer;

    /**
     * Retrieve cached item.
     *
     * @param Request $request
     *
     * @return CacheItem
     * @throws InvalidArgumentException
     */
    protected function getCachedItem(Request $request): CacheItem
    {
        return $this->getRedisManager()->getCacheItem(
            $this->getCacheKey($request)
        );
    }

    /**
     * Generate cache key using request uri.
     *
     * @param Request $request
     *
     * @return string
     */
    protected function getCacheKey(Request $request): string
    {
       return base64_encode($request->getUri());
    }

    /**
     * Retrieve redis manager.
     *
     * @return RedisManager
     */
    protected function getRedisManager(): RedisManager
    {
        if (null === $this->redisManager) {
            $this->redisManager = $this->getService('app_cache.manager.redis');
        }

        return $this->redisManager;
    }

    /**
     * Retrieve pim serializer.
     *
     * @return NormalizerInterface
     */
    protected function getNormalizer(): NormalizerInterface
    {
        if (null === $this->normalizer) {
            $this->normalizer = $this->getService('pim_serializer');
        }

        return $this->normalizer;
    }

    /**
     * Retrieve service from container using its name.
     *
     * @param string $serviceName
     *
     * @return mixed
     */
    protected function getService(string $serviceName)
    {
        return $this->container->get($serviceName);
    }
}