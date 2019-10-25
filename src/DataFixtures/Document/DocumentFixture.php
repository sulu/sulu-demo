<?php

namespace App\DataFixtures\Document;

use App\DataFixtures\ORM\AppFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use RuntimeException;
use Sulu\Bundle\DocumentManagerBundle\DataFixtures\DocumentFixtureInterface;
use Sulu\Bundle\MediaBundle\Entity\Media;
use Sulu\Bundle\PageBundle\Document\BasePageDocument;
use Sulu\Bundle\PageBundle\Document\HomeDocument;
use Sulu\Bundle\PageBundle\Document\PageDocument;
use Sulu\Bundle\SnippetBundle\Document\SnippetDocument;
use Sulu\Bundle\SnippetBundle\Snippet\DefaultSnippetManagerInterface;
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

    /** @var DefaultSnippetManagerInterface */
    private $defaultSnippetManager;

    public function getOrder()
    {
        return 10;
    }

    /**
     * @throws DocumentManagerException
     * @throws MetadataNotFoundException
     * @throws Exception
     */
    public function load(DocumentManager $documentManager)
    {
        $this->loadPages($documentManager);
        $this->loadContactSnippet($documentManager);
        $this->loadHomepage($documentManager);
        $this->updatePages($documentManager);

        $documentManager->flush();
    }

    /**
     * @throws MetadataNotFoundException
     */
    private function loadPages(DocumentManager $documentManager): void
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
                'blocks' => [
                    [
                        'type' => 'heading',
                        'heading' => 'Civil Literature',
                    ],
                    [
                        'type' => 'text',
                        'text' => '<p>After releasing their record album in 2014, Marshall Plan spent more than one year bringing their passion for the rock music to the big concerthalls and arenas all over Great Britain – in 2015 worldwide. In this time the rockband started to grow together and wrote there second album.</p>',
                    ],
                    [
                        'type' => 'text',
                        'text' => '<p>In 2010 Liam, the frontman of Civil Literature founded the band with his brother Garry, a well known guitar player and songwriter in Manchester. In 2011 Marc followed. His talent in playing the bass fullfilled the intense music vibes. Together they had one big dream: rocking the stage of the Royal Albert Hall. In 2016 they are as close as never before. With their new album, reaching records in Europe.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'I have always been a passionated songwriter. My greatest fullfillment is to touch people with my messages and to encourage them to live their dreams.',
                        'quoteReference' => 'Liam Hendrickson',
                    ],
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
                'blocks' => [
                    [
                        'type' => 'heading',
                        'heading' => 'Coyoos',
                    ],
                    [
                        'type' => 'text',
                        'text' => '<p>After releasing their record album in 2012, Coyoos spent more than one year bringing their passion for the folk music to the big concerthalls and arenas all over the United States – in 2015 worldwide. In this time the folkband started to grow together and wrote there second album.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'Travelling and singing is all I need. Discover new places and meeting inspiring people are the experiences you never forget. It is my source of creativity and inspiration.',
                        'quoteReference' => 'Jack',
                    ],
                    [
                        'type' => 'text',
                        'text' => '<p>In 2014 Jack started his music career in San Diego, California. His talent in playing the guitar made him famous in a short period of time. His one big dream: touring around the United States. In 2016 he is as close as never before – with his new album, reaching records in the US.</p>',
                    ],
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
                'blocks' => [
                    [
                        'type' => 'heading',
                        'heading' => 'Marshall Plan',
                    ],
                    [
                        'type' => 'text',
                        'text' => '<p>Releasing their record album in 2003, Marshall Plan spent more than one year bringing their passion for the rock music to the big concerthalls and arenas all over Great Britain – in 2015 worldwide. In this time the rockband started to grow together and wrote there second album.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'We love making music together and to inspire people around us with our songs. We come from a small town in the UK. It still feels unreal, to be known from Asia to the States.',
                        'quoteReference' => 'Jason Mcconkey',
                    ],
                    [
                        'type' => 'text',
                        'text' => '<p>In 2003 Alex, the frontman of Marshall Plan founded the band with his best friends Bronson, Albert and Ray. Those well known guitar player and songwriter in Liverpool. In 2007 Jason followed. His talent in playing the bass fullfilled the intense music vibes. Together they had one big dream: rocking the stage in front of the Times Square in New York. In 2016 they are as close as never before. With their new album, reaching records all over the world.</p>',
                    ],
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
                'blocks' => [
                    [
                        'type' => 'heading',
                        'heading' => 'The Bagpipes',
                    ],
                    [
                        'type' => 'text',
                        'text' => '<p>In the beginning they focused on traditional and contemporary music with innovative flair, before they concetrated on there classic bagpipe music. Short after releasing their record album in 1998, The Bagpipes spent more than one year bringing their passion for the folk music to the big concerthalls and arenas all over Scottland – in 2015 worldwide. In this time the folkband started to grow together and wrote there fourth album. Soon they become the Scottish folkband of the year 2003, 2005 and 2014.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'We started our career in the streets of Glasgow. There is nothing more real and authentic then playing street music. People like you, or they don\'t and pass you. You immediately get the reaction.',
                        'quoteReference' => 'Steve Avril',
                    ],
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
                'blocks' => [
                    [
                        'type' => 'heading',
                        'heading' => 'TJ Fury',
                    ],
                    [
                        'type' => 'text',
                        'text' => '<p>In the beginning he focused on combinations between music and Hip Hop. Today he concentrated on stong powerful Lines in the scene of Hip Hop. After releasing his record album in 2011, TJ Fury spent more than one year bringing his passion for Hip Hop to the downtown clubs all around the big cities in the United States – in 2015 worldwide. In this time TJ Fury started to record their new album. Soon they got several awards.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'We love making music. Check out our new tracks.',
                        'quoteReference' => 'TJ Fury',
                    ],
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
                'blocks' => [
                    [
                        'type' => 'heading',
                        'heading' => 'Coming soon!',
                    ],
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
                'blocks' => [
                    [
                        'type' => 'heading',
                        'heading' => 'International Talents',
                    ],
                    [
                        'type' => 'text',
                        'text' => '<h3>International Talents was founded 1998</h3><p>From Great Britain all over the world International Talents become one of the worldwide leading music brand. With over 20 years of recorded music history, our passion for artistry in music continues today. We love to inspire young talents with all of our knowledge and experience.&nbsp;The desire to speak into the heart and soul of the listeners is what fueled the creative and strategic efforts of the label.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'The whole experience of 20 years and a lot of knowledge come together in International Talents. We love what we do, and no day is like the one before.',
                        'quoteReference' => 'Jonathan Benett',
                    ],
                    [
                        'type' => 'text',
                        'text' => '<p>But all this, is not possible, without a Team behind. This Team is attendable around the clock. They prepare your Event, help for your exhibition or Product Presentation. Everyone of them is an urban legend in what they do. Success is no accident. It grows with an great Team.</p>',
                    ],
                ],
                'navigationContexts' => ['main', 'footer'],
                'structureType' => 'default',
            ],
        ];

        foreach ($pageDataList as $pageData) {
            $this->createPage($documentManager, $pageData);
        }
    }

    /**
     * @throws DocumentManagerException
     */
    private function loadHomepage(DocumentManager $documentManager): void
    {
        /** @var HomeDocument $homeDocument */
        $homeDocument = $documentManager->find('/cmf/demo/contents', AppFixtures::LOCALE);

        /** @var BasePageDocument $aboutDocument */
        $aboutDocument = $documentManager->find('/cmf/demo/contents/about-us', AppFixtures::LOCALE);

        /** @var BasePageDocument $headerTeaserDocument */
        $headerTeaserDocument = $documentManager->find('/cmf/demo/contents/artists/coyoos', AppFixtures::LOCALE);

        $homeDocument->getStructure()->bind(
            [
                'title' => $homeDocument->getTitle(),
                'url' => '/',
                'teaser' => $headerTeaserDocument->getUuid(),
                'blocks' => [
                    [
                        'type' => 'heading',
                        'heading' => 'Our Label',
                    ],
                    [
                        'type' => 'text',
                        'text' => '<h3>International Talents was founded 1998</h3><p>From Great Britain all over the world International Talents become one of the worldwide leading music brand. With over 20 years of recorded music history, our passion for artistry in music continues today. We love to inspire young talents with all of our knowledge and experience.&nbsp;The desire to speak into the heart and soul of the listeners is what fueled the creative and strategic efforts of the label.</p>',
                    ],
                    [
                        'type' => 'link',
                        'page' => $aboutDocument->getUuid(),
                        'text' => 'READ MORE',
                    ],
                ],
            ]
        );

        $documentManager->persist($homeDocument, AppFixtures::LOCALE);
        $documentManager->publish($homeDocument, AppFixtures::LOCALE);
    }

    /**
     * @throws Exception
     */
    private function loadContactSnippet(DocumentManager $documentManager): void
    {
        $data = [
            'title' => 'Z',
            'contact' => [
                'id' => 1,
            ],
        ];

        $uuid = $this->createSnippet($documentManager, 'contact', $data)->getUuid();

        $this->getDefaultSnippetManager()->save('demo', 'contact', $uuid, AppFixtures::LOCALE);
    }

    /**
     * @param mixed[] $data
     *
     * @throws MetadataNotFoundException
     */
    private function createSnippet(DocumentManager $documentManager, string $structureType, array $data): SnippetDocument
    {
        /** @var SnippetDocument $snippetDocument */
        $snippetDocument = $documentManager->create('snippet');
        $snippetDocument->setLocale(AppFixtures::LOCALE);
        $snippetDocument->setTitle($data['title']);
        $snippetDocument->setStructureType($structureType);
        $snippetDocument->setWorkflowStage(WorkflowStage::PUBLISHED);
        $snippetDocument->getStructure()->bind($data);

        $documentManager->persist($snippetDocument, AppFixtures::LOCALE, ['parent_path' => '/cmf/snippets']);
        $documentManager->publish($snippetDocument, AppFixtures::LOCALE);

        return $snippetDocument;
    }

    /**
     * @throws DocumentManagerException
     */
    private function updatePages(DocumentManager $documentManager): void
    {
        /** @var BasePageDocument $artistsDocument */
        $artistsDocument = $documentManager->find('/cmf/demo/contents/artists', AppFixtures::LOCALE);

        $data = $artistsDocument->getStructure()->toArray();

        $data['elements'] = [
            'sortBy' => 'published',
            'sortMethod' => 'asc',
            'dataSource' => $artistsDocument->getUuid(),
        ];

        $artistsDocument->getStructure()->bind($data);

        $documentManager->persist($artistsDocument, AppFixtures::LOCALE);
        $documentManager->publish($artistsDocument, AppFixtures::LOCALE);
    }

    /**
     * @param mixed[] $data
     *
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

    private function getDefaultSnippetManager(): DefaultSnippetManagerInterface
    {
        if (null === $this->defaultSnippetManager) {
            $this->defaultSnippetManager = $this->container->get('sulu_snippet.default_snippet.manager');
        }

        return $this->defaultSnippetManager;
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
