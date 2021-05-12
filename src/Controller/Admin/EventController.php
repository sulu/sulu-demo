<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\ViewHandlerInterface;
use HandcraftedInTheAlps\RestRoutingBundle\Controller\Annotations\RouteResource;
use HandcraftedInTheAlps\RestRoutingBundle\Routing\ClassResourceInterface;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Bundle\RouteBundle\Entity\RouteRepositoryInterface;
use Sulu\Bundle\RouteBundle\Manager\RouteManagerInterface;
use Sulu\Component\Rest\AbstractRestController;
use Sulu\Component\Security\SecuredControllerInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @RouteResource("event")
 */
class EventController extends AbstractRestController implements ClassResourceInterface, SecuredControllerInterface
{
    private DoctrineListRepresentationFactory $doctrineListRepresentationFactory;
    private EntityManagerInterface $entityManager;
    private MediaManagerInterface $mediaManager;
    private WebspaceManagerInterface $webspaceManager;
    private RouteManagerInterface $routeManager;
    private RouteRepositoryInterface $routeRepository;

    public function __construct(
        DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        EntityManagerInterface $entityManager,
        MediaManagerInterface $mediaManager,
        WebspaceManagerInterface $webspaceManager,
        RouteManagerInterface $routeManager,
        RouteRepositoryInterface $routeRepository,
        ViewHandlerInterface $viewHandler,
        ?TokenStorageInterface $tokenStorage = null
    ) {
        $this->doctrineListRepresentationFactory = $doctrineListRepresentationFactory;
        $this->entityManager = $entityManager;
        $this->mediaManager = $mediaManager;
        $this->webspaceManager = $webspaceManager;
        $this->routeManager = $routeManager;
        $this->routeRepository = $routeRepository;

        parent::__construct($viewHandler, $tokenStorage);
    }

    public function cgetAction(): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            Event::RESOURCE_KEY
        );

        return $this->handleView($this->view($listRepresentation));
    }

    public function getAction(int $id): Response
    {
        $event = $this->entityManager->getRepository(Event::class)->find($id);
        if (!$event) {
            throw new NotFoundHttpException();
        }

        return $this->handleView($this->view($event));
    }

    public function putAction(Request $request, int $id): Response
    {
        $event = $this->entityManager->getRepository(Event::class)->find($id);
        if (!$event) {
            throw new NotFoundHttpException();
        }

        $this->mapDataToEntity($request->request->all(), $event);
        $this->updateRoutesForEntity($event);
        $this->entityManager->flush();

        return $this->handleView($this->view($event));
    }

    public function postAction(Request $request): Response
    {
        $event = new Event();

        $this->mapDataToEntity($request->request->all(), $event);
        $this->entityManager->persist($event);
        $this->entityManager->flush();

        $this->updateRoutesForEntity($event);
        $this->entityManager->flush();

        return $this->handleView($this->view($event, 201));
    }

    public function deleteAction(int $id): Response
    {
        /** @var Event $event */
        $event = $this->entityManager->getReference(Event::class, $id);
        $this->removeRoutesForEntity($event);
        $this->entityManager->remove($event);
        $this->entityManager->flush();

        return $this->handleView($this->view(null, 204));
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function mapDataToEntity(array $data, Event $entity): void
    {
        $imageId = $data['image']['id'] ?? null;

        $entity->setName($data['name']);
        $entity->setRoutePath($data['routePath']);
        $entity->setImage($imageId ? $this->mediaManager->getEntityById($imageId) : null);
        $entity->setStartDate($data['startDate'] ? new \DateTimeImmutable($data['startDate']) : null);
        $entity->setEndDate($data['endDate'] ? new \DateTimeImmutable($data['endDate']) : null);
    }

    protected function updateRoutesForEntity(Event $entity): void
    {
        // create route for all locales of the application because event entity is not localized
        foreach ($this->webspaceManager->getAllLocales() as $locale) {
            $this->routeManager->createOrUpdateByAttributes(
                Event::class,
                (string) $entity->getId(),
                $locale,
                $entity->getRoutePath(),
            );
        }
    }

    protected function removeRoutesForEntity(Event $entity): void
    {
        // remove route for all locales of the application because event entity is not localized
        foreach ($this->webspaceManager->getAllLocales() as $locale) {
            $routes = $this->routeRepository->findAllByEntity(
                Event::class,
                (string) $entity->getId(),
                $locale
            );

            foreach ($routes as $route) {
                $this->routeRepository->remove($route);
            }
        }
    }

    public function getSecurityContext(): string
    {
        return Event::SECURITY_CONTEXT;
    }
}
