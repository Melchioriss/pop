<?php

namespace PlayOrPay\Infrastructure\Storage\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use PlayOrPay\Domain\Contracts\Entity\OnUpdateEventListenerInterface;

class EntityUpdateMessengerSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [Events::preUpdate];
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (!$entity instanceof OnUpdateEventListenerInterface) {
            return;
        }

        $entity->onUpdate();
    }
}
