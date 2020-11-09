<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @phpstan-type EventData array{
 *     id: int|null,
 *     name: string,
 *     image: array{id: int}|null,
 *     startDate: string|null,
 *     endDate: string|null,
 * }
 */
class EventController extends AbstractController implements SecuredControllerInterface
{
    private DoctrineListRepresentationFactory $doctrineListRepresentationFactory;
    private EntityManagerInterface $entityManager;
    private MediaManagerInterface $mediaManager;

    public function __construct(
        DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        EntityManagerInterface $entityManager,
        MediaManagerInterface $mediaManager
    ) {
        $this->doctrineListRepresentationFactory = $doctrineListRepresentationFactory;
        $this->entityManager = $entityManager;
        $this->mediaManager = $mediaManager;
    }

    /**
     * @Route("/admin/api/events/{id}", methods={"GET"}, name="app.get_event")
     */
    public function getAction(int $id): Response
    {
        $event = $this->entityManager->getRepository(Event::class)->find($id);
        if (!$event) {
            throw new NotFoundHttpException();
        }

        return $this->json($this->getDataForEntity($event));
    }

    /**
     * @Route("/admin/api/events/{id}", methods={"PUT"}, name="app.put_event")
     */
    public function putAction(Request $request, int $id): Response
    {
        $event = $this->entityManager->getRepository(Event::class)->find($id);
        if (!$event) {
            throw new NotFoundHttpException();
        }

        /** @var EventData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $event);
        $this->entityManager->flush();

        return $this->json($this->getDataForEntity($event));
    }

    /**
     * @Route("/admin/api/events", methods={"POST"}, name="app.post_event")
     */
    public function postAction(Request $request): Response
    {
        $event = new Event();

        /** @var EventData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $event);
        $this->entityManager->persist($event);
        $this->entityManager->flush();

        return $this->json($this->getDataForEntity($event), 201);
    }

    /**
     * @Route("/admin/api/events/{id}", methods={"DELETE"}, name="app.delete_event")
     */
    public function deleteAction(int $id): Response
    {
        /** @var Event $event */
        $event = $this->entityManager->getReference(Event::class, $id);
        $this->entityManager->remove($event);
        $this->entityManager->flush();

        return $this->json(null, 204);
    }

    /**
     * @Route("/admin/api/events", methods={"GET"}, name="app.get_event_list")
     */
    public function getListAction(): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            Event::RESOURCE_KEY
        );

        return $this->json($listRepresentation->toArray());
    }

    /**
     * @return EventData $data
     */
    protected function getDataForEntity(Event $entity): array
    {
        $image = $entity->getImage();
        $startDate = $entity->getStartDate();
        $endDate = $entity->getEndDate();

        return [
            'id' => $entity->getId(),
            'name' => $entity->getName(),
            'image' => $image
                ? ['id' => $image->getId()]
                : null,
            'startDate' => $startDate ? $startDate->format('c') : null,
            'endDate' => $endDate ? $endDate->format('c') : null,
        ];
    }

    /**
     * @param EventData $data
     */
    protected function mapDataToEntity(array $data, Event $entity): void
    {
        $imageId = $data['image']['id'] ?? null;

        $entity->setName($data['name']);
        $entity->setImage($imageId ? $this->mediaManager->getEntityById($imageId) : null);
        $entity->setStartDate($data['startDate'] ? new \DateTimeImmutable($data['startDate']) : null);
        $entity->setEndDate($data['endDate'] ? new \DateTimeImmutable($data['endDate']) : null);
    }

    public function getSecurityContext(): string
    {
        return Event::SECURITY_CONTEXT;
    }

    public function getLocale(Request $request): ?string
    {
        return $request->query->get('locale');
    }
}
