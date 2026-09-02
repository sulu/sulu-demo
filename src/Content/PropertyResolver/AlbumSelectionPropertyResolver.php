<?php

declare(strict_types=1);

namespace App\Content\PropertyResolver;

use App\Content\ResourceLoader\AlbumResourceLoader;
use App\Entity\Album;
use Sulu\Content\Application\ContentResolver\Value\ContentView;
use Sulu\Content\Application\PropertyResolver\Resolver\PropertyResolverInterface;

class AlbumSelectionPropertyResolver implements PropertyResolverInterface
{
    /**
     * @param array{resourceLoader?: string, properties?: array<string, mixed>|null} $params
     */
    public function resolve(mixed $data, string $locale, array $params = []): ContentView
    {
        if (!\is_array($data) || 0 === \count($data) || !\array_is_list($data)) {
            return ContentView::create([], ['ids' => [], ...$params]);
        }

        /** @var string $resourceLoaderKey */
        $resourceLoaderKey = $params['resourceLoader'] ?? AlbumResourceLoader::getKey();

        /** @var array<int|string> $ids */
        $ids = $data;

        return ContentView::createResolvablesWithReferences(
            ids: $ids,
            resourceLoaderKey: $resourceLoaderKey,
            resourceKey: Album::RESOURCE_KEY,
            view: ['ids' => $ids],
            metadata: ['properties' => $params['properties'] ?? null],
        );
    }

    public static function getType(): string
    {
        return 'album_selection';
    }
}
