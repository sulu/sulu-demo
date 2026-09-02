<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\ContactBundle\Admin\ContactAdmin;
use Sulu\Bundle\ContactBundle\Entity\ContactInterface;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @phpstan-type ContactAdditionalData array{
 *     id: int|null,
 *     socialSecurityNumber: string|null,
 *     externalCrmId: string|null,
 * }
 */
class ContactAdditionalDataController extends AbstractController implements SecuredControllerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/api/contact-additional-data/{id}", methods={"GET"}, name="app.get_contact_additional_data")
     */
    public function getAction(int $id): Response
    {
        /** @var Contact|null $contact */
        $contact = $this->entityManager->getRepository(ContactInterface::class)->find($id);
        if (!$contact) {
            throw new NotFoundHttpException();
        }

        return $this->json($this->getDataForEntity($contact));
    }

    /**
     * @Route("/admin/api/contact-additional-data/{id}", methods={"PUT"}, name="app.put_contact_additional_data")
     */
    public function putAction(Request $request, int $id): Response
    {
        /** @var Contact|null $contact */
        $contact = $this->entityManager->getRepository(ContactInterface::class)->find($id);
        if (!$contact) {
            throw new NotFoundHttpException();
        }

        /** @var ContactAdditionalData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $contact);
        $this->entityManager->flush();

        return $this->json($this->getDataForEntity($contact));
    }

    /**
     * @return ContactAdditionalData
     */
    protected function getDataForEntity(Contact $entity): array
    {
        return [
            'id' => $entity->getId(),
            'socialSecurityNumber' => $entity->getSocialSecurityNumber(),
            'externalCrmId' => $entity->getExternalCrmId(),
        ];
    }

    /**
     * @param ContactAdditionalData $data
     */
    protected function mapDataToEntity(array $data, Contact $entity): void
    {
        $entity->setSocialSecurityNumber($data['socialSecurityNumber']);
        $entity->setExternalCrmId($data['externalCrmId']);
    }

    public function getSecurityContext(): string
    {
        return ContactAdmin::CONTACT_SECURITY_CONTEXT;
    }

    public function getLocale(Request $request): ?string
    {
        return $request->query->get('locale');
    }
}
