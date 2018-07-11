<?php

namespace App\CacheBundle\Subscriber;

use Akeneo\Component\StorageUtils\StorageEvents;
use App\CacheBundle\Manager\RedisManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class ObjectSubscriber.
 *
 * @package App\CacheBundle\Subscriber
 * @author  Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class ObjectSubscriber implements EventSubscriberInterface
{
    /** @var RedisManager */
    protected $redisManager;

    /**
     * ObjectSubscriber constructor.
     *
     * @param RedisManager $redisManager
     */
    public function __construct(RedisManager $redisManager)
    {
        $this->redisManager = $redisManager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
            StorageEvents::POST_SAVE => 'postSave',
        ];
    }

    /**
     * Handle dependent attribute on pre save event.
     *
     * @param GenericEvent $event The generic event.
     */
    public function postSave(GenericEvent $event): void
    {
        $this->redisManager->clearCache();
    }
}
