<?php

declare(strict_types=1);

namespace App\Tests\Unit\Content\PropertyResolver;

use App\Content\PropertyResolver\AlbumSelectionPropertyResolver;
use PHPUnit\Framework\TestCase;
use Sulu\Content\Application\ContentResolver\Value\ResolvableResource;

class AlbumSelectionPropertyResolverTest extends TestCase
{
    private AlbumSelectionPropertyResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new AlbumSelectionPropertyResolver();
    }

    public function testType(): void
    {
        $this->assertSame('album_selection', AlbumSelectionPropertyResolver::getType());
    }

    public function testNullValue(): void
    {
        $contentView = $this->resolver->resolve(null, 'en');

        $this->assertSame([], $contentView->getContent());
        $this->assertSame(['ids' => []], $contentView->getView());
    }

    public function testEmptyArrayValue(): void
    {
        $contentView = $this->resolver->resolve([], 'en');

        $this->assertSame([], $contentView->getContent());
        $this->assertSame(['ids' => []], $contentView->getView());
    }

    public function testValidValue(): void
    {
        $contentView = $this->resolver->resolve([45, 22], 'en');

        $this->assertSame(['ids' => [45, 22]], $contentView->getView());

        $content = $contentView->getContent();
        $this->assertIsArray($content);
        $this->assertCount(2, $content);

        $ids = [];
        foreach ($content as $resource) {
            $this->assertInstanceOf(ResolvableResource::class, $resource);
            $ids[] = $resource->getId();
        }

        $this->assertSame([45, 22], $ids);
    }
}
