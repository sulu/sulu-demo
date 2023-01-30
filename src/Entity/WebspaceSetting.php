<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;


#[ORM\Entity]
#[ORM\Table(name: 'app_webspace_setting')]
class WebspaceSetting implements AuditableInterface
{
    use AuditableTrait;

    public const RESOURCE_KEY = 'webspace_settings';

    #[ORM\Id()]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 32, nullable: false, unique: true)]
    private string $webspace;

    #[ORM\Column(type: "string", length: 191, nullable: true)]
    private ?string $demobarText = null;

    public function __construct(string $webspace)
    {
        $this->webspace = $webspace;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWebspace(): string
    {
        return $this->webspace;
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
