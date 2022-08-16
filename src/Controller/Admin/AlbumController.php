<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\Album;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @phpstan-type AlbumData array{
 *     id: int|null,
 *     title: string,
 *     image: array{id: int}|null,
 *     tracklist: mixed[],
 * }
 */
class AlbumController extends AbstractController implements SecuredControllerInterface
{
    public function __construct(private readonly DoctrineListRepresentationFactory $doctrineListRepresentationFactory, private readonly EntityManagerInterface $entityManager, private readonly MediaManagerInterface $mediaManager)
    {
    }

    #[Route(path: '/admin/api/albums/{id}', methods: ['GET'], name: 'app.get_album')]
    public function getAction(int $id): Response
    {
        $album = $this->entityManager->getRepository(Album::class)->find($id);
        if (!$album instanceof Album) {
            throw new NotFoundHttpException();
        }

        return $this->json($this->getDataForEntity($album));
    }

    #[Route(path: '/admin/api/albums/{id}', methods: ['PUT'], name: 'app.put_album')]
    public function putAction(Request $request, int $id): Response
    {
        $album = $this->entityManager->getRepository(Album::class)->find($id);
        if (!$album instanceof Album) {
            throw new NotFoundHttpException();
        }
        /** @var AlbumData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $album);
        $this->entityManager->flush();

        return $this->json($this->getDataForEntity($album));
    }

    #[Route(path: '/admin/api/albums', methods: ['POST'], name: 'app.post_album')]
    public function postAction(Request $request): Response
    {
        $album = new Album();
        /** @var AlbumData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $album);
        $this->entityManager->persist($album);
        $this->entityManager->flush();

        return $this->json($this->getDataForEntity($album), 201);
    }

    #[Route(path: '/admin/api/albums/{id}', methods: ['DELETE'], name: 'app.delete_album')]
    public function deleteAction(int $id): Response
    {
        /** @var Album $album */
        $album = $this->entityManager->getReference(Album::class, $id);
        $this->entityManager->remove($album);
        $this->entityManager->flush();

        return $this->json(null, 204);
    }

    #[Route(path: '/admin/api/albums', methods: ['GET'], name: 'app.get_album_list')]
    public function getListAction(): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            Album::RESOURCE_KEY,
        );

        return $this->json($listRepresentation->toArray());
    }

    /**
     * @return AlbumData $data
     */
    protected function getDataForEntity(Album $entity): array
    {
        $image = $entity->getImage();

        return [
            'id' => $entity->getId(),
            'title' => $entity->getTitle(),
            'image' => null !== $image
                ? ['id' => $image->getId()]
                : null,
            'tracklist' => $entity->getTracklist(),
        ];
    }

    /**
     * @param AlbumData $data
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

    public function getLocale(Request $request): ?string
    {
        return $request->query->get('locale');
    }
}
