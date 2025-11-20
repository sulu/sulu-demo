<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Sulu\Bundle\SecurityBundle\Entity\User as SuluUser;

#[ORM\Entity]
#[ORM\Table(name: 'se_users')]
#[ORM\HasLifecycleCallbacks]
class User extends SuluUser
{
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, unique: true)]
    private ?string $externalId = null;

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): void
    {
        $this->externalId = $externalId;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function generateExternalIdIfEmpty(): void
    {
        if (null === $this->externalId || '' === $this->externalId) {
            $this->externalId = Uuid::uuid7()->toString();
        }
    }
}
