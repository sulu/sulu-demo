<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sulu\Bundle\ContactBundle\Entity\Contact as SuluContact;

/**
 * @ORM\Entity()
 * @ORM\Table(name="co_contacts")
 */
class Contact extends SuluContact
{
    /**
     * @ORM\Column(type="string", length=63, nullable=true)
     */
    private ?string $socialSecurityNumber;

    /**
     * @ORM\Column(type="string", length=63, nullable=true)
     */
    private ?string $externalCrmId;

    public function getSocialSecurityNumber(): ?string
    {
        return $this->socialSecurityNumber;
    }

    public function setSocialSecurityNumber(?string $socialSecurityNumber): void
    {
        $this->socialSecurityNumber = $socialSecurityNumber;
    }

    public function getExternalCrmId(): ?string
    {
        return $this->externalCrmId;
    }

    public function setExternalCrmId(?string $externalCrmId): void
    {
        $this->externalCrmId = $externalCrmId;
    }
}
