<?php

declare(strict_types=1);

namespace App\Content\Type;

use App\Entity\Album;
use App\Repository\AlbumRepository;
use Sulu\Component\Content\Compat\PropertyInterface;
use Sulu\Component\Content\SimpleContentType;

class SingleAlbumSelection extends SimpleContentType
{
    protected AlbumRepository $albumRepository;

    public function __construct(AlbumRepository $albumRepository)
    {
        $this->albumRepository = $albumRepository;

        parent::__construct('single_album_selection', null);
    }

    public function getContentData(PropertyInterface $property): ?Album
    {
        $id = $property->getValue();

        if (null === $id) {
            return $this->defaultValue;
        }

        return $this->albumRepository->find($id);
    }

    /**
     * @return array<string, array<int>>
     */
    public function getViewData(PropertyInterface $property): array
    {
        return [
            'id' => $property->getValue(),
        ];
    }
}
