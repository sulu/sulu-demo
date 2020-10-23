<?php

declare(strict_types=1);

namespace App\Tests\Unit\Content;

use App\Content\Type\SingleAlbumSelection;
use App\Entity\Album;
use App\Repository\AlbumRepository;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Sulu\Component\Content\Compat\PropertyInterface;

class SingleAlbumSelectionTest extends TestCase
{
    /**
     * @var SingleAlbumSelection
     */
    private $singleAlbumSelection;

    /**
     * @var ObjectProphecy<AlbumRepository>
     */
    private $albumRepository;

    public function setUp(): void
    {
        $this->albumRepository = $this->prophesize(AlbumRepository::class);
        $this->singleAlbumSelection = new SingleAlbumSelection($this->albumRepository->reveal());
    }

    public function testNullValue(): void
    {
        $property = $this->prophesize(PropertyInterface::class);
        $property->getValue()->willReturn(null);

        $this->assertNull($this->singleAlbumSelection->getContentData($property->reveal()));
        $this->assertSame(['id' => null], $this->singleAlbumSelection->getViewData($property->reveal()));
    }

    public function testValidValue(): void
    {
        $property = $this->prophesize(PropertyInterface::class);
        $property->getValue()->willReturn(45);

        $album45 = $this->prophesize(Album::class);

        $this->albumRepository->find(45)->willReturn($album45->reveal());

        $this->assertSame($album45->reveal(), $this->singleAlbumSelection->getContentData($property->reveal()));
        $this->assertSame(['id' => 45], $this->singleAlbumSelection->getViewData($property->reveal()));
    }
}
