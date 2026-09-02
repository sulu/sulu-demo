<?php

declare(strict_types=1);

namespace App\DataFixtures\Content;

use App\DataFixtures\ORM\AppFixture;
use App\Entity\Album;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Sulu\Article\Application\Message\ApplyWorkflowTransitionArticleMessage;
use Sulu\Article\Application\Message\CreateArticleMessage;
use Sulu\Article\Application\Message\ModifyArticleMessage;
use Sulu\Article\Domain\Model\ArticleInterface;
use Sulu\Bundle\ContactBundle\Entity\Account;
use Sulu\Bundle\MediaBundle\Entity\Media;
use Sulu\Content\Domain\Model\WorkflowInterface;
use Sulu\Messenger\Infrastructure\Symfony\Messenger\FlushMiddleware\EnableFlushStamp;
use Sulu\Page\Application\Message\ApplyWorkflowTransitionPageMessage;
use Sulu\Page\Application\Message\CreatePageMessage;
use Sulu\Page\Application\Message\ModifyPageMessage;
use Sulu\Page\Domain\Model\PageInterface;
use Sulu\Page\Domain\Repository\PageRepositoryInterface;
use Sulu\Snippet\Application\Message\ApplyWorkflowTransitionSnippetMessage;
use Sulu\Snippet\Application\Message\CreateSnippetMessage;
use Sulu\Snippet\Application\Message\ModifySnippetAreaMessage;
use Sulu\Snippet\Application\Message\ModifySnippetMessage;
use Sulu\Snippet\Domain\Model\SnippetInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Uid\Uuid;

/**
 * Creates the demo pages, articles and snippets on the Sulu 3.0 content storage.
 *
 * The content is the same data the PHPCR document fixtures used, so the diff against 2.6 shows
 * what the upgrade forced and nothing else. Only the way it is written changed: instead of a
 * document manager, every resource is created in English through a create message, translated
 * into German with a modify message, and published per locale.
 */
class ContentFixture extends Fixture implements OrderedFixtureInterface
{
    private const WEBSPACE_KEY = 'demo';

    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly PageRepositoryInterface $pageRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function getOrder(): int
    {
        return \PHP_INT_MAX;
    }

    public function load(ObjectManager $manager): void
    {
        $pages = $this->loadPages();
        $articles = $this->loadArticles();

        $this->loadHomepage($pages, $articles);
        $this->loadSettingsSnippet();
    }

    /**
     * @return array<string, PageInterface> indexed by the english url
     */
    private function loadPages(): array
    {
        $pages = [];

        foreach ($this->pagesEnglish() as $data) {
            $parentUrl = $data['parentOf'] ?? null;
            unset($data['parentOf']);

            $parent = \is_string($parentUrl)
                ? $pages[$parentUrl]->getUuid()
                : $this->homepage()->getUuid();

            // the uuid is generated up front so a pages smart content can point at its own
            // subtree in the same pass, CreatePageMessage accepts a supplied uuid
            $uuid = (string) Uuid::v7();
            $data['uuid'] = $uuid;
            $data = $this->withPagesDataSource($data, $uuid);

            $pages[$this->stringValue($data, 'url')] = $this->createPage($data, $parent);
        }

        foreach ($this->pagesGerman() as $data) {
            $englishUrl = $this->stringValue($data, 'translationOf');
            unset($data['translationOf'], $data['parentOf']);

            $page = $pages[$englishUrl];
            $this->translatePage($page, $this->withPagesDataSource($data, $page->getUuid()));
        }

        return $pages;
    }

    /**
     * @return array<string, ArticleInterface> indexed by the english url
     */
    private function loadArticles(): array
    {
        $articles = [];

        foreach ($this->articlesEnglish() as $data) {
            $url = '/blog/' . $this->slugify($this->stringValue($data, 'title'));
            $data['url'] = $url;
            $articles[$url] = $this->createArticle($data);
        }

        foreach ($this->articlesGerman() as $data) {
            $englishUrl = $this->stringValue($data, 'translationOf');
            unset($data['translationOf']);
            $data['url'] = '/blog/' . $this->slugify($this->stringValue($data, 'title'));

            $this->translateArticle($articles[$englishUrl], $data);
        }

        return $articles;
    }

    /**
     * In 2.6 a pages smart content without a data source listed the children of the current
     * page. In 3.0 the data source has to be given explicitly.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function withPagesDataSource(array $data, string $uuid): array
    {
        $elements = $data['element'] ?? null;
        if (!\is_array($elements)) {
            return $data;
        }

        $result = [];
        foreach ($elements as $element) {
            if (\is_array($element) && 'pages' === ($element['type'] ?? null)) {
                $pages = $element['pages'] ?? null;
                $pages = \is_array($pages) ? $pages : [];
                $pages['dataSource'] = $uuid;
                $element['pages'] = $pages;
            }

            $result[] = $element;
        }

        $data['element'] = $result;

        return $data;
    }

    private function homepage(): PageInterface
    {
        return $this->pageRepository->getOneBy([
            'webspaceKey' => self::WEBSPACE_KEY,
            'parentId' => null,
        ]);
    }

    /**
     * @param array<string, PageInterface> $pages
     * @param array<string, ArticleInterface> $articles
     */
    private function loadHomepage(array $pages, array $articles): void
    {
        $homepage = $this->homepage();

        foreach ($this->homepageContent($pages, $articles) as $locale => $data) {
            $data['locale'] = $locale;

            // sulu:page:initialize leaves a second draft dimension content per locale behind
            // that has no route attached. Without clearing the identity map first, the modify
            // picks that one up and inserts a second "/" route, which the unique index on
            // ro_routes rejects.
            $this->entityManager->clear();

            $this->dispatch(new ModifyPageMessage(['uuid' => $homepage->getUuid()], $data));
            $this->dispatch(new ApplyWorkflowTransitionPageMessage(
                ['uuid' => $homepage->getUuid()],
                $locale,
                WorkflowInterface::WORKFLOW_TRANSITION_PUBLISH,
            ));
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createPage(array $data, string $parentUuid): PageInterface
    {
        /** @var PageInterface $page */
        $page = $this->dispatch(new CreatePageMessage(self::WEBSPACE_KEY, $parentUuid, $data));

        $this->publishPage($page, $this->stringValue($data, 'locale'));

        return $page;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function translatePage(PageInterface $page, array $data): void
    {
        $this->dispatch(new ModifyPageMessage(['uuid' => $page->getUuid()], $data));
        $this->publishPage($page, $this->stringValue($data, 'locale'));
    }

    private function publishPage(PageInterface $page, string $locale): void
    {
        $this->dispatch(new ApplyWorkflowTransitionPageMessage(
            ['uuid' => $page->getUuid()],
            $locale,
            WorkflowInterface::WORKFLOW_TRANSITION_PUBLISH,
        ));
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createArticle(array $data): ArticleInterface
    {
        /** @var ArticleInterface $article */
        $article = $this->dispatch(new CreateArticleMessage($data));

        $this->publishArticle($article, $this->stringValue($data, 'locale'));

        return $article;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function translateArticle(ArticleInterface $article, array $data): void
    {
        $this->dispatch(new ModifyArticleMessage(['uuid' => $article->getUuid()], $data));
        $this->publishArticle($article, $this->stringValue($data, 'locale'));
    }

    private function publishArticle(ArticleInterface $article, string $locale): void
    {
        $this->dispatch(new ApplyWorkflowTransitionArticleMessage(
            ['uuid' => $article->getUuid()],
            $locale,
            WorkflowInterface::WORKFLOW_TRANSITION_PUBLISH,
        ));
    }

    private function loadSettingsSnippet(): void
    {
        $accountId = $this->firstAccountId();

        /** @var SnippetInterface $snippet */
        $snippet = $this->dispatch(new CreateSnippetMessage([
            'locale' => AppFixture::LOCALE_EN,
            'template' => 'settings',
            'title' => 'Demo Settings',
            'account' => $accountId,
        ]));
        $this->publishSnippet($snippet, AppFixture::LOCALE_EN);

        $this->dispatch(new ModifySnippetMessage(['uuid' => $snippet->getUuid()], [
            'locale' => AppFixture::LOCALE_DE,
            'template' => 'settings',
            'title' => 'Einstellungen Demo',
            'account' => $accountId,
        ]));
        $this->publishSnippet($snippet, AppFixture::LOCALE_DE);

        // the footer resolves the settings through the webspace_settings area
        foreach ([AppFixture::LOCALE_EN, AppFixture::LOCALE_DE] as $locale) {
            $this->dispatch(new ModifySnippetAreaMessage([
                'webspaceKey' => self::WEBSPACE_KEY,
                'areaKey' => 'webspace_settings',
                'snippetIdentifier' => ['uuid' => $snippet->getUuid()],
                'locale' => $locale,
            ]));
        }
    }

    private function publishSnippet(SnippetInterface $snippet, string $locale): void
    {
        $this->dispatch(new ApplyWorkflowTransitionSnippetMessage(
            ['uuid' => $snippet->getUuid()],
            $locale,
            WorkflowInterface::WORKFLOW_TRANSITION_PUBLISH,
        ));
    }

    private function dispatch(object $message): mixed
    {
        $envelope = $this->messageBus->dispatch(new Envelope($message, [new EnableFlushStamp()]));

        /** @var HandledStamp|null $handled */
        $handled = $envelope->last(HandledStamp::class);

        return $handled?->getResult();
    }

    /**
     * @param array<string, mixed> $data
     */
    private function stringValue(array $data, string $key): string
    {
        $value = $data[$key] ?? null;
        \assert(\is_string($value), \sprintf('Expected fixture key "%s" to be a string.', $key));

        return $value;
    }

    private function slugify(string $value): string
    {
        $slug = \iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        $slug = \strtolower((string) $slug);
        $slug = (string) \preg_replace('/[^a-z0-9]+/', '-', $slug);

        return \trim($slug, '-');
    }

    private function mediaId(string $name): int
    {
        /** @var int|string $id */
        $id = $this->entityManager->createQueryBuilder()
            ->from(Media::class, 'media')
            ->select('media.id')
            ->innerJoin('media.files', 'file')
            ->innerJoin('file.fileVersions', 'fileVersion')
            ->where('fileVersion.name = :name')
            ->setMaxResults(1)
            ->setParameter('name', $name)
            ->getQuery()->getSingleScalarResult();

        return (int) $id;
    }

    private function albumId(string $title): int
    {
        /** @var int|string $id */
        $id = $this->entityManager->createQueryBuilder()
            ->from(Album::class, 'album')
            ->select('album.id')
            ->where('album.title = :title')
            ->setMaxResults(1)
            ->setParameter('title', $title)
            ->getQuery()->getSingleScalarResult();

        return (int) $id;
    }

    private function firstAccountId(): int
    {
        /** @var int|string $id */
        $id = $this->entityManager->createQueryBuilder()
            ->from(Account::class, 'account')
            ->select('account.id')
            ->setMaxResults(1)
            ->getQuery()->getSingleScalarResult();

        return (int) $id;
    }

    /**
     * The homepage references pages and articles, so it is filled after they exist.
     * German reuses the english page keys because loadPages() indexes by the english url.
     *
     * @param array<string, PageInterface> $pages
     * @param array<string, ArticleInterface> $articles
     *
     * @return array<string, array<string, mixed>>
     */
    private function homepageContent(array $pages, array $articles): array
    {
        $aboutPage = $pages['/about'];
        $coyoosPage = $pages['/artists/coyoos'];
        $civilLiteraturePage = $pages['/artists/civil-literature'];
        $legendArticle = $articles['/blog/legend-behind-the-mix'];

        $teasers = [
            'items' => [
                ['id' => $legendArticle->getUuid(), 'type' => 'articles'],
                ['id' => $civilLiteraturePage->getUuid(), 'type' => 'pages'],
            ],
            'presentAs' => null,
        ];

        return [
            AppFixture::LOCALE_EN => [
                'title' => 'Homepage',
                'url' => '/',
                'template' => 'homepage',
                'teaser' => $coyoosPage->getUuid(),
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'Our Label',
                        'description' => '<h3>International Talents was founded 1998</h3><p>From Great Britain all over the world International Talents become one of the worldwide leading music brand. With over 20 years of recorded music history, our passion for artistry in music continues today. We love to inspire young talents with all of our knowledge and experience.&nbsp;The desire to speak into the heart and soul of the listeners is what fueled the creative and strategic efforts of the label.</p>',
                    ],
                    [
                        'type' => 'link',
                        'link' => [
                            'href' => $aboutPage->getUuid(),
                            'provider' => 'page',
                            'locale' => 'en',
                            'target' => '_self',
                            'title' => 'READ MORE',
                        ],
                    ],
                    [
                        'type' => 'teasers',
                        'title' => 'Featured',
                        'teasers' => $teasers,
                    ],
                ],
            ],
            AppFixture::LOCALE_DE => [
                'title' => 'Startseite',
                'url' => '/',
                'template' => 'homepage',
                'teaser' => $coyoosPage->getUuid(),
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'Unser Label',
                        'description' => '<h3>International Talents wurde 1998 gegründet</h3><p>Von Großbritanien aus wuchs International Talents über die ganze Welt zu einer der weltweit führenden Musik Marken.Wie lieben es junge Talente mit all unserem Wissen und Erfahrungen zu begleiten und inspirieren. Mit über 20 Jahren an Musik Aufnahmen, unserer Leidenschaft für die Musik Künstler geht heute weiter. Der Wunsch den Höreren und Fans ins Herz zusprechen ist die Motivation für immer neue kreative Ideen und Strategien des Labels.</p>',
                    ],
                    [
                        'type' => 'link',
                        'link' => [
                            'href' => $aboutPage->getUuid(),
                            'provider' => 'page',
                            'locale' => 'en',
                            'target' => '_self',
                            'title' => 'MEHR LESEN',
                        ],
                    ],
                    [
                        'type' => 'teasers',
                        'title' => 'Featured',
                        'teasers' => $teasers,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function pagesEnglish(): array
    {
        return [
            [
                'locale' => AppFixture::LOCALE_EN,
                'title' => 'Artists',
                'url' => '/artists',
                'subtitle' => 'Discover our roster of talented musicians',
                'headerImage' => [
                    'id' => $this->mediaId('artists.jpg'),
                ],
                'navigationContexts' => ['main', 'footer'],
                'template' => 'overview',
                'element' => [
                    [
                        'type' => 'pages',
                        'pages' => [
                            'sortBy' => 'published',
                            'sortMethod' => 'asc',
                        ],
                    ],
                ],
            ], [
                'locale' => AppFixture::LOCALE_EN,
                'title' => 'Civil Literature',
                'url' => '/artists/civil-literature',
                'parentOf' => '/artists',
                'subtitle' => '',
                'headerImage' => [
                    'id' => $this->mediaId('civil-literature.jpg'),
                ],
                'excerpt' => [
                    'images' => [
                        'ids' => [$this->mediaId('civil-literature.jpg')],
                    ],
                ],
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'Civil Literature',
                        'description' => '<p>After releasing their record album in 2014, Marshall Plan spent more than one year bringing their passion for the rock music to the big concerthalls and arenas all over Great Britain – in 2015 worldwide. In this time the rockband started to grow together and wrote there second album.</p><p>In 2010 Liam, the frontman of Civil Literature founded the band with his brother Garry, a well known guitar player and songwriter in Manchester. In 2011 Marc followed. His talent in playing the bass fullfilled the intense music vibes. Together they had one big dream: rocking the stage of the Royal Albert Hall. In 2016 they are as close as never before. With their new album, reaching records in Europe.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'I have always been a passionated songwriter. My greatest fullfillment is to touch people with my messages and to encourage them to live their dreams.',
                        'quoteReference' => 'Liam Hendrickson',
                    ],
                    [
                        'type' => 'albums',
                        'albums' => [
                            $this->albumId('Vikings'),
                            $this->albumId('Civilwar'),
                            $this->albumId('collapse'),
                            $this->albumId('#no more'),
                        ],
                    ],
                ],
                'template' => 'default',
            ], [
                'locale' => AppFixture::LOCALE_EN,
                'title' => 'Coyoos',
                'url' => '/artists/coyoos',
                'parentOf' => '/artists',
                'subtitle' => '',
                'headerImage' => [
                    'id' => $this->mediaId('coyoos.jpg'),
                ],
                'excerpt' => [
                    'images' => [
                        'ids' => [$this->mediaId('coyoos.jpg')],
                    ],
                ],
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'Coyoos',
                        'description' => '<p>After releasing their record album in 2012, Coyoos spent more than one year bringing their passion for the folk music to the big concerthalls and arenas all over the United States – in 2015 worldwide. In this time the folkband started to grow together and wrote there second album.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'Travelling and singing is all I need. Discover new places and meeting inspiring people are the experiences you never forget. It is my source of creativity and inspiration.',
                        'quoteReference' => 'Jack',
                    ],
                    [
                        'type' => 'text',
                        'description' => '<p>In 2014 Jack started his music career in San Diego, California. His talent in playing the guitar made him famous in a short period of time. His one big dream: touring around the United States. In 2016 he is as close as never before – with his new album, reaching records in the US.</p>',
                    ],
                    [
                        'type' => 'image-map',
                        'imageMap' => [
                            'imageId' => $this->mediaId('band.jpg'),
                            'hotspots' => [
                                [
                                    'type' => 'basic',
                                    'hotspot' => [
                                        'type' => 'circle',
                                        'left' => 0.5052987808664333,
                                        'top' => 0.5940029375917998,
                                        'radius' => 0.09,
                                    ],
                                    'title' => 'Lead Singer',
                                    'description' => 'This is our lead singer',
                                ],
                                [
                                    'type' => 'basic',
                                    'hotspot' => [
                                        'type' => 'circle',
                                        'left' => 0.2867240701260532,
                                        'top' => 0.6000031250976593,
                                        'radius' => 0.09,
                                    ],
                                    'title' => 'Basist',
                                    'description' => 'This is our basist',
                                ],
                                [
                                    'type' => 'basic',
                                    'hotspot' => [
                                        'type' => 'circle',
                                        'left' => 0.7927576428704489,
                                        'top' => 0.5880027500859402,
                                        'radius' => 0.09,
                                    ],
                                    'title' => 'Guitarist',
                                    'description' => 'This is our guitarist',
                                ],
                                [
                                    'type' => 'advanced',
                                    'hotspot' => [
                                        'type' => 'rectangle',
                                        'width' => 1,
                                        'height' => 0.20797524922653826,
                                        'left' => 0,
                                        'top' => 0.7920247507734617,
                                    ],
                                    'text' => 'This was an <b>awesome</b> crowd',
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'albums',
                        'albums' => [
                            $this->albumId('Wildfire'),
                            $this->albumId('Cross the River'),
                            $this->albumId('Gold Digger'),
                            $this->albumId('The Wolves'),
                        ],
                    ],
                ],
                'template' => 'default',
            ], [
                'locale' => AppFixture::LOCALE_EN,
                'title' => 'Marshall Plan',
                'url' => '/artists/marshall-plan',
                'parentOf' => '/artists',
                'subtitle' => '',
                'headerImage' => [
                    'id' => $this->mediaId('marshall.jpg'),
                ],
                'excerpt' => [
                    'images' => [
                        'ids' => [$this->mediaId('marshall.jpg')],
                    ],
                ],
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'Marshall Plan',
                        'description' => '<p>Releasing their record album in 2003, Marshall Plan spent more than one year bringing their passion for the rock music to the big concerthalls and arenas all over Great Britain – in 2015 worldwide. In this time the rockband started to grow together and wrote there second album.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'We love making music together and to inspire people around us with our songs. We come from a small town in the UK. It still feels unreal, to be known from Asia to the States.',
                        'quoteReference' => 'Jason Mcconkey',
                    ],
                    [
                        'type' => 'text',
                        'description' => '<p>In 2003 Alex, the frontman of Marshall Plan founded the band with his best friends Bronson, Albert and Ray. Those well known guitar player and songwriter in Liverpool. In 2007 Jason followed. His talent in playing the bass fullfilled the intense music vibes. Together they had one big dream: rocking the stage in front of the Times Square in New York. In 2016 they are as close as never before. With their new album, reaching records all over the world.</p>',
                    ],
                    [
                        'type' => 'albums',
                        'albums' => [
                            $this->albumId('Way'),
                            $this->albumId('let the light be'),
                            $this->albumId('Variety'),
                            $this->albumId('Path'),
                        ],
                    ],
                ],
                'template' => 'default',
            ], [
                'locale' => AppFixture::LOCALE_EN,
                'title' => 'The Bagpipes',
                'url' => '/artists/the-bagpipes',
                'parentOf' => '/artists',
                'subtitle' => '',
                'headerImage' => [
                    'id' => $this->mediaId('dudelsack.jpg'),
                ],
                'excerpt' => [
                    'images' => [
                        'ids' => [$this->mediaId('dudelsack.jpg')],
                    ],
                ],
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'The Bagpipes',
                        'description' => '<p>In the beginning they focused on traditional and contemporary music with innovative flair, before they concetrated on there classic bagpipe music. Short after releasing their record album in 1998, The Bagpipes spent more than one year bringing their passion for the folk music to the big concerthalls and arenas all over Scottland – in 2015 worldwide. In this time the folkband started to grow together and wrote there fourth album. Soon they become the Scottish folkband of the year 2003, 2005 and 2014.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'We started our career in the streets of Glasgow. There is nothing more real and authentic then playing street music. People like you, or they don\'t and pass you. You immediately get the reaction.',
                        'quoteReference' => 'Steve Avril',
                    ],
                    [
                        'type' => 'albums',
                        'albums' => [
                            $this->albumId('Joy'),
                            $this->albumId('Busk'),
                            $this->albumId('Bonfire'),
                            $this->albumId('Scottlang Call\'s'),
                        ],
                    ],
                ],
                'template' => 'default',
            ], [
                'locale' => AppFixture::LOCALE_EN,
                'title' => 'TJ Fury',
                'url' => '/artists/tj-fury',
                'parentOf' => '/artists',
                'subtitle' => '',
                'headerImage' => [
                    'id' => $this->mediaId('tj-fury.jpg'),
                ],
                'excerpt' => [
                    'images' => [
                        'ids' => [$this->mediaId('tj-fury.jpg')],
                    ],
                ],
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'TJ Fury',
                        'description' => '<p>In the beginning he focused on combinations between music and Hip Hop. Today he concentrated on stong powerful Lines in the scene of Hip Hop. After releasing his record album in 2011, TJ Fury spent more than one year bringing his passion for Hip Hop to the downtown clubs all around the big cities in the United States – in 2015 worldwide. In this time TJ Fury started to record their new album. Soon they got several awards.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'We love making music. Check out our new tracks.',
                        'quoteReference' => 'TJ Fury',
                    ],
                    [
                        'type' => 'albums',
                        'albums' => [
                            $this->albumId('Rebel'),
                            $this->albumId('random'),
                            $this->albumId('down_town'),
                            $this->albumId('Railling'),
                        ],
                    ],
                ],
                'template' => 'default',
            ], [
                'locale' => AppFixture::LOCALE_EN,
                'title' => 'Blog',
                'url' => '/blog',
                'subtitle' => 'We like to give you insights into what we do',
                'headerImage' => [
                    'id' => $this->mediaId('blog.jpg'),
                ],
                'navigationContexts' => ['main'],
                'template' => 'overview',
                'element' => [
                    [
                        'type' => 'articles',
                        'articles' => [
                            'sortBy' => 'published',
                            'sortMethod' => 'asc',
                        ],
                    ],
                ],
            ], [
                'locale' => AppFixture::LOCALE_EN,
                'title' => 'About Us',
                'url' => '/about',
                'subtitle' => 'We work hard, but we love what we do',
                'headerImage' => [
                    'id' => $this->mediaId('about.png'),
                ],
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'International Talents',
                        'description' => '<h3>International Talents was founded 1998</h3><p>From Great Britain all over the world International Talents become one of the worldwide leading music brand. With over 20 years of recorded music history, our passion for artistry in music continues today. We love to inspire young talents with all of our knowledge and experience.&nbsp;The desire to speak into the heart and soul of the listeners is what fueled the creative and strategic efforts of the label.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'The whole experience of 20 years and a lot of knowledge come together in International Talents. We love what we do, and no day is like the one before.',
                        'quoteReference' => 'Jonathan Benett',
                    ],
                    [
                        'type' => 'text',
                        'description' => '<p>But all this, is not possible, without a Team behind. This Team is attendable around the clock. They prepare your Event, help for your exhibition or Product Presentation. Everyone of them is an urban legend in what they do. Success is no accident. It grows with an great Team.</p>',
                    ],
                ],
                'navigationContexts' => ['main', 'footer'],
                'template' => 'default',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function pagesGerman(): array
    {
        return [
            [
                'translationOf' => '/artists',
                'locale' => AppFixture::LOCALE_DE,
                'title' => 'Musiker',
                'url' => '/musiker',
                'subtitle' => 'Entdecke unsere Vielfalt an talentierten Musiker',
                'headerImage' => [
                    'id' => $this->mediaId('artists.jpg'),
                ],
                'navigationContexts' => ['main', 'footer'],
                'template' => 'overview',
                'element' => [
                    [
                        'type' => 'pages',
                        'pages' => [
                            'sortBy' => 'published',
                            'sortMethod' => 'asc',
                        ],
                    ],
                ],
            ], [
                'translationOf' => '/artists/civil-literature',
                'locale' => AppFixture::LOCALE_DE,
                'title' => 'Civil Literature',
                'url' => '/musiker/civil-literature',
                'parentOf' => '/artists',
                'subtitle' => '',
                'headerImage' => [
                    'id' => $this->mediaId('civil-literature.jpg'),
                ],
                'excerpt' => [
                    'images' => [
                        'ids' => [$this->mediaId('civil-literature.jpg')],
                    ],
                ],
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'Civil Literature',
                        'description' => '<p>Nach dem release ihres neuen Albums 2014, verbrachte Civil Literature mehr als ein Jahr damit, auf den großen Bühnen der riesigen Hallen in Großbritanien, ihre Leidenschaft für die Rock Musik zu teilen - und 2015 dann sogar weltweit. In dieser Zeit wuchs die Rockband noch enger zusammen und schrieb ihr drittes Album.</p><p>Im Jahr 2010 gründete Liam, der Frontsänger der Band Civil Literature die Band mit seinem Bruder Garry, der in Manchester auch als Gitarrist und Songschreiber bekannt ist. Im Jahr 2011 folgte dann Marc. Sein Talent als Bass Spieler ergänzt sich perfekt zu der Musik die sie machten. Zusammen hatten sie einen großen Traum. Sie wollen zusammen die Bühne der Royal Albert Halle rocken. Im Jahr 2016 stehen sie vor diesem Ziel nun so kurz bevor. Vorallem deshalb, weil ihr neues Album Rekorde in ganz Europa bricht.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'Ich war schon immer ein leidenschaftlicher Song Schreiber. Mein größter Wunsch ist es die Menschen zu berühren und mit meiner Botschaft ermutigen nach ihren Träumen zu leben.',
                        'quoteReference' => 'Liam Hendrickson',
                    ],
                    [
                        'type' => 'albums',
                        'albums' => [
                            $this->albumId('Vikings'),
                            $this->albumId('Civilwar'),
                            $this->albumId('collapse'),
                            $this->albumId('#no more'),
                        ],
                    ],
                ],
                'template' => 'default',
            ], [
                'translationOf' => '/artists/coyoos',
                'locale' => AppFixture::LOCALE_DE,
                'title' => 'Coyoos',
                'url' => '/musiker/coyoos',
                'parentOf' => '/artists',
                'subtitle' => '',
                'headerImage' => [
                    'id' => $this->mediaId('coyoos.jpg'),
                ],
                'excerpt' => [
                    'images' => [
                        'ids' => [$this->mediaId('coyoos.jpg')],
                    ],
                ],
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'Coyoos',
                        'description' => '<p>Nach dem release ihres neuen Albums 2012, verbrachte Coyoos mehr als ein Jahr damit, auf den großen Bühnen der riesigen Hallen in den Vereinigten Staaten, ihre Leidenschaft für die Rock Musik zu teilen - und 2015 dann sogar weltweit. In dieser Zeit wuchs die Rockband noch enger zusammen und schrieb ihr drittes Album.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'Neue Orte zu entdecken und inspirierende Leute kennenzulernen sind Erfahrungen, die man nie vergisst. Sie sind die Quelle meiner Kreativität und Inspiration.',
                        'quoteReference' => 'Jack',
                    ],
                    [
                        'type' => 'text',
                        'description' => '<p>2014 startete Jack seine Musikkarriere in San Diego, California. Sein Talent mit der Gitarre lies ihn in kurzer Zeit bekannt werden. Sein großer Traum: In einer Tour durch die Vereinigten Staaten reisen. 2016 ist er so nah an seinem Ziel wie noch nie zuvor - mit seinem neuen Album erreichte er die Spitze der Charts in den Vereinigten Staaten.</p>',
                    ],
                    [
                        'type' => 'albums',
                        'albums' => [
                            $this->albumId('Wildfire'),
                            $this->albumId('Cross the River'),
                            $this->albumId('Gold Digger'),
                            $this->albumId('The Wolves'),
                        ],
                    ],
                ],
                'template' => 'default',
            ], [
                'translationOf' => '/artists/marshall-plan',
                'locale' => AppFixture::LOCALE_DE,
                'title' => 'Marshall Plan',
                'url' => '/musiker/marshall-plan',
                'parentOf' => '/artists',
                'subtitle' => '',
                'headerImage' => [
                    'id' => $this->mediaId('marshall.jpg'),
                ],
                'excerpt' => [
                    'images' => [
                        'ids' => [$this->mediaId('marshall.jpg')],
                    ],
                ],
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'Marshall Plan',
                        'description' => '<p>Nach dem Release ihres neuen Albums 2003, verbrachte Civil Literature mehr als ein Jahr damit, auf den großen Bühnen der riesigen Hallen in Großbritanien, ihre Leidenschaft für die Rock Musik zu teilen - und 2015 dann sogar weltweit. In dieser Zeit wuchs die Rockband noch enger zusammen und schrieb ihr zweites Album.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'Wir lieben es, zusammen Musik zu machen und die Menschen um uns herum mit unseren Songs zu inspirieren. Wir kommen aus einem kleinen Dorf in Großbritannien. Es fühlt sich surreal an, dass wir uns einen Namen von Asien bis zu den Staaten gemacht haben.',
                        'quoteReference' => 'Jason Mcconkey',
                    ],
                    [
                        'type' => 'text',
                        'description' => '<p>2003 gründete Alex, der Frontman von Marshall Plan die Band mit seinen besten freunden Albert und Ray, ein in Liverpool bekannter Gitarrenspieler und Songschreiber. 2007 folgte dann Jason. Sein Talent mit dem Bass war genau das richtige für die intensiven Vibes der Band. Sie hatten einen großen Traum zusammen: Die Bühne vor dem Times Square in New York zu rocken. 2016 sind sie so nah an ihrem Ziel wie noch nie zuvor - mit ihrem neuen Album erreichten sie die Spitze der Charts in den Vereinigten Staaten.</p>',
                    ],
                    [
                        'type' => 'albums',
                        'albums' => [
                            $this->albumId('Way'),
                            $this->albumId('let the light be'),
                            $this->albumId('Variety'),
                            $this->albumId('Path'),
                        ],
                    ],
                ],
                'template' => 'default',
            ], [
                'translationOf' => '/artists/the-bagpipes',
                'locale' => AppFixture::LOCALE_DE,
                'title' => 'The Bagpipes',
                'url' => '/musiker/the-bagpipes',
                'parentOf' => '/artists',
                'subtitle' => '',
                'headerImage' => [
                    'id' => $this->mediaId('dudelsack.jpg'),
                ],
                'excerpt' => [
                    'images' => [
                        'ids' => [$this->mediaId('dudelsack.jpg')],
                    ],
                ],
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'The Bagpipes',
                        'description' => '<p>In den Anfängen haben sich die Bagpipes auf traditionelle und zeitnahe Musik mit ihrem innovativen Flair konzentriert, bevor sie sich dann auf ihre klassische Dudelsackmusik stürtzten. Kurz nach der Veröffentlichung ihres Albums in 1998, haben die Bagpipes mehr als ein Jahr zusammen damit verbracht, ihre Leidenschaft auf die Bühnen und Arenen Schottlands zu bringen - in 2015 dann Weltweit. In dieser Zeit wuchs die Folkband noch enger zusammen und schrieb ihr viertes Album. Sie wurden die Schottische Folkband von den Jahren 2003, 2005 und 2014.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'Unsere Karriere startete auf den Straßen von Glasgow. Es gibt nichts authentischeres als Straßenmusik. Die Menschen mögen dich, oder laufen einfach weiter. Man merkt sofort, wie die Musik ankommt.',
                        'quoteReference' => 'Steve Avril',
                    ],
                    [
                        'type' => 'albums',
                        'albums' => [
                            $this->albumId('Joy'),
                            $this->albumId('Busk'),
                            $this->albumId('Bonfire'),
                            $this->albumId('Scottlang Call\'s'),
                        ],
                    ],
                ],
                'template' => 'default',
            ], [
                'translationOf' => '/artists/tj-fury',
                'locale' => AppFixture::LOCALE_DE,
                'title' => 'TJ Fury',
                'url' => '/musiker/tj-fury',
                'parentOf' => '/artists',
                'subtitle' => '',
                'headerImage' => [
                    'id' => $this->mediaId('tj-fury.jpg'),
                ],
                'excerpt' => [
                    'images' => [
                        'ids' => [$this->mediaId('tj-fury.jpg')],
                    ],
                ],
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'TJ Fury',
                        'description' => '<p>In den Anfängen hat sich TJ Fury auf Kombinationen von zeitnaher Musik und Hip Hop fokusiert. Heute konzentriert er sich auf kraftvolle Texte in der Hip Hop Szene. Nach der Veröffentlichung seines Albums in 2011, hat TJ Fury mehr als ein Jahr damit verbracht seine Leidenschaft für Hip Hop in die Clubs der größen Städte rundum den Staaten zu bringen - in 2015 dann Weltweit. Zu dieser Zeit nahm TJ Fury sein neues Album auf. Bald wurde er für zahlreiche Auszeichnungen nominiert und gewann einige davon.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'Wir lieben es, Musik zu kreieren. Hört euch unsere neuen Tracks an.',
                        'quoteReference' => 'TJ Fury',
                    ],
                    [
                        'type' => 'albums',
                        'albums' => [
                            $this->albumId('Rebel'),
                            $this->albumId('random'),
                            $this->albumId('down_town'),
                            $this->albumId('Railling'),
                        ],
                    ],
                ],
                'template' => 'default',
            ], [
                'translationOf' => '/blog',
                'locale' => AppFixture::LOCALE_DE,
                'title' => 'Blog',
                'url' => '/blog',
                'subtitle' => 'Erhalten Sie einen Einblick in unsere Arbeit',
                'headerImage' => [
                    'id' => $this->mediaId('blog.jpg'),
                ],
                'navigationContexts' => ['main'],
                'template' => 'overview',
                'element' => [
                    [
                        'type' => 'articles',
                        'articles' => [
                            'sortBy' => 'published',
                            'sortMethod' => 'asc',
                        ],
                    ],
                ],
            ], [
                'translationOf' => '/about',
                'locale' => AppFixture::LOCALE_DE,
                'title' => 'International Talents',
                'url' => '/about',
                'subtitle' => 'Wir arbeiten hart, aber lieben was wir tun',
                'headerImage' => [
                    'id' => $this->mediaId('about.png'),
                ],
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'International Talents',
                        'description' => '<h3>International Talents wurde 1998 gegründet.</h3><p>Von Großbritanien aus wuchs International Talents über die ganze Welt zu einer der weltweit führenden Musik Marken.Wie lieben es junge Talente mit all unserem Wissen und Erfahrungen zu begleiten und inspirieren. Mit über 20 Jahren an Musik Aufnahmen, unserer Leidenschaft für die Musik Künstler geht heute weiter. Der Wunsch den Höreren und Fans ins Herz zusprechen ist die Motivation für immer neue kreative Ideen und Strategien des Labels.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'Die ganze Erfahrung aus 20 Jahren und eine Menge Wissen kommen bei International Talents zusammen. Wir lieben was wir tun und kein Tag ist wie der zuvor.',
                        'quoteReference' => 'Jonathan Benett',
                    ],
                    [
                        'type' => 'text',
                        'description' => '<p>Aber alles zusammen wäre nicht möglich, ohne ein Team welches dahinter steht. Dieses Team ist erreichbar rund um die Uhr. Sie bereiten dein Event vor, helfen bei deiner Ausstellung oder Produkt Präsentation. Jeder von Ihnen ist eine lebende Legende in was sie tun. Erfolg passiert nicht einfach so. Er wächst vielmehr mit einem großartigen Team.</p>',
                    ],
                ],
                'navigationContexts' => ['main', 'footer'],
                'template' => 'default',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function articlesEnglish(): array
    {
        return [
            [
                'locale' => AppFixture::LOCALE_EN,
                'title' => 'A great song will win',
                'excerpt' => [
                    'title' => 'A great song will win',
                    'description' => '<p>We got the chance to talk to the Head of International Talents Jonathan Bennett. We talked about his career highlights and his advice for the artists.</p>',
                    'images' => [
                        'ids' => [$this->mediaId('mic.jpg')],
                    ],
                ],
                'headerImage' => [
                    'id' => $this->mediaId('mic.jpg'),
                ],
                'template' => 'blog',
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'A great song will win in the end',
                        'description' => '<p>For 50 years he is working with the greatest artists in the world. His projects have sold more than 50 million tracks worldwide. Usually he have no time but we got the chance to talk to the Head of International Talents Jonathan Bennett. We talked about his career highlights and his advice for the artists.</p><p>In the beginning Jonathan have been working for several different recording agencies. One Day (18 years ago) Calvin Merrit called me and asked if I would be interested in working as an International Talents consultant on the newcomer project Marshall Plan from the UK. So I decided to do that and hat an amazing time together wit the band. The band became famous in just a few years and so Calvin asked me if I would join International Talents as Head of the agency in August 1999.</p>',
                    ],
                    [
                        'type' => 'text',
                        'title' => 'Advice for artists who want to get discovered',
                        'description' => '<p>In my opinion I think the most important thing is to work on a long time developing a strong live show. When I remember back to Marschall Plan, the reason why we prepared that long time, was there live show. We reflected, invented, tried and rehearsed so I never mattered about there first album debut. They were good playing there concerts live - Everything.</p><p>It was the same with Civil Literature. When I saw them playing the first time. I got the feeling that they will become one of the biggest bands in the world, if they would upgrade there live acts. There was so much potential on the stage.</p><p>So when I worked with those bands together we spent most of the time into their live shows, The other thing I learned over the years, the bands really should work on there songwriting focus. Because this one song have to breakthrough a millions of songs and should be remembered by the fans and people out there.</p><p>Strong lines is what the people love.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'You can just win if you have a great song. The part of the show is the help of the agency. They can help you to jump through the ground. But work hard and than you will see the result.',
                        'quoteReference' => 'Jonathan Benett',
                    ],
                    [
                        'type' => 'similar-articles',
                    ],
                ],
            ], [
                'locale' => AppFixture::LOCALE_EN,
                'title' => 'A week on the road with Civil Literature',
                'excerpt' => [
                    'title' => 'A week on the road with Civil Literature',
                    'description' => '<p>Two month ago Civil Literature launched their new album "Civil War". Now they are on tour for one week and they have already played half a dozend concerts.</p>',
                    'images' => [
                        'ids' => [$this->mediaId('roadtrip.jpg')],
                    ],
                ],
                'headerImage' => [
                    'id' => $this->mediaId('roadtrip.jpg'),
                ],
                'template' => 'blog',
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'A week on the road with Civil Literature',
                        'description' => '<p>It took them three years but now it\'s finally here. Two month ago Civil Literature launched their new album "Civil War". Now they are on tour for one week and they have already played half a dozend concerts.</p><p>You\'re watching and thinking, "the show\'s true magic and their performace is amazing", that\'s how famous music critic Joe Zipotta descriped the show in Amsterdam. "It seems like in the last three year the band has improved a lot and wrote some awesome songs. Especially the frontman Liam was fascinating, he managed it to take the visitors in a other world. It was truely terrific."</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'The first week is over and is\'s so great! Germany and the Netherlands, Berlin and Amsterdam, tour live is awesome!',
                        'quoteReference' => 'Liam Mercx',
                    ],
                    [
                        'type' => 'text',
                        'description' => '<p>Civil Literatures concerts in Germany and the Netherlands are already over, but there will be a lot of other opportunities to see them and hear their extra ordanary new songs. The next stops are Paris and London. After Europe Civil Literature will then play in America and Asia.</p>',
                    ],
                    [
                        'type' => 'similar-articles',
                    ],
                ],
            ], [
                'locale' => AppFixture::LOCALE_EN,
                'title' => 'Behind the scenes of our creative directors',
                'excerpt' => [
                    'title' => 'Behind the scenes of our creative directors',
                    'description' => '<p>As the people working at International Talents it is our job to help our costumers to create something you will love.</p>',
                    'images' => [
                        'ids' => [$this->mediaId('meeting.jpg')],
                    ],
                ],
                'headerImage' => [
                    'id' => $this->mediaId('meeting.jpg'),
                ],
                'template' => 'blog',
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'Behind the scenes of our creative directors',
                        'description' => '<p>As a creative director every new das is a challange, but it\'s one that I like to take up. I know what our costumers expect from us and so I can create something that they will love. For me that\'s the real deal.</p><p>Some times I find myself sitting in an armchair and enjoying the silence, that\'s when I get my greatest ideas. Working on new songs with our artists is challenging, especially since every one of them need something else from us. Some want us to help them find new ideas for songs and others only want us to help them give their songs the final touch. So every day is a new callange and I am ony able to manage this with the help of our excelent team.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'Music lies deep within our soul, only because of this we can create something that is pure magic.',
                        'quoteReference' => 'Joe Armson',
                    ],
                    [
                        'type' => 'text',
                        'description' => '<p>As the people working at International Talents it is our job to help our costumers to create something you will love. In order to achieve this we need to live not just our pilosophy but also our customers. Thats a huge challange but we use all our energy to achive this.</p><p>One of the hardest things about this job is that although we have to support our artists we also need to make sure they have enougth freedom to try out something knew, even if we might not think a idea is not so great at the beginning. If we would have strict standard the artists would never be able to create these magic songs that they do.</p>',
                    ],
                    [
                        'type' => 'similar-articles',
                    ],
                ],
            ], [
                'locale' => AppFixture::LOCALE_EN,
                'title' => 'Drop Big Beats',
                'excerpt' => [
                    'title' => 'Drop Big Beats',
                    'description' => '<p>Charlotte Merana shares her advice for ambitious DJs and electronic musicans.</p>',
                    'images' => [
                        'ids' => [$this->mediaId('tj-fury.jpg')],
                    ],
                ],
                'headerImage' => [
                    'id' => $this->mediaId('tj-fury.jpg'),
                ],
                'template' => 'blog',
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'Drop big Beats',
                        'description' => '<p>Her finger on the pulse of dance and electronic music. Usually she is not listening to the music filled up with crazy beats. But today she shared her advice for ambitious DJs and electronic musicans. Her name is Charlotte Merana and she is the general Manager of the big beats of International Talents.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'I\'m so excited about what will come next - where will the trend blaze the trail.',
                        'quoteReference' => 'Charlotte Merena',
                    ],
                    [
                        'type' => 'text',
                        'description' => '<p>Charlotte explained, she never predicted it would get this big. In her thoughts and visions she hoped for it. But if she shared her dreams 30 years earlier, the people would laugh about her. Today you can\'t believe it. Kids loving this music. Not all of them are quite kids but for example a booking agency from Berlin in Germany signed a 14-year-old  DJ. It\'s really exciting for them, but also for me. She always believed in this subculture, but never predicted that it would get this big.</p><h3>Charlotte also shared her advice for younger artist who wants to get the attention of the people.</h3><p>She told us to beginn with your friends first. Do what you know, what you already learned. Show the people what you can do and win them. Bring the people to support you. Start throwing your party and grow up a network of people who like what you do and are excited about what you do. Don\'t make a fanbpage, and put your sounds on Soundcloud or do a crazy Photoshooing.</p>',
                    ],
                    [
                        'type' => 'similar-articles',
                    ],
                ],
            ], [
                'locale' => AppFixture::LOCALE_EN,
                'title' => 'Legend behind the Mix',
                'excerpt' => [
                    'title' => 'Legend behind the Mix',
                    'description' => '<p>We got the oppurtunity to sit down with our legendary record producer and mix master James McMorrison.</p>',
                    'images' => [
                        'ids' => [$this->mediaId('sound.jpg')],
                    ],
                ],
                'headerImage' => [
                    'id' => $this->mediaId('sound.jpg'),
                ],
                'template' => 'blog',
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'Legend behind the Mix',
                        'description' => '<p>Ever thought how your most loved songs are made? Ever thought what process a song have to pass before the radio plays or you can buy the vinyl? This trial of producing music is real art, and in the background there are a lot of talented technicans working behind the scenes to achieve the tracks we love and enjoy and dance.</p><p>We are enthusiastic about those technican engineers and got the oppurtunity to sit down with our legendary record producer and mix master James McMorrison. Together we talked about his growing experience over the years. The fast changing audio industry, his work with the band Civil Literature and what each sound board operator needs, to become a legend in recording.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'First I started recording myself recording in the Bathroom, with whatever equipment I could find in the Walmart. Yees, I bought it from my little pocket money. When the time started, where I played in several bands, the technology started to develop. First I tried to record on the real stuff at that time - cassettes. Oh I was really into that. When I went to Liberty College of Music close to Nashville, I dreamed all the time about becoming a musican. A legend rocking the huge stages all around the world. But than the Liberty College started a new different program. I didn\'t realize it, but a friend told me. The program a good friend of mine recommended was called musc production and engineering. I never changed this till now.',
                        'quoteReference' => 'James McMorrison',
                    ],
                    [
                        'type' => 'similar-articles',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function articlesGerman(): array
    {
        return [
            [
                'translationOf' => '/blog/a-great-song-will-win',
                'locale' => AppFixture::LOCALE_DE,
                'title' => 'Ein Guter Song wird immer gewinnen',
                'excerpt' => [
                    'title' => 'Ein Guter Song wird immer gewinnen',
                    'description' => '<p>Wir haben die Möglichkeit ergriffen, mit dem Mann, der ganz oben bei International Talents steht ein Gespräch zu führen.</p>',
                    'images' => [
                        'ids' => [$this->mediaId('mic.jpg')],
                    ],
                ],
                'headerImage' => [
                    'id' => $this->mediaId('mic.jpg'),
                ],
                'template' => 'blog',
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'Ein Guter Song wird immer gewinnen',
                        'description' => '<p>Er arbeitet schon seit über 50 Jahren mit den besten Künstlern der Welt. Seine Projekte haben weltweit schon über 50 Millionen Platten verkauft. Normalerweise hat er nicht viel Zeit, doch wir haben die Chance erhalten, ein Gespräch mit dem Mann, der ganz oben bei International Talents steht zu führen, Jonathan Bennet. Wir haben über die Highlights seiner Karriere und über Ratschläge für Künstler geredet.</p><p>In den Anfängen hat Jonathan für viele verschiedene Plattenfirmen gearbeitet. Eines Tages (vor 18 Jahren) hat mich Clavin Merrit angerufen und gefragt ob daran interessiert wäre, bei International Talents als Berater der jungen Band Marshall Plan aus Großbritannien zu arbeiten. Ich habe mich dafür entschieden und hatte eine unglaubliche Zeit zusammen mit der Band. Die Künstler wurden in nur wenigen Jahren berühmt, deswegen fragte mich Calvin 1999 ob ich International Talents als Kopf der Agentur beitreten möchte.</p>',
                    ],
                    [
                        'type' => 'text',
                        'title' => 'Ratschläge für Künstler, die entdeckt werden wollen',
                        'description' => '<p>Meiner Meinung nach ist das wichtigste, hart daran zu arbeiten, eine starke Live-show zu bieten. Wenn ich an Marshall Plan zurückdenke, war der Grund für die lange Vorbereitungsphase ihre Live-show. Wir haben reflektiert, geprobt und neue dinge ausprobiert. Mich kümmerte das erste Album weniger als ihre Live-show. Sie waren gut darin, ihre Konzerte live zu spielen.</p><p>Mit Civil Literature war es sehr ähnlich. Als ich sie zum ersten mal spielen sah, bekam ich das Gefühl, dass sie eines Tages eine der berühmtesten Bands weltweit sein werden, wenn sie ihre Live- Auftritte verbessern. Es gab so viel Potential auf der Bühne.</p><p>Als ich mit diesen Bands zusammenarbeitete, haben wir die meiste Zeit damit verbracht, ihre Live- Auftritte zu verbessern. Die andere Sache, die ich über die Jahre mitgenomme habe, ist, dass die Bands wirklich an ihren Songtexten arbeiten sollten. Dieser eine Song, der mit seinem Text an millionen von anderen Songs vorbeizieht und von den Fans niemals vergessen wird.</p><p>Starke Texte sind das, was die Leute lieben.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'Du kannst in der Szene gewinnen, wenn du einen großartigen song gemacht hast. Der Teil der Agentur ist, dass sie dir dabei hilft. Doch wenn du hart arbeitest dann wirst du früher oder später das Resultat sehen.',
                        'quoteReference' => 'Jonathan Benett',
                    ],
                    [
                        'type' => 'similar-articles',
                    ],
                ],
            ], [
                'translationOf' => '/blog/a-week-on-the-road-with-civil-literature',
                'locale' => AppFixture::LOCALE_DE,
                'title' => 'Eine Woche unterwegs mit Civil Literature',
                'excerpt' => [
                    'title' => 'Eine Woche unterwegs mit Civil Literature',
                    'description' => '<p>Vor zwei Monaten hat Civil Literature ihr neues Album "Civil War" veröffentlicht. Nun sind sie für eine Woche auf Tour und bis jetzt haben sie schon ein halbes Duzent Konzerte gegeben.</p>',
                    'images' => [
                        'ids' => [$this->mediaId('roadtrip.jpg')],
                    ],
                ],
                'headerImage' => [
                    'id' => $this->mediaId('roadtrip.jpg'),
                ],
                'template' => 'blog',
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'Eine Woche unterwegs mit Civil Literature',
                        'description' => '<p>Es hat sie drei Jahre Arbeit gekostet, doch nun ist es endlich da. Vor zwei Monaten hat Civil Literature ihr neues Album "Civil War" veröffentlicht. Nun sind sie für eine Woche auf Tour und bis jetzt haben sie schon ein halbes Duzent Konzerte gegeben.</p><p>Du siehst einfach nur zu und denkst dir, "Die Show ist echte Magie und ihre Performance ist atemberaubend", So hat der bekannte Musikkritiker Joe Zipotta die show in Amsterdam beschrieben. "Es scheint als hätte sich die Band in den letzten drei Jahren sehr verbessert und haben einige geniale Songs geschrieben. Vorallem der Frontman Liam war faszinierend, er hat es geschafft, die besucher in eine andere Welt zu versetzen. Es war wirklich großartig."</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'Wir haben die erste Woche hinter uns und es ist so toll! Deutschland und die Niederlande, Berlin und Amsterdam, das Tourleben ist genial!',
                        'quoteReference' => 'Liam Mercx',
                    ],
                    [
                        'type' => 'text',
                        'description' => '<p>Die Konzerte in Deutschland und den Niederlanden sind schon vorbei, doch es wird in Zukunft noch viele andere möglichkeiten geben, sie zu sehen und ihre neuen extraordinären Songs zu erleben. Der nächsten Stops sind Paris und London. Nach der Europatour werden Civil Literatur in Amerika und Asien spielen.</p>',
                    ],
                    [
                        'type' => 'similar-articles',
                    ],
                ],
            ], [
                'translationOf' => '/blog/behind-the-scenes-of-our-creative-directors',
                'locale' => AppFixture::LOCALE_DE,
                'title' => 'Hinter den Kulissen unserer creative Directors',
                'excerpt' => [
                    'title' => 'Hinter den Kulissen unserer creative Directors',
                    'description' => '<p>Als Mitarbeiter bei International Talents ist es unsere Aufgabe unseren Kunden zu helfen, damit sie etwas erstellen können, dass ihre Fans lieben werden.</p>',
                    'images' => [
                        'ids' => [$this->mediaId('meeting.jpg')],
                    ],
                ],
                'headerImage' => [
                    'id' => $this->mediaId('meeting.jpg'),
                ],
                'template' => 'blog',
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'Hinter den Kulissen unserer creative Directors',
                        'description' => '<p>Als creative Director ist jeder neuer Auftrag eine Herausforderung, doch ist es eine, die ich gerne auf mich nehme. Ich weiß was unsere Kunden von uns erwarten, somit kann ich etwas kreieren, dass sie lieben werden, was für mich sehr wichtig ist.</p><p>Manchmal erwische ich mich selbst dabei, wie ich in meinem Sessel sitze und die Stille genieße, denn dort fallen mir die besten Ideen ein. Das Arbeiten an neuen Songs mit unseren Künstlern ist eine Herausforderung, da jeder einzelne von Ihnen etwas anderes von uns benötigt. Einigen helfen wir dabei, neue Ideen für ihre Musik zu finden, andere benötigen nur noch den letzten Feinschliff für ihre Songs. Somit ist jeder Tag eine neue Herausforderung. Doch ich würde das Ganze niemals ohne die Hilfe unseres hervorragenden Teams schaffen.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'Die Musik liegt tief in unseren Seelen, nur deshalb können wir etwas kreieren, dass purer Magie gleicht.',
                        'quoteReference' => 'Joe Armson',
                    ],
                    [
                        'type' => 'text',
                        'description' => '<p>Als Mitarbeiter bei International Talents ist es unsere Aufgabe unseren Kunden zu helfen, damit sie etwas erstellen können, dass ihre Fans lieben werden. Um dass zu ermöglichen müssen wir nicht nur unsere Philosophie leben, sondern auch unsere Kunden. Das ist eine rießige Herausforderung und wir verwenden all unsere Energie um genau das zu erreichen.</p><p>Einer der härtesten Aspekte des Jobs ist, obwohl wir unsere Künstler unterstützen müssen, sollten wir trotzdem darauf achten, dass sie genug Freiraum haben, neue Dinge auszuprobieren, auch wenn wir am Anfang vielleicht die Idee kritisch betrachten. Hätten wir strikte Standards und Vorgaben für unsere Künstler, würden sie nie in die Lage kommen, ihre magischen Werke zu kreieren.</p>',
                    ],
                    [
                        'type' => 'similar-articles',
                    ],
                ],
            ], [
                'translationOf' => '/blog/drop-big-beats',
                'locale' => AppFixture::LOCALE_DE,
                'title' => 'Fette Beats',
                'excerpt' => [
                    'title' => 'Fette Beats',
                    'description' => '<p>Charlotte Merena teilt ihren Ratschlag für ehrgeizige DJs und Künstler, die in der elektronischen Musikbranche tätig sind.</p>',
                    'images' => [
                        'ids' => [$this->mediaId('tj-fury.jpg')],
                    ],
                ],
                'headerImage' => [
                    'id' => $this->mediaId('tj-fury.jpg'),
                ],
                'template' => 'blog',
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'Fette Beats',
                        'description' => '<p>Mit ihrem finger am Puls von Dance und elektonischer Musik. Normalerweise hört sie keine Musik, die gefüllt mit verrückten und ausgefallenen Beats ist. Doch heute teilt sie ihren Ratschlag für ehrgeizige DJs und Künstler, die in der elektronischen Musikbranche tätig sind. Ihr Name ist Charlotte Merena, general Manager der fettesten beats bei international Talents.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'Ich bin so aufgeregt, was als nächstes kommt und welchen Pfad der Trend nehmen wird.',
                        'quoteReference' => 'Charlotte Merena',
                    ],
                    [
                        'type' => 'text',
                        'description' => '<p>Charlotte erklärt, dass sie niemals erwartet hätte, dass die Sache so explodieren würde. In ihren Gedanken und Visionen hoffte sie darauf, hätte sie ihre Träume jedoch 30 Jahre früher geteilt, wäre sie höchstwahrscheinlich ausgelacht worden. Heute kann man es kaum glauben: vorallem die jüngere Generation liebt diese Art von Musik. Gerade erst hat eine Buchungsagentur aus Berlin einen 14 Jahre alten DJ unter die fittiche genommen. "Es ist nicht nur aufregend für sie, sondern auch für mich", erklärt Charlotte. Sie hatte immer Hoffnung in diese Subkultur, jedoch ist das Ausmaß, welche sie angenommen hat, unhervorsehbar gewesen.</p><h3>Charlotte teilte mit uns auch ihren Ratschlag, insbesondere für jüngere Künstler, die sich einen Namen in der Szene machen wollen.</h3><p>Tu dass, was du schon gelernt hast, und zeige den Leuten was du kannst um sie zu überzeugen. Sie erklärte, dass es eine gute Idee ist, zuerst mit den eigenen Freunden anzufangen. Versuche ein Netzwerk von Leuten zu bilden, die mögen was du machst und die selber Gefallen in dem was du machst finden. Erstelle keine Fanpage, stelle deine Werke nicht auf Soundcloud und mach keine verrückten Photoshootings.</p>',
                    ],
                    [
                        'type' => 'similar-articles',
                    ],
                ],
            ], [
                'translationOf' => '/blog/legend-behind-the-mix',
                'locale' => AppFixture::LOCALE_DE,
                'title' => 'Eine Legende hinter dem Mischpult',
                'excerpt' => [
                    'title' => 'Eine Legende hinter dem Mischpult',
                    'description' => '<p>Wir haben die Chance bekommen, ein Gespräch mit dem legendären Musikproduzenten und Mix-Master James McMorrison zu führen.</p>',
                    'images' => [
                        'ids' => [$this->mediaId('sound.jpg')],
                    ],
                ],
                'headerImage' => [
                    'id' => $this->mediaId('sound.jpg'),
                ],
                'template' => 'blog',
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'Eine Legende hinter dem Mischpult',
                        'description' => '<p>Hast du jemals darüber nachgedacht, wie deine lieblingssongs erstellt werden? Hast du dir schon einmal Gedanken darüber gemacht, welcher Prozess ein Song durchmachen muss, bevor er Radiotauglich ist oder auf Schallplatte erhältlich ist? Musik zu Produzieren ist echte Kunst und im Hintergrund arbeiten viele talentierte Techniker hinter den Kulissen um die Tracks zu erschaffen, zu denen wir alle tanzen können.</p><p>Wir teilen unsere Beigeisterung mit diesen Ingenieuren und haben die Chance bekommen, ein Gespräch mit dem legendären Musikproduzenten und Mix-Master James McMorrison zu führen. Wir haben über seine Jahrelange und ständig wachsende Erfahrung in der Szene, die rapiden Änderungen des Sounds in der Audioindustrie, seine Zusammenarbeit mit der Band Civil Literatur und was jeder Sound-board Bediener braucht, um eine Legende in der Audio-Aufnahme zu werden geredet.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'Ich fing damit an, mich im Bad selber aufzunehmen, mit Ausrüstung die ich für mein Taschengeld im Walmart finden konnte. In der Zeit in der ich in mehreren Bands gespielt hatte, fing auch die Technologie an sich zu weiterzuentwickeln. Als erstes habe ich versucht auf Kassetten aufzunehmen - welche zu dieser Zeit der Hit waren. Dort habe ich mich richtig ausgetobt. Als ich dann auf die Universität \'Liberty College of Music\' in der nähe von Nashville ging, träumte ich die ganze Zeit davon ein Musiker zu werden. Eine Legende, die die größten Bühnen rundum die Welt rockt. Doch dann startete die Universität ein neues, etwas anderes Programm. Ich wusste nichts davon bis mich ein Freund darauf Aufmerksam machte. Das Programm wurde "music production and engineering" genannt, also Musikproduktion. Damit habe ich bis heute nicht aufgehört.',
                        'quoteReference' => 'James McMorrison',
                    ],
                    [
                        'type' => 'similar-articles',
                    ],
                ],
            ],
        ];
    }
}
