<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Admin;

use App\Controller\Admin\AlbumController;
use App\Entity\Album;
use App\Tests\Functional\Traits\CreateAlbumTrait;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

/**
 * @phpstan-import-type AlbumData from AlbumController
 */
class AlbumControllerTest extends SuluTestCase
{
    use CreateAlbumTrait;

    /**
     * @var KernelBrowser
     */
    private $client;

    protected function setUp(): void
    {
        $this->client = $this->createAuthenticatedClient();
        $this->purgeDatabase();
    }

    public function testCGet(): void
    {
        $album1 = $this->createAlbum([
            'title' => 'Railling',
            'tracklist' => [
                ['type' => 'track', 'title' => 'Railling', 'interpreter' => 'TJ Fury'],
                ['type' => 'track', 'title' => 'Friday Night', 'interpreter' => 'TJ Fury'],
            ],
        ]);

        $album2 = $this->createAlbum([
            'title' => 'The Wolves',
            'tracklist' => [
                ['type' => 'track', 'title' => 'Mercy on the Road', 'interpreter' => 'Coyoos'],
            ],
        ]);

        $this->client->jsonRequest('GET', '/admin/api/albums');

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        /**
         * @var array{
         *     _embedded: array{
         *         albums: array<array{
         *             id: int,
         *             title: string,
         *             changed: string,
         *             created: string,
         *             changer: string|null,
         *             creator: string|null,
         *         }>
         *     },
         *     total: int,
         * } $result
         */
        $result = \json_decode($response->getContent() ?: '', true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertSame(2, $result['total']);
        $this->assertCount(2, $result['_embedded']['albums']);
        $items = $result['_embedded']['albums'];

        $this->assertSame($album1->getId(), $items[0]['id']);
        $this->assertSame($album2->getId(), $items[1]['id']);

        $this->assertSame($album1->getTitle(), $items[0]['title']);
        $this->assertSame($album2->getTitle(), $items[1]['title']);
    }

    public function testGet(): void
    {
        $album = $this->createAlbum([
            'title' => 'Railling',
            'tracklist' => [
                ['type' => 'track', 'title' => 'Railling', 'interpreter' => 'TJ Fury'],
            ],
        ]);

        $this->client->jsonRequest('GET', '/admin/api/albums/' . $album->getId());

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);

        /**
         * @var AlbumData $result
         */
        $result = \json_decode($response->getContent() ?: '', true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertSame($album->getId(), $result['id']);
        $this->assertSame($album->getTitle(), $result['title']);
        $this->assertNull($album->getImage());
        $this->assertSame($album->getTracklist(), $result['tracklist']);
    }

    public function testGetNotExisting(): void
    {
        $this->client->jsonRequest('GET', '/admin/api/albums/9999');

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = \json_decode($response->getContent() ?: '', true);
        $this->assertHttpStatusCode(404, $response);
    }

    public function testPost(): void
    {
        $this->client->jsonRequest(
                'POST',
                '/admin/api/albums',
                [
                    'title' => 'Railling',
                    'tracklist' => [
                        ['type' => 'track', 'title' => 'Railling', 'interpreter' => 'TJ Fury'],
                    ],
                ]
            );

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);

        /**
         * @var AlbumData $result
         */
        $result = \json_decode($response->getContent() ?: '', true);
        $this->assertHttpStatusCode(201, $response);

        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);
        $this->assertSame('Railling', $result['title']);
        $this->assertNull($result['image']);
        $this->assertSame(
                [['type' => 'track', 'title' => 'Railling', 'interpreter' => 'TJ Fury']],
                $result['tracklist']
            );

        /** @var Album|null $result */
        $result = $this->getEntityManager()->getRepository(Album::class)->findOneBy(['id' => $result['id']]);
        $this->assertNotNull($result);
        $this->assertSame('Railling', $result->getTitle());
        $this->assertNull($result->getImage());
        $this->assertSame(
                [['type' => 'track', 'title' => 'Railling', 'interpreter' => 'TJ Fury']],
                $result->getTracklist()
            );
    }

    public function testPut(): void
    {
        $album = $this->createAlbum([
                'title' => 'Railling',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Railling', 'interpreter' => 'TJ Fury'],
                ],
            ]);

        $this->client->jsonRequest(
                'PUT',
                '/admin/api/albums/' . $album->getId(),
                [
                    'title' => 'The Wolves',
                    'tracklist' => [
                        ['type' => 'track', 'title' => 'Battle in the Nature', 'interpreter' => 'Coyoos'],
                        ['type' => 'track', 'title' => 'Mercy on the Road', 'interpreter' => 'Coyoos'],
                    ],
                ]
            );

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        /**
         * @var AlbumData $result
         */
        $result = \json_decode($response->getContent() ?: '', true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);
        $this->assertSame('The Wolves', $result['title']);
        $this->assertNull($result['image']);
        $this->assertSame(
                [
                    ['type' => 'track', 'title' => 'Battle in the Nature', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Mercy on the Road', 'interpreter' => 'Coyoos'],
                ],
                $result['tracklist']
            );

        /** @var Album|null $result */
        $result = $this->getEntityManager()->getRepository(Album::class)->findOneBy(['id' => $result['id']]);
        $this->assertNotNull($result);
        $this->assertSame('The Wolves', $result->getTitle());
        $this->assertNull($result->getImage());
        $this->assertSame(
                [
                    ['type' => 'track', 'title' => 'Battle in the Nature', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Mercy on the Road', 'interpreter' => 'Coyoos'],
                ],
                $result->getTracklist()
            );
    }

    public function testPutNotExisting(): void
    {
        $this->client->jsonRequest(
            'PUT',
            '/admin/api/albums/9999',
            [
                'title' => 'The Wolves',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Battle in the Nature', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Mercy on the Road', 'interpreter' => 'Coyoos'],
                ],
            ]
        );

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = \json_decode($response->getContent() ?: '', true);
        $this->assertHttpStatusCode(404, $response);
    }

    public function testDelete(): void
    {
        $album = $this->createAlbum([
                'title' => 'Railling',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Railling', 'interpreter' => 'TJ Fury'],
                ],
            ]);

        $albumId = $album->getId();
        $this->assertNotNull($albumId);

        $this->client->jsonRequest('DELETE', '/admin/api/albums/' . $albumId);

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertHttpStatusCode(204, $response);

        $this->assertNull($this->getEntityManager()->getRepository(Album::class)->findOneBy(['id' => $albumId]));
    }
}
