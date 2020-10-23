<?php

declare(strict_types=1);

namespace App\Tests\Functional\Traits;

use App\Entity\Album;
use Doctrine\ORM\EntityManagerInterface;

trait CreateAlbumTrait
{
    /**
     * @param mixed[] $data
     */
    public function createAlbum(array $data): Album
    {
        $entityManager = static::getEntityManager();

        $album = new Album();
        $album->setTitle($data['title'] ?? null);
        $album->setImage($data['image'] ?? null);
        $album->setTracklist($data['tracklist'] ?? []);

        $entityManager->persist($album);
        $entityManager->flush();

        return $album;
    }

    abstract protected static function getEntityManager(): EntityManagerInterface;
}
