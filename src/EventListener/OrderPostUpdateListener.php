<?php

namespace App\EventListener;

use App\Entity\Order;
use Doctrine\ORM\Event\PostUpdateEventArgs;

class OrderPostUpdateListener
{
    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Order) {
            return;
        }

        dump('Order updated, status: ' . $entity->getStatus());
    }
}
