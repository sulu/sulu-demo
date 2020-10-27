<?php

declare(strict_types=1);

namespace App\Content\Type;

use App\Entity\Album;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Component\Content\Compat\PropertyInterface;
use Sulu\Component\Content\SimpleContentType;

class SingleAlbumSelection extends SimpleContentType
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct('single_album_selection', null);
    }

    public function getContentData(PropertyInterface $property): ?Album
    {
        $id = $property->getValue();

        if (empty($id)) {
            return $this->defaultValue;
        }

        return $this->entityManager->getRepository(Album::class)->find($id);
    }

    /**
     * @return array<string, int|null>
     */
    public function getViewData(PropertyInterface $property): array
    {
        return [
            'id' => $property->getValue(),
        ];
    }
}
