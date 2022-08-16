<?php

declare(strict_types=1);

namespace App\Tests\Unit\Content;

use App\Content\Type\AlbumSelection;
use App\Entity\Album;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sulu\Component\Content\Compat\PropertyInterface;

class AlbumSelectionTest extends TestCase
{
    use ProphecyTrait;

    private AlbumSelection $albumSelection;

    /**
     * @var ObjectProphecy<ObjectRepository<Album>>
     */
    private ObjectProphecy $albumRepository;

    protected function setUp(): void
    {
        $this->albumRepository = $this->prophesize(ObjectRepository::class); // @phpstan-ignore-line
        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $entityManager->getRepository(Album::class)->willReturn($this->albumRepository->reveal());

        $this->albumSelection = new AlbumSelection($entityManager->reveal());
    }

    public function testNullValue(): void
    {
        $property = $this->prophesize(PropertyInterface::class);
        $property->getValue()->willReturn(null);

        $this->assertSame([], $this->albumSelection->getContentData($property->reveal()));
        $this->assertSame(['ids' => null], $this->albumSelection->getViewData($property->reveal()));
    }

    public function testEmptyArrayValue(): void
    {
        $property = $this->prophesize(PropertyInterface::class);
        $property->getValue()->willReturn([]);

        $this->assertSame([], $this->albumSelection->getContentData($property->reveal()));
        $this->assertSame(['ids' => []], $this->albumSelection->getViewData($property->reveal()));
    }

    public function testValidValue(): void
    {
        $property = $this->prophesize(PropertyInterface::class);
        $property->getValue()->willReturn([45, 22]);

        $album22 = $this->prophesize(Album::class);
        $album22->getId()->willReturn(22);

        $album45 = $this->prophesize(Album::class);
        $album45->getId()->willReturn(45);

        $this->albumRepository->findBy(['id' => [45, 22]])->willReturn([
            $album22->reveal(),
            $album45->reveal(),
        ]);

        $this->assertSame(
            [
                $album45->reveal(),
                $album22->reveal(),
            ],
            $this->albumSelection->getContentData($property->reveal()),
        );
        $this->assertSame(['ids' => [45, 22]], $this->albumSelection->getViewData($property->reveal()));
    }
}
