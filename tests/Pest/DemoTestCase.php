<?php

namespace App\Tests\Pest;

use App\DataFixtures\Document\DocumentFixture;
use App\DataFixtures\ORM\AppFixture;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Sulu\Bundle\ContactBundle\DataFixtures\ORM\LoadDefaultTypes;
use Sulu\Bundle\MediaBundle\DataFixtures\ORM\LoadCollectionTypes;
use Sulu\Bundle\MediaBundle\DataFixtures\ORM\LoadMediaTypes;
use Sulu\Bundle\MediaBundle\Media\Storage\StorageInterface;
use Sulu\Bundle\TestBundle\Testing\WebsiteTestCase;
use Sulu\Component\DocumentManager\DocumentManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class DemoTestCase extends WebsiteTestCase
{
    /**
     * @var KernelBrowser
     */
    private static $client;

    public static function setUpBeforeClass(): void
    {
        static::$client = static::createWebsiteClient();
        static::purgeDatabase();
        static::initPhpcr();

        // in the following section we just load all fixtures of our website
        // which we will use in our pest test cases

        // create app fixtures
        $entityManager = static::getEntityManager();
        /** @var StorageInterface $mediaStorage */
        $mediaStorage = static::getContainer()->get(StorageInterface::class);
        $referenceRepository = new ReferenceRepository($entityManager);

        // load core fixtures
        $contactTypes = new LoadDefaultTypes();
        $contactTypes->setReferenceRepository($referenceRepository);
        $contactTypes->load($entityManager);
        $collectionTypes = new LoadCollectionTypes();
        $collectionTypes->setReferenceRepository($referenceRepository);
        $collectionTypes->load($entityManager);
        $mediaTypes = new LoadMediaTypes();
        $mediaTypes->setReferenceRepository($referenceRepository);
        $mediaTypes->load($entityManager);
        $appFixture = new AppFixture($mediaStorage);
        $appFixture->setReferenceRepository($referenceRepository);
        $appFixture->load($entityManager);

        // create document fixtures
        /** @var DocumentManagerInterface $documentManager */
        $documentManager = static::getContainer()->get('sulu_document_manager.document_manager');
        /** @var DocumentFixture $documentFixture */
        $documentFixture = static::getContainer()->get(DocumentFixture::class);
        $documentFixture->load($documentManager);
    }

    public static function getClient(): KernelBrowser
    {
        return static::$client;
    }
}
