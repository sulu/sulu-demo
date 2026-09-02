<?php

declare(strict_types=1);

namespace App\Tests\Unit\Content\ResourceLoader;

use App\Content\ResourceLoader\AlbumResourceLoader;
use App\Entity\Album;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class AlbumResourceLoaderTest extends TestCase
{
    use ProphecyTrait;

    private AlbumResourceLoader $loader;

    /**
     * @var ObjectProphecy<EntityRepository<Album>>
     */
    private ObjectProphecy $albumRepository;

    protected function setUp(): void
    {
        $this->albumRepository = $this->prophesize(EntityRepository::class); // @phpstan-ignore-line
        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $entityManager->getRepository(Album::class)->willReturn($this->albumRepository->reveal());

        $this->loader = new AlbumResourceLoader($entityManager->reveal());
    }

    public function testKey(): void
    {
        $this->assertSame('album', AlbumResourceLoader::getKey());
    }

    public function testLoadIndexesResultById(): void
    {
        $album22 = $this->prophesize(Album::class);
        $album22->getId()->willReturn(22);

        $album45 = $this->prophesize(Album::class);
        $album45->getId()->willReturn(45);

        // the repository returns whatever order it likes, the loader has to index by id
        $this->albumRepository->findBy(['id' => [45, 22]])
            ->willReturn([$album22->reveal(), $album45->reveal()]);

        $result = $this->loader->load([45, 22], 'en');

        $this->assertSame([22, 45], \array_keys($result));
        $this->assertSame($album22->reveal(), $result[22]);
        $this->assertSame($album45->reveal(), $result[45]);
    }

    public function testLoadWithoutResults(): void
    {
        $this->albumRepository->findBy(['id' => [1]])->willReturn([]);

        $this->assertSame([], $this->loader->load([1], 'en'));
    }
}
