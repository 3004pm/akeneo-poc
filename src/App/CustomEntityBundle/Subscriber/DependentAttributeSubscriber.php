<?php

namespace App\CustomEntityBundle\Subscriber;

use Akeneo\Component\StorageUtils\StorageEvents;
use App\CustomEntityBundle\Entity\DependentAttribute;
use App\CustomEntityBundle\Manager\DependentAttributeManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class DependantAttributeSubscriber.
 *
 * @package App\CustomEntityBundle\Subscriber
 * @author  Jessy JURKOWSKI <jessy.jurkowski@cgi.com>
 */
class DependentAttributeSubscriber implements EventSubscriberInterface
{
    /** @var DependentAttributeManager */
    protected $dependentAttributeManager;

    /**
     * DependentAttributeSubscriber constructor.
     *
     * @param DependentAttributeManager $dependentAttributeManager
     */
    public function __construct(DependentAttributeManager $dependentAttributeManager)
    {
        $this->dependentAttributeManager = $dependentAttributeManager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
            StorageEvents::PRE_SAVE => 'preSave',
        ];
    }

    /**
     * Handle dependent attribute on pre save event.
     *
     * @param GenericEvent $event The generic event.
     */
    public function preSave(GenericEvent $event): void
    {
        if (false === $this->supportObject($event)) {
            return;
        }

        /** @var DependentAttribute $dependentAttribute */
        $dependentAttribute = $event->getSubject();

        $this->onUpdate($dependentAttribute);
    }

    /**
     * Handle dependent attribute on update.
     *
     * @param DependentAttribute $dependentAttribute The dependent attribute.
     */
    protected function onUpdate(DependentAttribute $dependentAttribute): void
    {
        $this->dependentAttributeManager->setCode($dependentAttribute);
    }

    /**
     * Check if object can be handle by this subscriber.
     *
     * @param GenericEvent $event The generic event.
     *
     * @return bool
     */
    protected function supportObject(GenericEvent $event): bool
    {
        $object = $event->getSubject();

        return $object instanceof DependentAttribute;
    }
}
