<?php

namespace App\Twig;

use App\Entity\WebspaceSetting;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WebspaceSettingTwigExtension extends AbstractExtension
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('load_webspace_setting', [$this, 'loadWebspaceSetting']),
        ];
    }

    public function loadWebspaceSetting(string $webspace): WebspaceSetting
    {
        $applicationSettings = $this->entityManager->getRepository(WebspaceSetting::class)->findOneBy(['webspace' => $webspace]) ?? null;

        return $applicationSettings ?: new WebspaceSetting($webspace);
    }
}
