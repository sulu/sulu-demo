<?php

namespace App\EventSubscriber;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\CategoryBundle\Entity\CategoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CategoryEventSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => [
                ['storeAdditionalDataToEntity', 10],
                ['addAdditionalDataToResponse', -10],
            ],
        ];
    }

    public function storeAdditionalDataToEntity(ResponseEvent $event): void
    {
        $requestRoute = $event->getRequest()->attributes->get('_route');
        $persistRoutes = ['sulu_category.post_category', 'sulu_category.put_category'];
        if (!\in_array($requestRoute, $persistRoutes, true)) {
            return;
        }

        $category = $this->getEntityForResponse($event->getResponse());
        if (!$category) {
            return;
        }

        $requestData = $event->getRequest()->request->all();
        $category->setColor($requestData['color']);

        $this->entityManager->flush();
    }

    public function addAdditionalDataToResponse(ResponseEvent $event): void
    {
        $requestRoute = $event->getRequest()->attributes->get('_route');
        $serializeRoutes = ['sulu_category.get_category', 'sulu_category.post_category', 'sulu_category.put_category'];
        if (!\in_array($requestRoute, $serializeRoutes, true)) {
            return;
        }

        $category = $this->getEntityForResponse($event->getResponse());
        if (!$category) {
            return;
        }

        $responseData = json_decode($event->getResponse()->getContent() ?: '{}', true);
        $responseData['color'] = $category->getColor();

        $event->getResponse()->setContent(json_encode($responseData) ?: null);
    }

    protected function getEntityForResponse(Response $response): ?Category
    {
        $id = json_decode($response->getContent() ?: '{}', true)['id'] ?? null;

        /** @var Category|null $category */
        $category = $id ? $this->entityManager->getRepository(CategoryInterface::class)->find($id) : null;

        return $category;
    }
}
