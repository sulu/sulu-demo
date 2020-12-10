<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Album;
use PHPUnit\Framework\TestCase;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;

class AlbumTest extends TestCase
{
    /**
     * @var Album
     */
    private $album;

    protected function setUp(): void
    {
        $this->album = new Album();
    }

    public function testTitle(): void
    {
        $this->assertSame('', $this->album->getTitle());

        $this->album->setTitle('Cross the River');

        $this->assertSame('Cross the River', $this->album->getTitle());
    }

    public function testImage(): void
    {
        $image = $this->prophesize(MediaInterface::class);
        $image->getId()->willReturn(1234);

        $this->assertNull($this->album->getImage());
        $this->assertNull($this->album->getImageData());

        $this->album->setImage($image->reveal());

        $this->assertSame($image->reveal(), $this->album->getImage());
        $this->assertSame(['id' => 1234], $this->album->getImageData());
    }

    public function testTracklist(): void
    {
        $this->assertSame([], $this->album->getTracklist());

        $this->album->setTracklist([
            ['type' => 'track', 'title' => 'Shelter from the Storm', 'interpreter' => 'Coyoos'],
            ['type' => 'track', 'title' => 'Sunrise', 'interpreter' => 'Coyoos'],
        ]);

        $this->assertSame(
            [
                ['type' => 'track', 'title' => 'Shelter from the Storm', 'interpreter' => 'Coyoos'],
                ['type' => 'track', 'title' => 'Sunrise', 'interpreter' => 'Coyoos'],
            ],
            $this->album->getTracklist()
        );
    }
}
