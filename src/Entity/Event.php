<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="app_event")
 * @Serializer\ExclusionPolicy("all")
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
     *
     * @Serializer\Expose()
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Serializer\Expose()
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Serializer\Expose()
     */
    private string $routePath;

    /**
     * @ORM\ManyToOne(targetEntity=MediaInterface::class)
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?MediaInterface $image = null;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     *
     * @Serializer\Expose()
     */
    private ?\DateTimeImmutable $startDate;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     *
     * @Serializer\Expose()
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

    public function getRoutePath(): string
    {
        return $this->routePath ?? '';
    }

    public function setRoutePath(string $routePath): void
    {
        $this->routePath = $routePath;
    }

    public function getImage(): ?MediaInterface
    {
        return $this->image;
    }

    /**
     * @return array<string, mixed>
     *
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("image")
     */
    public function getImageData(): ?array
    {
        if ($image = $this->getImage()) {
            return [
                'id' => $image->getId(),
            ];
        }

        return null;
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
