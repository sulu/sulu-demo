<?php

declare(strict_types=1);

namespace App\Tests\Unit\Content;

use App\Content\Type\AlbumSelection;
use App\Entity\Album;
use App\Repository\AlbumRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Sulu\Component\Content\Compat\PropertyInterface;

class AlbumSelectionTest extends TestCase
{
    /**
     * @var AlbumSelection
     */
    private $albumSelection;

    /**
     * @var ObjectProphecy<AlbumRepository>
     */
    private $albumRepository;

    public function setUp(): void
    {
        $this->albumRepository = $this->prophesize(AlbumRepository::class);
        $this->albumSelection = new AlbumSelection($this->albumRepository->reveal());
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
        $property->getValue()->willReturn([22, 45]);

        $album22 = $this->prophesize(Album::class);
        $album45 = $this->prophesize(Album::class);

        $this->albumRepository->findByIds([22, 45])->willReturn([
            $album22->reveal(),
            $album45->reveal(),
        ]);

        $this->assertSame(
            [
                $album22->reveal(),
                $album45->reveal(),
            ],
            $this->albumSelection->getContentData($property->reveal())
        );
        $this->assertSame(['ids' => [22, 45]], $this->albumSelection->getViewData($property->reveal()));
    }
}
