<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\Album;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\ViewHandlerInterface;
use HandcraftedInTheAlps\RestRoutingBundle\Controller\Annotations\RouteResource;
use HandcraftedInTheAlps\RestRoutingBundle\Routing\ClassResourceInterface;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Component\Rest\AbstractRestController;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @RouteResource("album")
 */
class AlbumController extends AbstractRestController implements ClassResourceInterface, SecuredControllerInterface
{
    private DoctrineListRepresentationFactory $doctrineListRepresentationFactory;
    private EntityManagerInterface $entityManager;
    private MediaManagerInterface $mediaManager;

    public function __construct(
        DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        EntityManagerInterface $entityManager,
        MediaManagerInterface $mediaManager,
        ViewHandlerInterface $viewHandler,
        ?TokenStorageInterface $tokenStorage = null
    ) {
        $this->doctrineListRepresentationFactory = $doctrineListRepresentationFactory;
        $this->entityManager = $entityManager;
        $this->mediaManager = $mediaManager;

        parent::__construct($viewHandler, $tokenStorage);
    }

    public function cgetAction(): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            Album::RESOURCE_KEY
        );

        return $this->handleView($this->view($listRepresentation));
    }

    public function getAction(int $id): Response
    {
        $album = $this->entityManager->getRepository(Album::class)->find($id);
        if (!$album) {
            throw new NotFoundHttpException();
        }

        return $this->handleView($this->view($album));
    }

    public function putAction(Request $request, int $id): Response
    {
        $album = $this->entityManager->getRepository(Album::class)->find($id);
        if (!$album) {
            throw new NotFoundHttpException();
        }

        $this->mapDataToEntity($request->request->all(), $album);
        $this->entityManager->flush();

        return $this->handleView($this->view($album));
    }

    public function postAction(Request $request): Response
    {
        $album = new Album();

        $this->mapDataToEntity($request->request->all(), $album);
        $this->entityManager->persist($album);
        $this->entityManager->flush();

        return $this->handleView($this->view($album, 201));
    }

    public function deleteAction(int $id): Response
    {
        /** @var Album $album */
        $album = $this->entityManager->getReference(Album::class, $id);
        $this->entityManager->remove($album);
        $this->entityManager->flush();

        return $this->handleView($this->view(null, 204));
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function mapDataToEntity(array $data, Album $entity): void
    {
        $imageId = $data['image']['id'] ?? null;

        $entity->setTitle($data['title']);
        $entity->setImage($imageId ? $this->mediaManager->getEntityById($imageId) : null);
        $entity->setTracklist($data['tracklist']);
    }

    public function getSecurityContext(): string
    {
        return Album::SECURITY_CONTEXT;
    }
}
