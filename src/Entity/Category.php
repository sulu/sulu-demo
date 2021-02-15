<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sulu\Bundle\CategoryBundle\Entity\Category as SuluCategory;

/**
 * @ORM\Entity()
 * @ORM\Table(name="ca_categories")
 */
class Category extends SuluCategory
{
    /**
     * @ORM\Column(type="string", length=63, nullable=true)
     */
    private ?string $color;

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }
}
