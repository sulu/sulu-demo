<?php

namespace App\DataFixtures\ORM;

use App\Entity\Album;
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

class AppFixture extends Fixture implements OrderedFixtureInterface
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
        if ($this->fixturesAlreadyLoaded($manager)) {
            return;
        }

        $collections = $this->loadCollections($manager);
        $images = $this->loadImages($manager, $collections['Content Images']);
        $this->loadContacts($manager);
        $this->loadAccounts($manager);
        $this->loadAlbums($manager, $images);
        $this->loadAnalytics($manager);

        $manager->flush();
    }

    private function fixturesAlreadyLoaded(ObjectManager $manager): bool
    {
        $firstExistingAlbum = $manager->getRepository(Album::class)->findOneBy([]);

        return null !== $firstExistingAlbum;
    }

    /**
     * @return CollectionInterface[]
     */
    private function loadCollections(ObjectManager $manager): array
    {
        $collections = [];

        $collections['Content Images'] = $this->createCollection(
            $manager,
            ['title' => 'Content Images']
        );

        return $collections;
    }

    /**
     * @return MediaInterface[]
     */
    private function loadImages(ObjectManager $manager, CollectionInterface $collection): array
    {
        $media = [];
        $finder = new Finder();

        foreach ($finder->files()->in(__DIR__ . '/../images') as $file) {
            $media[pathinfo($file, PATHINFO_BASENAME)] = $this->createMedia($manager, $collection, $file);
        }

        return $media;
    }

    /**
     * @return ContactInterface[]
     */
    private function loadContacts(ObjectManager $manager): array
    {
        $contacts = [];

        $contacts[] = $this->createContact(
            $manager,
            ['firstName' => 'Liz', 'lastName' => 'Adam']
        );

        return $contacts;
    }

    /**
     * @return AccountInterface[]
     */
    private function loadAccounts(ObjectManager $manager): array
    {
        $accounts = [];

        $accounts[] = $this->createAccount($manager, [
            'name' => 'Z Music GmbH',
            'corporation' => 'Soundmanagement Corporation',
            'email' => 'office@z.com',
            'phone' => '+43 5572 93482',
            'address' => [
                'title' => 'Office Dornbirn',
                'street' => 'Gütlestraße',
                'number' => '7a',
                'zip' => '6850',
                'city' => 'Dornbirn',
                'countryCode' => 'AT',
            ],
        ]);

        return $accounts;
    }

    /**
     * @param MediaInterface[] $images
     *
     * @return Album[]
     */
    private function loadAlbums(ObjectManager $manager, array $images): array
    {
        $albums = [];

        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'Vikings',
                'image' => 'vikings.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Night Falls', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Lost High', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Energy', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Let\'s Hurt Tonight', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Other Side', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Come Around', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'I Remember You', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Worthless Words', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Cheating Death', 'interpreter' => 'Civil Literature'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'Civilwar',
                'image' => 'civilwar.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Clearview', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'The Game', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'My Children', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Homesick', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'We\'re right', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Apologize', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Home again', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'My Story', 'interpreter' => 'Civil Literature'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'collapse',
                'image' => 'collapse.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Shadows', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'My Champions', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'The Other Side', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'War', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Back in Time', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Other Side', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Hunt for Glory', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Devotion', 'interpreter' => 'Civil Literature'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => '#no more',
                'image' => 'no-more.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Turning Back', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Don\'t be Denied', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Carry On', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Peace', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Europe is Lost', 'interpreter' => 'Civil Literature'],
                    ['type' => 'track', 'title' => 'Break Through', 'interpreter' => 'Civil Literature'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'Wildfire',
                'image' => 'forest.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'I won\'t go Back', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Count the Years', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Day after Day', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'A beautiful Girl', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Walk On', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Celebrate Life', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Promised Land', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Alive in us', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Dance you Life', 'interpreter' => 'Coyoos'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'Cross the River',
                'image' => 'river.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Shelter from the Storm', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Sunrise', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Landscape', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Hotel in Chicago', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'The Horse in the small city', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Campfire in Murainen', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'A huge Drink', 'interpreter' => 'Coyoos'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'Gold Digger',
                'image' => 'gold.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Gold Digger', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'First Autumn', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Under Valleys', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Concquer the Giants', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Wild World', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Go to San Francisco', 'interpreter' => 'Coyoos'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'The Wolves',
                'image' => 'wolves.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Tears come down', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'The Wolves', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Come Home', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Don\'t Worry Baby', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Mercy on the Road', 'interpreter' => 'Coyoos'],
                    ['type' => 'track', 'title' => 'Battle in the Nature', 'interpreter' => 'Coyoos'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'Way',
                'image' => 'way.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Way Home', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Walk', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Come Together', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Living the Dream', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Mine', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Never to late', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Freedom', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Live - Follow', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Goal', 'interpreter' => 'Marshall Plan'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'let the light be',
                'image' => 'letthelightbe.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Cry', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Light', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Back to the Roots', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Needless', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Brighter', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Light the Sun', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Let the light be', 'interpreter' => 'Marshall Plan'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'Variety',
                'image' => 'variety.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Think Different', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Maybe', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Endless chance', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Again again', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Right Now	', 'interpreter' => 'Marshall Plan'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'Path',
                'image' => 'path.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Down the Way', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Step One', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Everything', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Ticket', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Learn again', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'No more valleys', 'interpreter' => 'Marshall Plan'],
                    ['type' => 'track', 'title' => 'Wonder', 'interpreter' => 'Marshall Plan'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'Joy',
                'image' => 'vw.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Joy', 'interpreter' => 'The Bagpipes'],
                    ['type' => 'track', 'title' => 'Find Me', 'interpreter' => 'The Bagpipes'],
                    ['type' => 'track', 'title' => 'Take it Back', 'interpreter' => 'The Bagpipes'],
                    ['type' => 'track', 'title' => 'Jump to heaven', 'interpreter' => 'The Bagpipes'],
                    ['type' => 'track', 'title' => 'More than this', 'interpreter' => 'The Bagpipes'],
                    ['type' => 'track', 'title' => 'Turn Over', 'interpreter' => 'The Bagpipes'],
                    ['type' => 'track', 'title' => 'Love is in the streets', 'interpreter' => 'The Bagpipes'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'Busk',
                'image' => 'city.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Around', 'interpreter' => 'The Bagpipes'],
                    ['type' => 'track', 'title' => 'Every Giant will Fall', 'interpreter' => 'The Bagpipes'],
                    ['type' => 'track', 'title' => 'Royality', 'interpreter' => 'The Bagpipes'],
                    ['type' => 'track', 'title' => 'Conquer more', 'interpreter' => 'The Bagpipes'],
                    ['type' => 'track', 'title' => 'Live alive', 'interpreter' => 'The Bagpipes'],
                    ['type' => 'track', 'title' => 'Battle Cry', 'interpreter' => 'The Bagpipes'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'Bonfire',
                'image' => 'bonfire.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Celebration', 'interpreter' => 'The Bagpipes'],
                    ['type' => 'track', 'title' => 'Catch a Glimpse', 'interpreter' => 'The Bagpipes'],
                    ['type' => 'track', 'title' => 'Love your Neighbour', 'interpreter' => 'The Bagpipes'],
                    ['type' => 'track', 'title' => 'Invest in me', 'interpreter' => 'The Bagpipes'],
                    ['type' => 'track', 'title' => 'Movement', 'interpreter' => 'The Bagpipes'],
                    ['type' => 'track', 'title' => 'Bagpipe Revival', 'interpreter' => 'The Bagpipes'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'Scottlang Call\'s',
                'image' => 'isle.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Scottland Call\'s', 'interpreter' => 'The Bagpipes'],
                    ['type' => 'track', 'title' => 'Scottland Call\'s live', 'interpreter' => 'The Bagpipes'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'Rebel',
                'image' => 'rebel.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Get It', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'Fly', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'Stick up', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => '2nd chance', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'Neighbourhood', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'Warning', 'interpreter' => 'TJ Fury'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'random',
                'image' => 'random.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Dope', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'Punchline', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'Don\'t give up', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'Slippin', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'Nervous', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'No more Violence', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'Good Life', 'interpreter' => 'TJ Fury'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'down_town',
                'image' => 'down-town.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Rolling', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'Child of the Ghetto', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'Complete', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'This is my life', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'My mom', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'Barbershop', 'interpreter' => 'TJ Fury'],
                ],
            ]
        );
        $albums[] = $this->createAlbum(
            $manager,
            $images,
            [
                'title' => 'Railling',
                'image' => 'train.jpg',
                'tracklist' => [
                    ['type' => 'track', 'title' => 'Railling', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'Friday Night', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'Gucci', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'Fresh Today', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'No Nobody', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'There it is', 'interpreter' => 'TJ Fury'],
                    ['type' => 'track', 'title' => 'Fight for your Aim', 'interpreter' => 'TJ Fury'],
                ],
            ]
        );

        return $albums;
    }

    /**
     * @return Analytics[]
     */
    private function loadAnalytics(ObjectManager $manager): array
    {
        $analytics = [];

        $analytics[] = $this->createAnalytics(
            $manager,
            ['title' => 'Google Analytics', 'type' => 'google', 'content' => 'UA-46229871-6', 'allDomains' => true]
        );

        return $analytics;
    }

    /**
     * @param mixed[] $data
     */
    private function createCollection(ObjectManager $manager, array $data): CollectionInterface
    {
        $collection = new Collection();

        /** @var CollectionType|null $collectionType */
        $collectionType = $manager->getRepository(CollectionType::class)->find(1);

        if (!$collectionType) {
            throw new \RuntimeException('CollectionType "1" not found. Have you loaded the Sulu fixtures?');
        }

        $collection->setType($collectionType);

        $meta = new CollectionMeta();
        $meta->setLocale(self::LOCALE_EN);
        $meta->setTitle($data['title']);
        $meta->setCollection($collection);

        $collection->addMeta($meta);
        $collection->setDefaultMeta($meta);

        $manager->persist($collection);
        $manager->persist($meta);

        return $collection;
    }

    private function createMedia(
        ObjectManager $manager,
        CollectionInterface $collection,
        SplFileInfo $fileInfo
    ): MediaInterface {
        $fileName = $fileInfo->getBasename();
        $title = $fileInfo->getFilename();
        $uploadedFile = new UploadedFile($fileInfo->getPathname(), $fileName);

        $storageOptions = $this->storage->save(
            $uploadedFile->getPathname(),
            $fileName
        );

        $mediaType = $manager->getRepository(MediaType::class)->find(2);
        if (!$mediaType instanceof MediaType) {
            throw new \RuntimeException('MediaType "2" not found. Have you loaded the Sulu fixtures?');
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
     * @param mixed[] $data
     */
    private function createContact(ObjectManager $manager, array $data): ContactInterface
    {
        $contact = new Contact();
        $contact->setFirstName($data['firstName']);
        $contact->setLastName($data['lastName']);

        $manager->persist($contact);

        return $contact;
    }

    /**
     * @param mixed[] $data
     */
    private function createAccount(ObjectManager $manager, array $data): AccountInterface
    {
        $account = new Account();
        $account->setName($data['name']);
        $account->setCorporation($data['corporation']);
        $account->setMainEmail($data['email']);
        $account->setMainPhone($data['phone']);

        /** @var AddressType|null $addressType */
        $addressType = $manager->getRepository(AddressType::class)->find(1);
        if (!$addressType) {
            throw new \RuntimeException('AddressType "1" not found. Have you loaded the Sulu fixtures?');
        }

        $address = new Address();
        $address->setTitle($data['address']['title']);
        $address->setStreet($data['address']['street']);
        $address->setNumber($data['address']['number']);
        $address->setZip($data['address']['zip']);
        $address->setCity($data['address']['city']);
        $address->setCountryCode($data['address']['countryCode']);
        $address->setAddressType($addressType);

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
     * @param MediaInterface[] $images
     * @param mixed[] $data
     */
    private function createAlbum(ObjectManager $manager, array $images, array $data): Album
    {
        if (!($media = $images[$data['image']] ?? null)) {
            throw new \RuntimeException(sprintf('Image "%s" could not be found!', $data['image']));
        }

        $album = new Album();
        $album->setTitle($data['title']);
        $album->setImage($media);
        $album->setTracklist($data['tracklist']);

        $manager->persist($album);

        return $album;
    }

    /**
     * @param mixed[] $data
     */
    private function createAnalytics(ObjectManager $manager, array $data): Analytics
    {
        $analytics = new Analytics();
        $analytics->setTitle($data['title']);
        $analytics->setType($data['type']);
        $analytics->setContent($data['content']);
        $analytics->setAllDomains($data['allDomains']);
        $analytics->setWebspaceKey('demo');

        $manager->persist($analytics);

        return $analytics;
    }

    /**
     * @return array<string, mixed>
     */

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return PHP_INT_MAX;
    }
}
