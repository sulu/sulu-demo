<?php

namespace App\DataFixtures\ORM;

use App\Entity\Album;
use App\Repository\AlbumRepository;
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

    /**
     * @var array<string, MediaInterface>
     */
    private $medias = [];

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

        $this->loadImages($manager, $collections['Content']);
        $this->loadContact($manager);
        $this->loadAccount($manager);
        $this->loadAlbums($manager);
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

    private function loadAlbums(ObjectManager $manager): void
    {
        // Civil Literature
        $this->createAlbum($manager, 'Vikings', 'vikings.jpg', [
            $this->createTrack('Night Falls', 'Civil Literature'),
            $this->createTrack('Lost High', 'Civil Literature'),
            $this->createTrack('Energy', 'Civil Literature'),
            $this->createTrack('Let\'s Hurt Tonight', 'Civil Literature'),
            $this->createTrack('Other Side', 'Civil Literature'),
            $this->createTrack('Come Around', 'Civil Literature'),
            $this->createTrack('I Remember You', 'Civil Literature'),
            $this->createTrack('Worthless Words', 'Civil Literature'),
            $this->createTrack('Cheating Death', 'Civil Literature'),
        ]);
        $this->createAlbum($manager, 'Civilwar', 'civilwar.jpg', [
            $this->createTrack('Clearview', 'Civil Literature'),
            $this->createTrack('The Game', 'Civil Literature'),
            $this->createTrack('My Children', 'Civil Literature'),
            $this->createTrack('Homesick', 'Civil Literature'),
            $this->createTrack('We\'re right', 'Civil Literature'),
            $this->createTrack('Apologize', 'Civil Literature'),
            $this->createTrack('Home again', 'Civil Literature'),
            $this->createTrack('My Story', 'Civil Literature'),
        ]);
        $this->createAlbum($manager, 'collapse', 'collapse.jpg', [
            $this->createTrack('Shadows', 'Civil Literature'),
            $this->createTrack('My Champions', 'Civil Literature'),
            $this->createTrack('The Other Side', 'Civil Literature'),
            $this->createTrack('War', 'Civil Literature'),
            $this->createTrack('Back in Time', 'Civil Literature'),
            $this->createTrack('Other Side', 'Civil Literature'),
            $this->createTrack('Hunt for Glory', 'Civil Literature'),
            $this->createTrack('Devotion', 'Civil Literature'),
        ]);
        $this->createAlbum($manager, '#no more', 'no-more.jpg', [
            $this->createTrack('Turning Back', 'Civil Literature'),
            $this->createTrack('Don\'t be Denied', 'Civil Literature'),
            $this->createTrack('Carry On', 'Civil Literature'),
            $this->createTrack('Peace', 'Civil Literature'),
            $this->createTrack('Europe is Lost', 'Civil Literature'),
            $this->createTrack('Break Through', 'Civil Literature'),
        ]);

        // Coyoos
        $this->createAlbum($manager, 'Wildfire', 'forest.jpg', [
            $this->createTrack('I won\'t go Back', 'Coyoos'),
            $this->createTrack('Count the Years', 'Coyoos'),
            $this->createTrack('Day after Day', 'Coyoos'),
            $this->createTrack('A beautiful Girl', 'Coyoos'),
            $this->createTrack('Walk On', 'Coyoos'),
            $this->createTrack('Celebrate Life', 'Coyoos'),
            $this->createTrack('Promised Land', 'Coyoos'),
            $this->createTrack('Alive in us', 'Coyoos'),
            $this->createTrack('Dance you Life', 'Coyoos'),
        ]);
        $this->createAlbum($manager, 'Cross the River', 'river.jpg', [
            $this->createTrack('Shelter from the Storm', 'Coyoos'),
            $this->createTrack('Sunrise', 'Coyoos'),
            $this->createTrack('Landscape', 'Coyoos'),
            $this->createTrack('Hotel in Chicago', 'Coyoos'),
            $this->createTrack('The Horse in the small city', 'Coyoos'),
            $this->createTrack('Campfire in Murainen', 'Coyoos'),
            $this->createTrack('A huge Drink', 'Coyoos'),
        ]);
        $this->createAlbum($manager, 'Gold Digger', 'gold.jpg', [
            $this->createTrack('Gold Digger', 'Coyoos'),
            $this->createTrack('First Autumn', 'Coyoos'),
            $this->createTrack('Under Valleys', 'Coyoos'),
            $this->createTrack('Concquer the Giants', 'Coyoos'),
            $this->createTrack('Wild World', 'Coyoos'),
            $this->createTrack('Go to San Francisco', 'Coyoos'),
        ]);
        $this->createAlbum($manager, 'The Wolves', 'wolves.jpg', [
            $this->createTrack('Tears come down', 'Coyoos'),
            $this->createTrack('The Wolves', 'Coyoos'),
            $this->createTrack('Come Home', 'Coyoos'),
            $this->createTrack('Don\'t Worry Baby', 'Coyoos'),
            $this->createTrack('Mercy on the Road', 'Coyoos'),
            $this->createTrack('Battle in the Nature', 'Coyoos'),
        ]);

        // Marshall Plan
        $this->createAlbum($manager, 'Way', 'way.jpg', [
            $this->createTrack('Way Home', 'Marshall Plan'),
            $this->createTrack('Walk', 'Marshall Plan'),
            $this->createTrack('Come Together', 'Marshall Plan'),
            $this->createTrack('Living the Dream', 'Marshall Plan'),
            $this->createTrack('Mine', 'Marshall Plan'),
            $this->createTrack('Never to late', 'Marshall Plan'),
            $this->createTrack('Freedom', 'Marshall Plan'),
            $this->createTrack('Live - Follow', 'Marshall Plan'),
            $this->createTrack('Goal', 'Marshall Plan'),
        ]);
        $this->createAlbum($manager, 'let the light be', 'letthelightbe.jpg', [
            $this->createTrack('Cry', 'Marshall Plan'),
            $this->createTrack('Light', 'Marshall Plan'),
            $this->createTrack('Back to the Roots', 'Marshall Plan'),
            $this->createTrack('Needless', 'Marshall Plan'),
            $this->createTrack('Brighter', 'Marshall Plan'),
            $this->createTrack('Light the Sun', 'Marshall Plan'),
            $this->createTrack('Let the light be', 'Marshall Plan'),
        ]);
        $this->createAlbum($manager, 'Variety', 'variety.jpg', [
            $this->createTrack('Think Different', 'Marshall Plan'),
            $this->createTrack('Maybe', 'Marshall Plan'),
            $this->createTrack('Endless chance', 'Marshall Plan'),
            $this->createTrack('Again again', 'Marshall Plan'),
            $this->createTrack('Right Now	', 'Marshall Plan'),
        ]);
        $this->createAlbum($manager, 'Path', 'path.jpg', [
            $this->createTrack('Down the Way', 'Marshall Plan'),
            $this->createTrack('Step One', 'Marshall Plan'),
            $this->createTrack('Everything', 'Marshall Plan'),
            $this->createTrack('Ticket', 'Marshall Plan'),
            $this->createTrack('Learn again', 'Marshall Plan'),
            $this->createTrack('No more valleys', 'Marshall Plan'),
            $this->createTrack('Wonder', 'Marshall Plan'),
        ]);

        // The Bagpipes
        $this->createAlbum($manager, 'Joy', 'vw.jpg', [
            $this->createTrack('Joy', 'The Bagpipes'),
            $this->createTrack('Find Me', 'The Bagpipes'),
            $this->createTrack('Take it Back', 'The Bagpipes'),
            $this->createTrack('Jump to heaven', 'The Bagpipes'),
            $this->createTrack('More than this', 'The Bagpipes'),
            $this->createTrack('Turn Over', 'The Bagpipes'),
            $this->createTrack('Love is in the streets', 'The Bagpipes'),
        ]);
        $this->createAlbum($manager, 'Busk', 'city.jpg', [
            $this->createTrack('Around', 'The Bagpipes'),
            $this->createTrack('Every Giant will Fall', 'The Bagpipes'),
            $this->createTrack('Royality', 'The Bagpipes'),
            $this->createTrack('Conquer more', 'The Bagpipes'),
            $this->createTrack('Live alive', 'The Bagpipes'),
            $this->createTrack('Battle Cry', 'The Bagpipes'),
        ]);
        $this->createAlbum($manager, 'Bonfire', 'bonfire.jpg', [
            $this->createTrack('Celebration', 'The Bagpipes'),
            $this->createTrack('Catch a Glimpse', 'The Bagpipes'),
            $this->createTrack('Love your Neighbour', 'The Bagpipes'),
            $this->createTrack('Invest in me', 'The Bagpipes'),
            $this->createTrack('Movement', 'The Bagpipes'),
            $this->createTrack('Bagpipe Revival', 'The Bagpipes'),
        ]);
        $this->createAlbum($manager, 'Scottlang Call\'s', 'isle.jpg', [
            $this->createTrack('Scottland Call\'s', 'The Bagpipes'),
            $this->createTrack('Scottland Call\'s live', 'The Bagpipes'),
        ]);

        // TJ Fury
        $this->createAlbum($manager, 'Rebel', 'rebel.jpg', [
            $this->createTrack('Get It', 'TJ Fury'),
            $this->createTrack('Fly', 'TJ Fury'),
            $this->createTrack('Stick up', 'TJ Fury'),
            $this->createTrack('2nd chance', 'TJ Fury'),
            $this->createTrack('Neighbourhood', 'TJ Fury'),
            $this->createTrack('Warning', 'TJ Fury'),
        ]);
        $this->createAlbum($manager, 'random', 'random.jpg', [
            $this->createTrack('Dope', 'TJ Fury'),
            $this->createTrack('Punchline', 'TJ Fury'),
            $this->createTrack('Don\'t give up', 'TJ Fury'),
            $this->createTrack('Slippin', 'TJ Fury'),
            $this->createTrack('Nervous', 'TJ Fury'),
            $this->createTrack('No more Violence', 'TJ Fury'),
            $this->createTrack('Good Life', 'TJ Fury'),
        ]);
        $this->createAlbum($manager, 'down_town', 'down-town.jpg', [
            $this->createTrack('Rolling', 'TJ Fury'),
            $this->createTrack('Child of the Ghetto', 'TJ Fury'),
            $this->createTrack('Complete', 'TJ Fury'),
            $this->createTrack('This is my life', 'TJ Fury'),
            $this->createTrack('My mom', 'TJ Fury'),
            $this->createTrack('Barbershop', 'TJ Fury'),
        ]);
        $this->createAlbum($manager, 'Railling', 'train.jpg', [
            $this->createTrack('Railling', 'TJ Fury'),
            $this->createTrack('Friday Night', 'TJ Fury'),
            $this->createTrack('Gucci', 'TJ Fury'),
            $this->createTrack('Fresh Today', 'TJ Fury'),
            $this->createTrack('No Nobody', 'TJ Fury'),
            $this->createTrack('There it is', 'TJ Fury'),
            $this->createTrack('Fight for your Aim', 'TJ Fury'),
        ]);
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

    /**
     * @return array<string, mixed>
     */
    private function createTrack(string $title, string $interpreter): array
    {
        return [
            'type' => 'track',
            'title' => $title,
            'interpreter' => $interpreter,
        ];
    }

    /**
     * @param mixed[] $tracklist
     */
    private function createAlbum(ObjectManager $manager, string $title, string $image, array $tracklist): Album
    {
        /** @var AlbumRepository $repository */
        $repository = $manager->getRepository(Album::class);

        if (!($media = $this->medias[$image] ?? null)) {
            throw new \RuntimeException(sprintf('Image "%s" could not be found!', $image));
        }

        $album = $repository->create();
        $album->setTitle($title);
        $album->setImage($media);
        $album->setTracklist($tracklist);

        $manager->persist($album);

        return $album;
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

        $this->medias[$fileName] = $media;

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
