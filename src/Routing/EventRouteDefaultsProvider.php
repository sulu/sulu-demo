<?php

namespace App\Routing;

use App\Controller\Website\EventController;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\RouteBundle\Routing\Defaults\RouteDefaultsProviderInterface;

class EventRouteDefaultsProvider implements RouteDefaultsProviderInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @return mixed[]
     */
    public function getByEntity($entityClass, $id, $locale, $object = null)
    {
        return [
            '_controller' => EventController::class . '::indexAction',
            'event' => $object ?: $this->entityManager->getRepository(Event::class)->find($id),
        ];
    }

    public function isPublished($entityClass, $id, $locale)
    {
        return true;
    }

    public function supports($entityClass)
    {
        return Event::class === $entityClass;
    }
}
