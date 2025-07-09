<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="app_event")
 */
class Event
{
    public const RESOURCE_KEY = 'events';
    public const FORM_KEY = 'event_details';
    public const LIST_KEY = 'events';
    public const SECURITY_CONTEXT = 'sulu.events.events';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\ManyToOne(targetEntity=MediaInterface::class)
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?MediaInterface $image = null;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $startDate;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $endDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name ?? '';
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getImage(): ?MediaInterface
    {
        return $this->image;
    }

    public function setImage(?MediaInterface $image): void
    {
        $this->image = $image;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeImmutable $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): void
    {
        $this->endDate = $endDate;
    }
}
