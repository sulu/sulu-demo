<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\WebspaceSettingAdmin;
use App\Entity\WebspaceSetting;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @phpstan-type WebspaceSettingsData array{
 *     demobarText: string|null,
 * }
 */
class WebspaceSettingController extends AbstractController implements SecuredControllerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
    ) {
    }

    /**
     * @Route("/admin/api/webspace-settings/{id}", methods={"GET"}, name="app.get_webspace_settings")
     */
    public function getAction(Request $request): Response
    {
        $webspace = $request->query->get('webspace');

        $webspaceSetting = $this->entityManager->getRepository(WebspaceSetting::class)->findOneBy([
            'webspace' => $webspace,
        ]);

        return $this->json($this->getDataForEntity($webspaceSetting ?: new WebspaceSetting($webspace)));
    }

    /**
     * @Route("/admin/api/webspace-settings/{id}", methods={"PUT"}, name="app.put_webspace_settings")
     */
    public function putAction(Request $request): Response
    {
        $webspace = $request->query->get('webspace');

        $webspaceSetting = $this->entityManager->getRepository(WebspaceSetting::class)->findOneBy([
            'webspace' => $webspace,
        ]);

        if (!$webspaceSetting) {
            $webspaceSetting = new WebspaceSetting($webspace);
            $this->entityManager->persist($webspaceSetting);
        }

        /** @var WebspaceSettingsData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $webspaceSetting);
        $this->entityManager->flush();

        return $this->json($this->getDataForEntity($webspaceSetting));
    }

    /**
     * @return WebspaceSettingsData $data
     */
    protected function getDataForEntity(WebspaceSetting $entity): array
    {
        return [
            'demobarText' => $entity->getDemobarText(),
        ];
    }

    /**
     * @param WebspaceSettingsData $data
     */
    protected function mapDataToEntity(array $data, WebspaceSetting $entity): void
    {
        $entity->setDemobarText($data['demobarText']);
    }

    public function getSecurityContext()
    {
        $request = $this->requestStack->getCurrentRequest();

        return WebspaceSettingAdmin::getWebspaceSettingSecurityContext($request->query->get('webspace'));
    }

    public function getLocale(Request $request): ?string
    {
        return $request->query->get('locale');
    }
}
