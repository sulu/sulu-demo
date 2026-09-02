<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ApplicationSettings;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @phpstan-type ApplicationSettingsData array{
 *     demobarText: string|null,
 * }
 */
class ApplicationSettingsController extends AbstractController implements SecuredControllerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/admin/api/application-settings/{id}", methods={"GET"}, name="app.get_application_settings")
     */
    public function getAction(): Response
    {
        $applicationSettings = $this->entityManager->getRepository(ApplicationSettings::class)->findOneBy([]);

        return $this->json($this->getDataForEntity($applicationSettings ?: new ApplicationSettings()));
    }

    /**
     * @Route("/admin/api/application-settings/{id}", methods={"PUT"}, name="app.put_application_settings")
     */
    public function putAction(Request $request): Response
    {
        $applicationSettings = $this->entityManager->getRepository(ApplicationSettings::class)->findOneBy([]);
        if (!$applicationSettings) {
            $applicationSettings = new ApplicationSettings();
            $this->entityManager->persist($applicationSettings);
        }

        /** @var ApplicationSettingsData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $applicationSettings);
        $this->entityManager->flush();

        return $this->json($this->getDataForEntity($applicationSettings));
    }

    /**
     * @return ApplicationSettingsData $data
     */
    protected function getDataForEntity(ApplicationSettings $entity): array
    {
        return [
            'demobarText' => $entity->getDemobarText(),
        ];
    }

    /**
     * @param ApplicationSettingsData $data
     */
    protected function mapDataToEntity(array $data, ApplicationSettings $entity): void
    {
        $entity->setDemobarText($data['demobarText']);
    }

    public function getSecurityContext(): string
    {
        return ApplicationSettings::SECURITY_CONTEXT;
    }

    public function getLocale(Request $request): ?string
    {
        return $request->query->get('locale');
    }
}
