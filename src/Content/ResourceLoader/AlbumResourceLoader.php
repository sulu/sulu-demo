<?php

declare(strict_types=1);

namespace App\Content\ResourceLoader;

use App\Entity\Album;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Content\Application\ResourceLoader\Loader\ResourceLoaderInterface;

class AlbumResourceLoader implements ResourceLoaderInterface
{
    public const RESOURCE_LOADER_KEY = 'album';

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param array<int|string> $ids
     * @param mixed[] $params
     *
     * @return array<int|string, Album>
     */
    public function load(array $ids, ?string $locale, array $params = []): array
    {
        $albums = $this->entityManager->getRepository(Album::class)->findBy(['id' => $ids]);

        $mappedResult = [];
        foreach ($albums as $album) {
            $mappedResult[$album->getId()] = $album;
        }

        return $mappedResult;
    }

    public static function getKey(): string
    {
        return self::RESOURCE_LOADER_KEY;
    }
}
