<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Entity\Album;
use Sulu\Bundle\ActivityBundle\Domain\Event\DomainEvent;

class AlbumModifiedEvent extends DomainEvent
{
    private Album $album;

    /**
     * @var mixed[]
     */
    private array $payload;

    /**
     * @param mixed[] $payload
     */
    public function __construct(Album $album, array $payload)
    {
        parent::__construct();

        $this->album = $album;
        $this->payload = $payload;
    }

    public function getAlbum(): Album
    {
        return $this->album;
    }

    public function getEventPayload(): ?array
    {
        return $this->payload;
    }

    public function getEventType(): string
    {
        return 'modified';
    }

    public function getResourceKey(): string
    {
        return Album::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return (string) $this->album->getId();
    }

    public function getResourceTitle(): ?string
    {
        return $this->album->getTitle();
    }

    public function getResourceSecurityContext(): ?string
    {
        return Album::SECURITY_CONTEXT;
    }
}
