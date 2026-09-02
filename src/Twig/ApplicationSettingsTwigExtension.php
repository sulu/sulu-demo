<?php

namespace App\Twig;

use App\Entity\ApplicationSettings;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ApplicationSettingsTwigExtension extends AbstractExtension
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
            new TwigFunction('load_application_settings', [$this, 'loadApplicationSettings']),
        ];
    }

    public function loadApplicationSettings(): ApplicationSettings
    {
        $applicationSettings = $this->entityManager->getRepository(ApplicationSettings::class)->findOneBy([]) ?? null;

        return $applicationSettings ?: new ApplicationSettings();
    }
}
