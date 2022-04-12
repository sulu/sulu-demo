<?php

declare(strict_types=1);

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Trash;

use App\Admin\AlbumAdmin;
use App\Entity\Album;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Bundle\TrashBundle\Application\DoctrineRestoreHelper\DoctrineRestoreHelperInterface;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfiguration;
use Sulu\Bundle\TrashBundle\Application\RestoreConfigurationProvider\RestoreConfigurationProviderInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\RestoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashItemHandler\StoreTrashItemHandlerInterface;
use Sulu\Bundle\TrashBundle\Domain\Model\TrashItemInterface;
use Sulu\Bundle\TrashBundle\Domain\Repository\TrashItemRepositoryInterface;
use Sulu\Component\Security\Authentication\UserInterface;

final class AlbumTrashItemHandler implements
    StoreTrashItemHandlerInterface,
    RestoreTrashItemHandlerInterface,
    RestoreConfigurationProviderInterface
{
    private TrashItemRepositoryInterface $trashItemRepository;
    private EntityManagerInterface $entityManager;
    private DoctrineRestoreHelperInterface $doctrineRestoreHelper;

    public function __construct(
        TrashItemRepositoryInterface $trashItemRepository,
        EntityManagerInterface $entityManager,
        DoctrineRestoreHelperInterface $doctrineRestoreHelper
    ) {
        $this->trashItemRepository = $trashItemRepository;
        $this->doctrineRestoreHelper = $doctrineRestoreHelper;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Album $album
     */
    public function store(object $album, array $options = []): TrashItemInterface
    {
        $image = $album->getImage();
        $creator = $album->getCreator();

        $data = [
            'title' => $album->getTitle(),
            'tracklist' => $album->getTracklist(),
            'imageId' => $image ? $image->getId() : null,
            'created' => $album->getCreated()->format('c'),
            'creatorId' => $creator ? $creator->getId() : null,
        ];

        return $this->trashItemRepository->create(
            Album::RESOURCE_KEY,
            (string) $album->getId(),
            $album->getTitle(),
            $data,
            null,
            $options,
            Album::SECURITY_CONTEXT,
            null,
            null
        );
    }

    public function restore(TrashItemInterface $trashItem, array $restoreFormData = []): object
    {
        /**
         * @var array{
         *     title: string,
         *     imageId: int|null,
         *     created: string,
         *     creatorId: int|null,
         *     tracklist: array<array{
         *         type: string,
         *         title: string|null,
         *         interpreter: string|null,
         *     }>,
         * } $data
         */
        $data = $trashItem->getRestoreData();

        $album = new Album();
        $album->setTitle($data['title']);
        $album->setTracklist($data['tracklist']);
        $album->setCreated(new \DateTime($data['created']));

        if ($data['imageId']) {
            $album->setImage($this->entityManager->find(MediaInterface::class, $data['imageId']));
        }

        if ($data['creatorId']) {
            $album->setCreator($this->entityManager->find(UserInterface::class, $data['creatorId']));
        }

        $this->doctrineRestoreHelper->persistAndFlushWithId($album, (int) $trashItem->getResourceId());

        return $album;
    }

    public function getConfiguration(): RestoreConfiguration
    {
        return new RestoreConfiguration(null, AlbumAdmin::EDIT_FORM_VIEW, ['id' => 'id']);
    }

    public static function getResourceKey(): string
    {
        return Album::RESOURCE_KEY;
    }
}
