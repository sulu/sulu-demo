<?php

namespace App\DataFixtures\Document;

use App\DataFixtures\ORM\AppFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use RuntimeException;
use Sulu\Bundle\DocumentManagerBundle\DataFixtures\DocumentFixtureInterface;
use Sulu\Bundle\MediaBundle\Entity\Media;
use Sulu\Bundle\PageBundle\Document\BasePageDocument;
use Sulu\Bundle\PageBundle\Document\HomeDocument;
use Sulu\Bundle\PageBundle\Document\PageDocument;
use Sulu\Component\Content\Document\RedirectType;
use Sulu\Component\Content\Document\WorkflowStage;
use Sulu\Component\DocumentManager\DocumentManager;
use Sulu\Component\DocumentManager\Exception\DocumentManagerException;
use Sulu\Component\DocumentManager\Exception\MetadataNotFoundException;
use Sulu\Component\PHPCR\PathCleanup;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class DocumentFixture implements DocumentFixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var PathCleanup
     */
    private $pathCleanup;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function getOrder()
    {
        return 10;
    }

    /**
     * @param DocumentManager $documentManager
     * @throws DocumentManagerException
     * @throws MetadataNotFoundException
     */
    public function load(DocumentManager $documentManager)
    {
        $pages = $this->loadPages($documentManager);
        $this->loadHomepage($documentManager);
        $this->updatePages($documentManager);

        $documentManager->flush();
    }

    /**
     * @param DocumentManager $documentManager
     * @return BasePageDocument[]
     * @throws MetadataNotFoundException
     */
    private function loadPages(DocumentManager $documentManager): array
    {
        $pageDataList = [
            [
                'title' => 'Artists',
                'url' => '/artists',
                'subtitle' => 'Discover our roster of talented musicians.',
                'header_image' => [
                    'id' => $this->getMediaId('artists.jpg'),
                ],
                'navigationContexts' => ['main', 'footer'],
                'structureType' => 'overview',
            ],
            [
                'title' => 'Civil Literature',
                'url' => '/artists/civil-literature',
                'parent_path' => '/cmf/demo/contents/artists',
                'subtitle' => 'Lorem ipsum dolor sit amet.',
                'header_image' => [
                    'id' => $this->getMediaId('civil-literature.jpg'),
                ],
                'structureType' => 'default',
            ],
            [
                'title' => 'Coyoos',
                'url' => '/artists/coyoos',
                'parent_path' => '/cmf/demo/contents/artists',
                'subtitle' => 'Lorem ipsum dolor sit amet.',
                'header_image' => [
                    'id' => $this->getMediaId('coyoos.jpg'),
                ],
                'structureType' => 'default',
            ],
            [
                'title' => 'Marshall Plan',
                'url' => '/artists/marshall-plan',
                'parent_path' => '/cmf/demo/contents/artists',
                'subtitle' => 'Lorem ipsum dolor sit amet.',
                'header_image' => [
                    'id' => $this->getMediaId('marshall.jpg'),
                ],
                'structureType' => 'default',
            ],
            [
                'title' => 'The Bagpipes',
                'url' => '/artists/the-bagpipes',
                'parent_path' => '/cmf/demo/contents/artists',
                'subtitle' => 'Lorem ipsum dolor sit amet.',
                'header_image' => [
                    'id' => $this->getMediaId('dudelsack.jpg'),
                ],
                'structureType' => 'default',
            ],
            [
                'title' => 'TJ Fury',
                'url' => '/artists/tj-fury',
                'parent_path' => '/cmf/demo/contents/artists',
                'subtitle' => 'Lorem ipsum dolor sit amet.',
                'header_image' => [
                    'id' => $this->getMediaId('tj-fury.jpg'),
                ],
                'structureType' => 'default',
            ],
            [
                'title' => 'Blog',
                'url' => '/blog',
                'subtitle' => 'We like to give you insights into what we do.',
                'header_image' => [
                    'id' => $this->getMediaId('blog.jpg'),
                ],
                'navigationContexts' => ['main'],
                'structureType' => 'default',
            ],
            [
                'title' => 'About Us',
                'url' => '/about',
                'subtitle' => 'We work hard, but we love what we do.',
                'header_image' => [
                    'id' => $this->getMediaId('about.png'),
                ],
                'navigationContexts' => ['main', 'footer'],
                'structureType' => 'default',
            ],
        ];

        $pages = [];

        foreach ($pageDataList as $pageData) {
            $pages[$pageData['title']] = $this->createPage($documentManager, $pageData);
        }

        return $pages;
    }

    /**
     * @param DocumentManager $documentManager
     * @throws DocumentManagerException
     */
    private function loadHomepage(DocumentManager $documentManager): void {
        /** @var HomeDocument $homeDocument */
        $homeDocument = $documentManager->find('/cmf/demo/contents', AppFixtures::LOCALE);

        /** @var BasePageDocument $teaserDocument */
        $teaserDocument = $documentManager->find('/cmf/demo/contents/artists/the-bagpipes', AppFixtures::LOCALE);

        $homeDocument->getStructure()->bind(
            [
                'title' => $homeDocument->getTitle(),
                'url' => '/',
                'teaser' => $teaserDocument->getUuid(),
            ]
        );

        $documentManager->persist($homeDocument, AppFixtures::LOCALE);
        $documentManager->publish($homeDocument, AppFixtures::LOCALE);
    }

    /**
     * @param DocumentManager $documentManager
     * @throws DocumentManagerException
     */
    private function updatePages(DocumentManager $documentManager): void {
        /** @var BasePageDocument $artistsDocument */
        $artistsDocument = $documentManager->find('/cmf/demo/contents/artists', AppFixtures::LOCALE);

        $data = $artistsDocument->getStructure()->toArray();

        $data['elements'] = [
            'sortBy' => 'published',
            'sortMethod' => 'desc',
            'dataSource' => $artistsDocument->getUuid(),
        ];

        $artistsDocument->getStructure()->bind($data);

        $documentManager->persist($artistsDocument, AppFixtures::LOCALE);
        $documentManager->publish($artistsDocument, AppFixtures::LOCALE);
    }

    /**
     * @param DocumentManager $documentManager
     * @param mixed[] $data
     * @return BasePageDocument
     * @throws MetadataNotFoundException
     */
    private function createPage(DocumentManager $documentManager, array $data): BasePageDocument
    {
        if (!isset($data['url'])) {
            $url = $this->getPathCleanup()->cleanup('/' . $data['title']);
            if (isset($data['parent_path'])) {
                $url = mb_substr($data['parent_path'], mb_strlen('/cmf/demo/contents')) . $url;
            }

            $data['url'] = $url;
        }

        $extensionData = [
            'seo' => $data['seo'] ?? [],
            'excerpt' => $data['excerpt'] ?? [],
        ];

        unset($data['excerpt']);
        unset($data['seo']);

        /** @var PageDocument $pageDocument */
        $pageDocument = $documentManager->create('page');
        $pageDocument->setNavigationContexts($data['navigationContexts'] ?? []);
        $pageDocument->setLocale(AppFixtures::LOCALE);
        $pageDocument->setTitle($data['title']);
        $pageDocument->setResourceSegment($data['url']);
        $pageDocument->setStructureType($data['structureType'] ?? 'default');
        $pageDocument->setWorkflowStage(WorkflowStage::PUBLISHED);
        $pageDocument->getStructure()->bind($data);
        $pageDocument->setAuthor(1);
        $pageDocument->setExtensionsData($extensionData);

        if (isset($data['redirect'])) {
            $pageDocument->setRedirectType(RedirectType::EXTERNAL);
            $pageDocument->setRedirectExternal($data['redirect']);
        }

        $documentManager->persist(
            $pageDocument,
            AppFixtures::LOCALE,
            ['parent_path' => $data['parent_path'] ?? '/cmf/demo/contents']
        );

        // Set dataSource to current page after persist as uuid is before not available
        if (isset($data['pages']['dataSource']) && '__CURRENT__' === $data['pages']['dataSource']) {
            $pageDocument->getStructure()->bind([
                'pages' => array_merge(
                    $data['pages'],
                    [
                        'dataSource' => $pageDocument->getUuid(),
                    ]
                ),
            ]);

            $documentManager->persist(
                $pageDocument,
                AppFixtures::LOCALE,
                ['parent_path' => $data['parent_path'] ?? '/cmf/demo/contents']
            );
        }

        $documentManager->publish($pageDocument, AppFixtures::LOCALE);

        return $pageDocument;
    }

    private function getPathCleanup(): PathCleanup
    {
        if (null === $this->pathCleanup) {
            $this->pathCleanup = $this->container->get('sulu.content.path_cleaner');
        }

        return $this->pathCleanup;
    }

    private function getEntityManager(): EntityManagerInterface
    {
        if (null === $this->entityManager) {
            $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
        }

        return $this->entityManager;
    }

    private function getMediaId(string $name): int
    {
        try {
            $id = $this->getEntityManager()->createQueryBuilder()
                ->from(Media::class, 'media')
                ->select('media.id')
                ->innerJoin('media.files', 'file')
                ->innerJoin('file.fileVersions', 'fileVersion')
                ->where('fileVersion.name = :name')
                ->setMaxResults(1)
                ->setParameter('name', $name)
                ->getQuery()->getSingleScalarResult();

            return (int) $id;
        } catch (NonUniqueResultException $e) {
            throw new RuntimeException(sprintf('Too many images with the name "%s" found.', $name), 0, $e);
        }
    }
}
