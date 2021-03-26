<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ApplicationSettings;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\ViewHandlerInterface;
use HandcraftedInTheAlps\RestRoutingBundle\Controller\Annotations\RouteResource;
use HandcraftedInTheAlps\RestRoutingBundle\Routing\ClassResourceInterface;
use Sulu\Component\Rest\AbstractRestController;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @RouteResource("application-settings")
 */
class ApplicationSettingsController extends AbstractRestController implements ClassResourceInterface, SecuredControllerInterface
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

    public function getAction(): Response
    {
        $applicationSettings = $this->entityManager->getRepository(ApplicationSettings::class)->findOneBy([]);

        return $this->handleView($this->view($applicationSettings ?: new ApplicationSettings()));
    }

    public function putAction(Request $request): Response
    {
        $applicationSettings = $this->entityManager->getRepository(ApplicationSettings::class)->findOneBy([]);
        if (!$applicationSettings) {
            $applicationSettings = new ApplicationSettings();
            $this->entityManager->persist($applicationSettings);
        }

        $this->mapDataToEntity($request->request->all(), $applicationSettings);
        $this->entityManager->flush();

        return $this->handleView($this->view($applicationSettings));
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function mapDataToEntity(array $data, ApplicationSettings $entity): void
    {
        $entity->setDemobarText($data['demobarText']);
    }

    public function getSecurityContext(): string
    {
        return ApplicationSettings::SECURITY_CONTEXT;
    }
}
