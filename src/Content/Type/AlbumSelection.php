<?php

declare(strict_types=1);

namespace App\Content\Type;

use App\Entity\Album;
use App\Repository\AlbumRepository;
use Sulu\Component\Content\Compat\PropertyInterface;
use Sulu\Component\Content\SimpleContentType;

class AlbumSelection extends SimpleContentType
{
    protected AlbumRepository $albumRepository;

    public function __construct(AlbumRepository $albumRepository)
    {
        $this->albumRepository = $albumRepository;

        parent::__construct('album_selection', []);
    }

    /**
     * @return Album[]
     */
    public function getContentData(PropertyInterface $property): array
    {
        $ids = $property->getValue();

        if (empty($ids)) {
            return $this->defaultValue;
        }

        return $this->albumRepository->findByIds($ids);
    }

    /**
     * @return array<string, array<int>>
     */
    public function getViewData(PropertyInterface $property): array
    {
        return [
            'ids' => $property->getValue(),
        ];
    }
}
