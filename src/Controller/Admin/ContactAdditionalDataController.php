<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\ViewHandlerInterface;
use HandcraftedInTheAlps\RestRoutingBundle\Controller\Annotations\RouteResource;
use HandcraftedInTheAlps\RestRoutingBundle\Routing\ClassResourceInterface;
use Sulu\Bundle\ContactBundle\Admin\ContactAdmin;
use Sulu\Bundle\ContactBundle\Entity\ContactInterface;
use Sulu\Component\Rest\AbstractRestController;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @RouteResource("contact-additional-data")
 */
class ContactAdditionalDataController extends AbstractRestController implements ClassResourceInterface, SecuredControllerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        ViewHandlerInterface $viewHandler,
        ?TokenStorageInterface $tokenStorage = null
    ) {
        $this->entityManager = $entityManager;

        parent::__construct($viewHandler, $tokenStorage);
    }

    public function getAction(int $id): Response
    {
        /** @var Contact|null $contact */
        $contact = $this->entityManager->getRepository(ContactInterface::class)->find($id);
        if (!$contact) {
            throw new NotFoundHttpException();
        }

        return $this->handleView($this->view($this->getDataForEntity($contact)));
    }

    public function putAction(Request $request, int $id): Response
    {
        /** @var Contact|null $contact */
        $contact = $this->entityManager->getRepository(ContactInterface::class)->find($id);
        if (!$contact) {
            throw new NotFoundHttpException();
        }

        $this->mapDataToEntity($request->request->all(), $contact);
        $this->entityManager->flush();

        return $this->handleView($this->view($this->getDataForEntity($contact)));
    }

    /**
     * @return array<string, mixed>
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
     * @param array<string, mixed> $data
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
}
