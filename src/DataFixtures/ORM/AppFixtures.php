<?php

namespace App\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Sulu\Bundle\ContactBundle\Entity\Account;
use Sulu\Bundle\ContactBundle\Entity\AccountAddress;
use Sulu\Bundle\ContactBundle\Entity\AccountInterface;
use Sulu\Bundle\ContactBundle\Entity\Address;
use Sulu\Bundle\ContactBundle\Entity\AddressType;
use Sulu\Bundle\ContactBundle\Entity\Contact;
use Sulu\Bundle\ContactBundle\Entity\ContactInterface;
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
use Sulu\Bundle\WebsiteBundle\Entity\Analytics;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AppFixtures extends Fixture implements OrderedFixtureInterface
{
    const LOCALE_EN = 'en';
    const LOCALE_DE = 'de';

    /**
     * @var StorageInterface
     */
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function load(ObjectManager $manager): void
    {
        $collections = $this->loadCollections($manager);

        if (!$collections['Content']) {
            // Ignore as it seems fixtures where loaded before

            return;
        }

        $this->loadContact($manager);
        $this->loadAccount($manager);
        $this->loadImages($manager, $collections['Content']);
        $this->loadAnalytics($manager);

        $manager->flush();
    }

    private function loadContact(ObjectManager $manager): ContactInterface
    {
        $contact = new Contact();
        $contact->setFirstName('Liz');
        $contact->setLastName('Adam');

        $manager->persist($contact);

        return $contact;
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

        $account->setMainEmail('office@z.com');
        $account->setMainPhone('+43 5572 93482');

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

    private function loadAnalytics(ObjectManager $manager): void
    {
        $analytics = new Analytics();
        $analytics->setTitle('Google Analytics');
        $analytics->setType('google');
        $analytics->setWebspaceKey('demo');
        $analytics->setAllDomains(true);
        $analytics->setContent('UA-46229871-6');

        $manager->persist($analytics);
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

        /** @var CollectionType|null $collectionType */
        $collectionType = $manager->getRepository(CollectionType::class)->find(1);

        if (!$collectionType) {
            throw new \RuntimeException('CollectionType "1" not found. Maybe sulu fixtures missing?');
        }

        $collection->setType($collectionType);
        $collection->setKey($key);
        $meta = new CollectionMeta();
        $meta->setLocale(self::LOCALE_EN);
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
            ->setLocale(self::LOCALE_EN)
            ->setFileVersion($fileVersion);

        $fileVersion->addMeta($fileVersionMeta)
            ->setDefaultMeta($fileVersionMeta);

        $manager->persist($fileVersionMeta);
        $manager->persist($fileVersion);
        $manager->persist($media);

        return $media;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return PHP_INT_MAX;
    }
}
