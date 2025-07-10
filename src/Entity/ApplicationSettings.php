<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="app_application_settings")
 */
class ApplicationSettings implements AuditableInterface
{
    use AuditableTrait;

    public const RESOURCE_KEY = 'application_settings';
    public const FORM_KEY = 'application_settings';
    public const SECURITY_CONTEXT = 'sulu.settings.application_settings';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $demobarText = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDemobarText(): ?string
    {
        return $this->demobarText;
    }

    public function setDemobarText(?string $demobarText): void
    {
        $this->demobarText = $demobarText;
    }
}
