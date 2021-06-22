<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Entity\Album;
use Sulu\Bundle\ActivityBundle\Domain\Event\DomainEvent;

class AlbumRemovedEvent extends DomainEvent
{
    private int $id;
    private string $title;

    public function __construct(int $id, string $title)
    {
        parent::__construct();

        $this->id = $id;
        $this->title = $title;
    }

    public function getEventType(): string
    {
        return 'removed';
    }

    public function getResourceKey(): string
    {
        return Album::RESOURCE_KEY;
    }

    public function getResourceId(): string
    {
        return (string) $this->id;
    }

    public function getResourceTitle(): ?string
    {
        return $this->title;
    }

    public function getResourceSecurityContext(): ?string
    {
        return Album::SECURITY_CONTEXT;
    }
}
