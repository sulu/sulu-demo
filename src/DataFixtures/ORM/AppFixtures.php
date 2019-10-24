<?php

namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sulu\Bundle\ContactBundle\DataFixtures\ORM\LoadDefaultTypes;
use Sulu\Bundle\ContactBundle\Entity\Account;
use Sulu\Bundle\ContactBundle\Entity\AccountAddress;
use Sulu\Bundle\ContactBundle\Entity\AccountInterface;
use Sulu\Bundle\ContactBundle\Entity\Address;
use Sulu\Bundle\ContactBundle\Entity\AddressType;
use Sulu\Bundle\MediaBundle\DataFixtures\ORM\LoadCollectionTypes;
use Sulu\Bundle\MediaBundle\DataFixtures\ORM\LoadMediaTypes;
use Sulu\Bundle\MediaBundle\Entity\Collection;
use Sulu\Bundle\MediaBundle\Entity\CollectionInterface;
use Sulu\Bundle\MediaBundle\Entity\CollectionMeta;
use Sulu\Bundle\MediaBundle\Entity\CollectionType;
use Sulu\Bundle\MediaBundle\Entity\File;
use Sulu\Bundle\MediaBundle\Entity\FileVersion;
use Sulu\Bundle\MediaBundle\Entity\FileVersionMeta;
use Sulu\Bundle\MediaBundle\Entity\Media;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaType;
use Sulu\Bundle\MediaBundle\Media\Storage\StorageInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AppFixtures extends Fixture implements OrderedFixtureInterface
{
    const LOCALE = 'en';

    /**
     * @var StorageInterface
     */
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function load(ObjectManager $manager)
    {
        $collections = $this->loadCollections($manager);

        if (!$collections['Content']) {
            // Ignore as it seems fixtures where loaded before

            return;
        }

        $this->loadAccount($manager);
        $this->loadImages($manager, $collections['Content']);

        $manager->flush();
    }

    private function loadAccount(ObjectManager $manager): AccountInterface
    {
        $repository = $manager->getRepository(AddressType::class);

        /** @var AddressType|null $addressType */
        $addressType = $repository->find(1);
        if (!$addressType) {
            $addressType = new AddressType();
            $addressType->setId(1);
            $addressType->setName('Work');

            $manager->persist($addressType);
        }

        $address = new Address();
        $address->setTitle('Office Dornbirn');
        $address->setStreet('Gütlestraße');
        $address->setNumber('7a');
        $address->setZip('6850');
        $address->setCity('Dornbirn');
        $address->setCountryCode('AT');
        $address->setAddressType($addressType);
        $address->setPrimaryAddress(true);
        $address->setDeliveryAddress(true);
        $address->setBillingAddress(true);

        $account = new Account();
        $account->setName('Z');
        $account->setCorporation('GmbH');

        $accountAddress = new AccountAddress();
        $accountAddress->setAddress($address);
        $accountAddress->setAccount($account);
        $accountAddress->setMain(true);

        $manager->persist($address);
        $manager->persist($account);
        $manager->persist($accountAddress);

        return $account;
    }

    /**
     * @return array{Content: CollectionInterface|null}
     */
    private function loadCollections(ObjectManager $manager): array
    {
        $collections = [
            'Content' => $this->createCollection($manager, 'Content'),
        ];

        return $collections;
    }

    /**
     * @return MediaInterface[]
     */
    private function loadImages(ObjectManager $manager, CollectionInterface $collection): array
    {
        $finder = new Finder();
        $media = [];

        foreach ($finder->files()->in(__DIR__ . '/../images') as $file) {
            $media[pathinfo($file, PATHINFO_BASENAME)] = $this->createMedia($manager, $file, $collection);
        }

        return $media;
    }

    private function createCollection(ObjectManager $manager, string $title): ?CollectionInterface
    {
        $collectionRepository = $manager->getRepository(CollectionInterface::class);
        $key = 'app.' . str_replace(' ', '_', mb_strtolower($title));

        $collection = $collectionRepository->findOneBy(['key' => $key]);

        if ($collection) {
            return null;
        }

        $collection = new Collection();
        $collectionType = $manager->getRepository(CollectionType::class)->find(1);

        if (!$collectionType) {
            throw new \RuntimeException('CollectionType "1" not found. Maybe sulu fixtures missing?');
        }

        $collection->setType($collectionType);
        $collection->setKey($key);
        $meta = new CollectionMeta();
        $meta->setLocale(self::LOCALE);
        $meta->setTitle($title);
        $meta->setCollection($collection);

        $collection->addMeta($meta);
        $collection->setDefaultMeta($meta);

        $manager->persist($collection);
        $manager->persist($meta);

        return $collection;
    }

    private function createMedia(
        ObjectManager $manager,
        SplFileInfo $file,
        CollectionInterface $collection
    ): MediaInterface {
        $fileName = $file->getBasename();
        $title = $file->getFilename();
        $uploadedFile = new UploadedFile($file->getPathname(), $fileName);

        $storageOptions = $this->storage->save(
            $uploadedFile->getPathname(),
            $fileName
        );

        $mediaType = $manager->getRepository(MediaType::class)->find(2);

        if (!$mediaType instanceof MediaType) {
            throw new \RuntimeException('MediaType "2" not found. Maybe sulu fixtures missing?');
        }

        $media = new Media();

        $file = new File();
        $file->setVersion(1)
            ->setMedia($media);

        $media->addFile($file)
            ->setType($mediaType)
            ->setCollection($collection);

        $fileVersion = new FileVersion();
        $fileVersion->setVersion($file->getVersion())
            ->setSize($uploadedFile->getSize())
            ->setName($fileName)
            ->setStorageOptions($storageOptions)
            ->setMimeType($uploadedFile->getMimeType() ?: 'image/jpeg')
            ->setFile($file);

        $file->addFileVersion($fileVersion);

        $fileVersionMeta = new FileVersionMeta();
        $fileVersionMeta->setTitle($title)
            ->setDescription('')
            ->setLocale(self::LOCALE)
            ->setFileVersion($fileVersion);

        $fileVersion->addMeta($fileVersionMeta)
            ->setDefaultMeta($fileVersionMeta);

        $manager->persist($fileVersionMeta);
        $manager->persist($fileVersion);
        $manager->persist($media);

        return $media;
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return PHP_INT_MAX;
    }
}
