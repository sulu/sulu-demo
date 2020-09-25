<?php

namespace App\Repository;

use App\Entity\Album;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Album|null find($id, $lockMode = null, $lockVersion = null)
 * @method Album|null findOneBy(array $criteria, array $orderBy = null)
 * @method Album[]    findAll()
 * @method Album[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Album>
 */
class AlbumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Album::class);
    }

    public function create(): Album
    {
        return new Album();
    }

    public function save(Album $album): void
    {
        $this->getEntityManager()->persist($album);
        $this->getEntityManager()->flush();
    }

    public function remove(int $id): void
    {
        /** @var Album $album */
        $album = $this->getEntityManager()->getReference(Album::class, $id);
        $this->getEntityManager()->remove($album);
        $this->getEntityManager()->flush();
    }

    /**
     * @param int[] $ids
     *
     * @return Album[]
     */
    public function findByIds(array $ids): array
    {
        $albums = $this->findBy(['id' => $ids]);

        $idPositions = array_flip($ids);
        usort($albums, function (Album $a, Album $b) use ($idPositions) {
            return $idPositions[$a->getId()] - $idPositions[$b->getId()];
        });

        return $albums;
    }
}
