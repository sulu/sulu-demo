<?php

namespace App\Entity;

use App\Repository\AlbumRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Component\Persistence\Model\AuditableInterface;
use Sulu\Component\Persistence\Model\AuditableTrait;

/**
 * @ORM\Entity(repositoryClass=AlbumRepository::class)
 * @ORM\Table(name="app_album")
 * @Serializer\ExclusionPolicy("all")
 */
class Album implements AuditableInterface
{
    use AuditableTrait;

    public const RESOURCE_KEY = 'albums';
    public const FORM_KEY = 'album_details';
    public const LIST_KEY = 'albums';
    public const SECURITY_CONTEXT = 'sulu.album.albums';

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
    private string $title;

    /**
     * @ORM\ManyToOne(targetEntity=MediaInterface::class)
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?MediaInterface $image = null;

    /**
     * @var mixed[]
     *
     * @ORM\Column(type="json")
     *
     * @Serializer\Expose()
     */
    private array $tracklist = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title ?? '';
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
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

    /**
     * @return mixed[]
     */
    public function getTracklist(): array
    {
        return $this->tracklist;
    }

    /**
     * @param mixed[] $tracklist
     */
    public function setTracklist(array $tracklist): void
    {
        $this->tracklist = $tracklist;
    }
}
