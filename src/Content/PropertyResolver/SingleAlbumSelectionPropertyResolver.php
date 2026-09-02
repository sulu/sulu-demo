<?php

declare(strict_types=1);

namespace App\Content\PropertyResolver;

use App\Content\ResourceLoader\AlbumResourceLoader;
use App\Entity\Album;
use Sulu\Content\Application\ContentResolver\Value\ContentView;
use Sulu\Content\Application\PropertyResolver\Resolver\PropertyResolverInterface;

class SingleAlbumSelectionPropertyResolver implements PropertyResolverInterface
{
    /**
     * @param array{resourceLoader?: string, properties?: array<string, mixed>|null} $params
     */
    public function resolve(mixed $data, string $locale, array $params = []): ContentView
    {
        if (!\is_numeric($data) && !\is_string($data)) {
            return ContentView::create(null, ['id' => null, ...$params]);
        }

        /** @var string $resourceLoaderKey */
        $resourceLoaderKey = $params['resourceLoader'] ?? AlbumResourceLoader::getKey();

        return ContentView::createResolvableWithReferences(
            id: $data,
            resourceLoaderKey: $resourceLoaderKey,
            resourceKey: Album::RESOURCE_KEY,
            view: ['id' => $data],
            metadata: ['properties' => $params['properties'] ?? null],
        );
    }

    public static function getType(): string
    {
        return 'single_album_selection';
    }
}
