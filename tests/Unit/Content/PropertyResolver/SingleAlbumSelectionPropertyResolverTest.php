<?php

declare(strict_types=1);

namespace App\Tests\Unit\Content\PropertyResolver;

use App\Content\PropertyResolver\SingleAlbumSelectionPropertyResolver;
use PHPUnit\Framework\TestCase;
use Sulu\Content\Application\ContentResolver\Value\ResolvableResource;

class SingleAlbumSelectionPropertyResolverTest extends TestCase
{
    private SingleAlbumSelectionPropertyResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new SingleAlbumSelectionPropertyResolver();
    }

    public function testType(): void
    {
        $this->assertSame('single_album_selection', SingleAlbumSelectionPropertyResolver::getType());
    }

    public function testNullValue(): void
    {
        $contentView = $this->resolver->resolve(null, 'en');

        $this->assertNull($contentView->getContent());
        $this->assertSame(['id' => null], $contentView->getView());
    }

    public function testValidValue(): void
    {
        $contentView = $this->resolver->resolve(22, 'en');

        $this->assertSame(['id' => 22], $contentView->getView());

        $resource = $contentView->getContent();
        $this->assertInstanceOf(ResolvableResource::class, $resource);
        $this->assertSame(22, $resource->getId());
    }
}
