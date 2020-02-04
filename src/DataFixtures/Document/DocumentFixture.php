<?php

namespace App\DataFixtures\Document;

use App\DataFixtures\ORM\AppFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use OutOfBoundsException;
use RuntimeException;
use Sulu\Bundle\ArticleBundle\Document\ArticleDocument;
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
        $pages = $this->loadPages($documentManager);
        $documentManager->flush();
        $documentManager->clear();
        $this->loadPagesGerman($documentManager, $pages);

        $snippet = $this->loadContactSnippet($documentManager);
        $this->loadContactSnippetGerman($documentManager, $snippet);
        $this->loadHomepage($documentManager);

        $articles = $this->loadArticles($documentManager);
        $documentManager->flush();
        $documentManager->clear();
        $this->loadArticlesGerman($documentManager, $articles);

        // Needed, so that a Document use by loadHomepageGerman is managed.
        $documentManager->flush();
        $documentManager->clear();

        $this->loadHomepageGerman($documentManager);

        $documentManager->flush();
        $documentManager->clear();

        $this->updatePages($documentManager, AppFixtures::LOCALE_EN);
        $this->updatePages($documentManager, AppFixtures::LOCALE_DE);

        $documentManager->flush();
        $documentManager->clear();
    }

    /**
     * @throws MetadataNotFoundException
     *
     * @return mixed[]
     */
    private function loadPages(DocumentManager $documentManager): array
    {
        $pageDataList = [
            [
                'locale' => AppFixtures::LOCALE_EN,
                'title' => 'Artists',
                'url' => '/artists',
                'subtitle' => 'Discover our roster of talented musicians',
                'headerImage' => [
                    'id' => $this->getMediaId('artists.jpg'),
                ],
                'navigationContexts' => ['main', 'footer'],
                'structureType' => 'overview',
                'element' => [
                    [
                        'type' => 'pages',
                        'pages' => [
                            'sortBy' => 'published',
                            'sortMethod' => 'asc',
                        ],
                    ],
                ],
            ],
            [
                'locale' => AppFixtures::LOCALE_EN,
                'title' => 'Civil Literature',
                'url' => '/artists/civil-literature',
                'parent_path' => '/cmf/demo/contents/artists',
                'subtitle' => '',
                'headerImage' => [
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
                'locale' => AppFixtures::LOCALE_EN,
                'title' => 'Coyoos',
                'url' => '/artists/coyoos',
                'parent_path' => '/cmf/demo/contents/artists',
                'subtitle' => '',
                'headerImage' => [
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
                'locale' => AppFixtures::LOCALE_EN,
                'title' => 'Marshall Plan',
                'url' => '/artists/marshall-plan',
                'parent_path' => '/cmf/demo/contents/artists',
                'subtitle' => '',
                'headerImage' => [
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
                'locale' => AppFixtures::LOCALE_EN,
                'title' => 'The Bagpipes',
                'url' => '/artists/the-bagpipes',
                'parent_path' => '/cmf/demo/contents/artists',
                'subtitle' => '',
                'headerImage' => [
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
                'locale' => AppFixtures::LOCALE_EN,
                'title' => 'TJ Fury',
                'url' => '/artists/tj-fury',
                'parent_path' => '/cmf/demo/contents/artists',
                'subtitle' => '',
                'headerImage' => [
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
                'locale' => AppFixtures::LOCALE_EN,
                'title' => 'Blog',
                'url' => '/blog',
                'subtitle' => 'We like to give you insights into what we do',
                'headerImage' => [
                    'id' => $this->getMediaId('blog.jpg'),
                ],
                'navigationContexts' => ['main'],
                'structureType' => 'overview',
                'element' => [
                    [
                        'type' => 'articles',
                        'articles' => [
                            'sortBy' => 'published',
                            'sortMethod' => 'asc',
                        ],
                    ],
                ],
            ],
            [
                'locale' => AppFixtures::LOCALE_EN,
                'title' => 'About Us',
                'url' => '/about',
                'subtitle' => 'We work hard, but we love what we do',
                'headerImage' => [
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

        $pages = [];

        foreach ($pageDataList as $pageData) {
            $pages[$pageData['url']] = $this->createPage($documentManager, $pageData);
        }

        return $pages;
    }

    /**
     * @param PageDocument[] $pages
     *
     * @throws MetadataNotFoundException
     */
    private function loadPagesGerman(DocumentManager $documentManager, array $pages): void
    {
        $pageDataList = [];

        /**
         * @var string
         * @var PageDocument $pageDocument
         */
        foreach ($pages as $url => $pageDocument) {
            switch ($url) {
                case '/artists':
                    $pageDataList[] = [
                        'id' => $pageDocument->getUuid(),
                        'locale' => AppFixtures::LOCALE_DE,
                        'title' => 'Musiker',
                        'url' => '/musiker',
                        'subtitle' => 'Entdecke unsere Vielfalt an talentierten Musiker',
                        'headerImage' => [
                            'id' => $this->getMediaId('artists.jpg'),
                        ],
                        'navigationContexts' => ['main', 'footer'],
                        'structureType' => 'overview',
                        'element' => [
                            [
                                'type' => 'pages',
                                'pages' => [
                                    'sortBy' => 'published',
                                    'sortMethod' => 'asc',
                                ],
                            ],
                        ],
                    ];

                    break;
                case '/artists/civil-literature':
                    $pageDataList[] = [
                        'id' => $pageDocument->getUuid(),
                        'locale' => AppFixtures::LOCALE_DE,
                        'title' => 'Civil Literature',
                        'url' => '/musiker/civil-literature',
                        'parent_path' => '/cmf/demo/contents/artists',
                        'subtitle' => '',
                        'headerImage' => [
                            'id' => $this->getMediaId('civil-literature.jpg'),
                        ],
                        'blocks' => [
                            [
                                'type' => 'heading',
                                'heading' => 'Civil Literature',
                            ],
                            [
                                'type' => 'text',
                                'text' => '<p>Nach dem release ihres neuen Albums 2014, verbrachte Civil Literature mehr als ein Jahr damit, auf den großen Bühnen der riesigen Hallen in Großbritanien, ihre Leidenschaft für die Rock Musik zu teilen - und 2015 dann sogar weltweit. In dieser Zeit wuchs die Rockband noch enger zusammen und schrieb ihr drittes Album.</p>',
                            ],
                            [
                                'type' => 'text',
                                'text' => '<p>Im Jahr 2010 gründete Liam, der Frontsänger der Band Civil Literature die Band mit seinem Bruder Garry, der in Manchester auch als Gitarrist und Songschreiber bekannt ist. Im Jahr 2011 folgte dann Marc. Sein Talent als Bass Spieler ergänzt sich perfekt zu der Musik die sie machten. Zusammen hatten sie einen großen Traum. Sie wollen zusammen die Bühne der Royal Albert Halle rocken. Im Jahr 2016 stehen sie vor diesem Ziel nun so kurz bevor. Vorallem deshalb, weil ihr neues Album Rekorde in ganz Europa bricht.</p>',
                            ],
                            [
                                'type' => 'quote',
                                'quote' => 'Ich war schon immer ein leidenschaftlicher Song Schreiber. Mein größter Wunsch ist es die Menschen zu berühren und mit meiner Botschaft ermutigen nach ihren Träumen zu leben.',
                                'quoteReference' => 'Liam Hendrickson',
                            ],
                        ],
                        'structureType' => 'default',
                    ];

                    break;
                case '/artists/coyoos':
                    $pageDataList[] = [
                        'id' => $pageDocument->getUuid(),
                        'locale' => AppFixtures::LOCALE_DE,
                        'title' => 'Coyoos',
                        'url' => '/musiker/coyoos',
                        'parent_path' => '/cmf/demo/contents/artists',
                        'subtitle' => '',
                        'headerImage' => [
                            'id' => $this->getMediaId('coyoos.jpg'),
                        ],
                        'blocks' => [
                            [
                                'type' => 'heading',
                                'heading' => 'Coyoos',
                            ],
                            [
                                'type' => 'text',
                                'text' => '<p>Nach dem release ihres neuen Albums 2012, verbrachte Coyoos mehr als ein Jahr damit, auf den großen Bühnen der riesigen Hallen in den Vereinigten Staaten, ihre Leidenschaft für die Rock Musik zu teilen - und 2015 dann sogar weltweit. In dieser Zeit wuchs die Rockband noch enger zusammen und schrieb ihr drittes Album.</p>',
                            ],
                            [
                                'type' => 'quote',
                                'quote' => 'Neue Orte zu entdecken und inspirierende Leute kennenzulernen sind Erfahrungen, die man nie vergisst. Sie sind die Quelle meiner Kreativität und Inspiration.',
                                'quoteReference' => 'Jack',
                            ],
                            [
                                'type' => 'text',
                                'text' => '<p>2014 startete Jack seine Musikkarriere in San Diego, California. Sein Talent mit der Gitarre lies ihn in kurzer Zeit bekannt werden. Sein großer Traum: In einer Tour durch die Vereinigten Staaten reisen. 2016 ist er so nah an seinem Ziel wie noch nie zuvor - mit seinem neuen Album erreichte er die Spitze der Charts in den Vereinigten Staaten.</p>',
                            ],
                        ],
                        'structureType' => 'default',
                    ];

                    break;
                case '/artists/marshall-plan':
                    $pageDataList[] = [
                        'id' => $pageDocument->getUuid(),
                        'locale' => AppFixtures::LOCALE_DE,
                        'title' => 'Marshall Plan',
                        'url' => '/musiker/marshall-plan',
                        'parent_path' => '/cmf/demo/contents/artists',
                        'subtitle' => '',
                        'headerImage' => [
                            'id' => $this->getMediaId('marshall.jpg'),
                        ],
                        'blocks' => [
                            [
                                'type' => 'heading',
                                'heading' => 'Marshall Plan',
                            ],
                            [
                                'type' => 'text',
                                'text' => '<p>Nach dem Release ihres neuen Albums 2003, verbrachte Civil Literature mehr als ein Jahr damit, auf den großen Bühnen der riesigen Hallen in Großbritanien, ihre Leidenschaft für die Rock Musik zu teilen - und 2015 dann sogar weltweit. In dieser Zeit wuchs die Rockband noch enger zusammen und schrieb ihr zweites Album.</p>',
                            ],
                            [
                                'type' => 'quote',
                                'quote' => 'Wir lieben es, zusammen Musik zu machen und die Menschen um uns herum mit unseren Songs zu inspirieren. Wir kommen aus einem kleinen Dorf in Großbritannien. Es fühlt sich surreal an, dass wir uns einen Namen von Asien bis zu den Staaten gemacht haben.',
                                'quoteReference' => 'Jason Mcconkey',
                            ],
                            [
                                'type' => 'text',
                                'text' => '<p>2003 gründete Alex, der Frontman von Marshall Plan die Band mit seinen besten freunden Albert und Ray, ein in Liverpool bekannter Gitarrenspieler und Songschreiber. 2007 folgte dann Jason. Sein Talent mit dem Bass war genau das richtige für die intensiven Vibes der Band. Sie hatten einen großen Traum zusammen: Die Bühne vor dem Times Square in New York zu rocken. 2016 sind sie so nah an ihrem Ziel wie noch nie zuvor - mit ihrem neuen Album erreichten sie die Spitze der Charts in den Vereinigten Staaten.</p>',
                            ],
                        ],
                        'structureType' => 'default',
                    ];

                    break;
                case '/artists/the-bagpipes':
                    $pageDataList[] = [
                        'id' => $pageDocument->getUuid(),
                        'locale' => AppFixtures::LOCALE_DE,
                        'title' => 'The Bagpipes',
                        'url' => '/musiker/the-bagpipes',
                        'parent_path' => '/cmf/demo/contents/artists',
                        'subtitle' => '',
                        'headerImage' => [
                            'id' => $this->getMediaId('dudelsack.jpg'),
                        ],
                        'blocks' => [
                            [
                                'type' => 'heading',
                                'heading' => 'The Bagpipes',
                            ],
                            [
                                'type' => 'text',
                                'text' => '<p>In den Anfängen haben sich die Bagpipes auf traditionelle und zeitnahe Musik mit ihrem innovativen Flair konzentriert, bevor sie sich dann auf ihre klassische Dudelsackmusik stürtzten. Kurz nach der Veröffentlichung ihres Albums in 1998, haben die Bagpipes mehr als ein Jahr zusammen damit verbracht, ihre Leidenschaft auf die Bühnen und Arenen Schottlands zu bringen - in 2015 dann Weltweit. In dieser Zeit wuchs die Folkband noch enger zusammen und schrieb ihr viertes Album. Sie wurden die Schottische Folkband von den Jahren 2003, 2005 und 2014.</p>',
                            ],
                            [
                                'type' => 'quote',
                                'quote' => 'Unsere Karriere startete auf den Straßen von Glasgow. Es gibt nichts authentischeres als Straßenmusik. Die Menschen mögen dich, oder laufen einfach weiter. Man merkt sofort, wie die Musik ankommt.',
                                'quoteReference' => 'Steve Avril',
                            ],
                        ],
                        'structureType' => 'default',
                    ];

                    break;
                case '/artists/tj-fury':
                    $pageDataList[] = [
                        'id' => $pageDocument->getUuid(),
                        'locale' => AppFixtures::LOCALE_DE,
                        'title' => 'TJ Fury',
                        'url' => '/musiker/tj-fury',
                        'parent_path' => '/cmf/demo/contents/artists',
                        'subtitle' => '',
                        'headerImage' => [
                            'id' => $this->getMediaId('tj-fury.jpg'),
                        ],
                        'blocks' => [
                            [
                                'type' => 'heading',
                                'heading' => 'TJ Fury',
                            ],
                            [
                                'type' => 'text',
                                'text' => '<p>In den Anfängen hat sich TJ Fury auf Kombinationen von zeitnaher Musik und Hip Hop fokusiert. Heute konzentriert er sich auf kraftvolle Texte in der Hip Hop Szene. Nach der Veröffentlichung seines Albums in 2011, hat TJ Fury mehr als ein Jahr damit verbracht seine Leidenschaft für Hip Hop in die Clubs der größen Städte rundum den Staaten zu bringen - in 2015 dann Weltweit. Zu dieser Zeit nahm TJ Fury sein neues Album auf. Bald wurde er für zahlreiche Auszeichnungen nominiert und gewann einige davon.</p>',
                            ],
                            [
                                'type' => 'quote',
                                'quote' => 'Wir lieben es, Musik zu kreieren. Hört euch unsere neuen Tracks an.',
                                'quoteReference' => 'TJ Fury',
                            ],
                        ],
                        'structureType' => 'default',
                    ];

                    break;
                case '/blog':
                    $pageDataList[] = [
                        'id' => $pageDocument->getUuid(),
                        'locale' => AppFixtures::LOCALE_DE,
                        'title' => 'Blog',
                        'url' => '/blog',
                        'subtitle' => 'Erhalten Sie einen Einblick in unsere Arbeit',
                        'headerImage' => [
                            'id' => $this->getMediaId('blog.jpg'),
                        ],
                        'navigationContexts' => ['main'],
                        'structureType' => 'overview',
                        'element' => [
                            [
                                'type' => 'articles',
                                'articles' => [
                                    'sortBy' => 'published',
                                    'sortMethod' => 'asc',
                                ],
                            ],
                        ],
                    ];

                    break;
                case '/about':
                    $pageDataList[] = [
                        'id' => $pageDocument->getUuid(),
                        'locale' => AppFixtures::LOCALE_DE,
                        'title' => 'International Talents',
                        'url' => '/about',
                        'subtitle' => 'Wir arbeiten hart, aber lieben was wir tun',
                        'headerImage' => [
                            'id' => $this->getMediaId('about.png'),
                        ],
                        'blocks' => [
                            [
                                'type' => 'heading',
                                'heading' => 'International Talents',
                            ],
                            [
                                'type' => 'text',
                                'text' => '<h3>International Talents wurde 1998 gegründet.</h3><p>Von Großbritanien aus wuchs International Talents über die ganze Welt zu einer der weltweit führenden Musik Marken.Wie lieben es junge Talente mit all unserem Wissen und Erfahrungen zu begleiten und inspirieren. Mit über 20 Jahren an Musik Aufnahmen, unserer Leidenschaft für die Musik Künstler geht heute weiter. Der Wunsch den Höreren und Fans ins Herz zusprechen ist die Motivation für immer neue kreative Ideen und Strategien des Labels.</p>',
                            ],
                            [
                                'type' => 'quote',
                                'quote' => 'Die ganze Erfahrung aus 20 Jahren und eine Menge Wissen kommen bei International Talents zusammen. Wir lieben was wir tun und kein Tag ist wie der zuvor.',
                                'quoteReference' => 'Jonathan Benett',
                            ],
                            [
                                'type' => 'text',
                                'text' => '<p>Aber alles zusammen wäre nicht möglich, ohne ein Team welches dahinter steht. Dieses Team ist erreichbar rund um die Uhr. Sie bereiten dein Event vor, helfen bei deiner Ausstellung oder Produkt Präsentation. Jeder von Ihnen ist eine lebende Legende in was sie tun. Erfolg passiert nicht einfach so. Er wächst vielmehr mit einem großartigen Team.</p>',
                            ],
                        ],
                        'navigationContexts' => ['main', 'footer'],
                        'structureType' => 'default',
                    ];

                    break;
            }
        }

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
        $homeDocument = $documentManager->find('/cmf/demo/contents', AppFixtures::LOCALE_EN);

        /** @var BasePageDocument $aboutDocument */
        $aboutDocument = $documentManager->find('/cmf/demo/contents/about-us', AppFixtures::LOCALE_EN);

        /** @var BasePageDocument $headerTeaserDocument */
        $headerTeaserDocument = $documentManager->find('/cmf/demo/contents/artists/coyoos', AppFixtures::LOCALE_EN);

        $homeDocument->getStructure()->bind(
            [
                'locale' => AppFixtures::LOCALE_EN,
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

        $documentManager->persist($homeDocument, AppFixtures::LOCALE_EN);
        $documentManager->publish($homeDocument, AppFixtures::LOCALE_EN);
    }

    /**
     * @throws MetadataNotFoundException
     *
     * @return mixed[]
     */
    private function loadArticles(DocumentManager $documentManager): array
    {
        $articleDataList = [
            [
                'locale' => AppFixtures::LOCALE_EN,
                'title' => 'A great song will win',
                'excerpt' => [
                    'title' => 'A great song will win',
                    'description' => '<p>We got the chance to talk to the Head of International Talents Jonathan Bennett. We talked about his career highlights and his advice for the artists.</p>',
                    'images' => [
                        'ids' => [$this->getMediaId('mic.jpg')],
                    ],
                ],
                'headerImage' => [
                    'id' => $this->getMediaId('mic.jpg'),
                ],
                'structureType' => 'blog',
                'blocks' => [
                    [
                        'type' => 'text',
                        'text' => '<h3>A great song will win in the end</h3><p>For 50 years he is working with the greatest artists in the world. His projects have sold more than 50 million tracks worldwide. Usually he have no time but we got the chance to talk to the Head of International Talents Jonathan Bennett. We talked about his career highlights and his advice for the artists.</p><p>In the beginning Jonathan have been working for several different recording agencies. One Day (18 years ago) Calvin Merrit called me and asked if I would be interested in working as an International Talents consultant on the newcomer project Marshall Plan from the UK. So I decided to do that and hat an amazing time together wit the band. The band became famous in just a few years and so Calvin asked me if I would join International Talents as Head of the agency in August 1999.</p><h3>Advice for artists who want to get discovered</h3><p>In my opinion I think the most important thing is to work on a long time developing a strong live show. When I remember back to Marschall Plan, the reason why we prepared that long time, was there live show. We reflected, invented, tried and rehearsed so I never mattered about there first album debut. They were good playing there concerts live - Everything.</p><p>It was the same with Civil Literature. When I saw them playing the first time. I got the feeling that they will become one of the biggest bands in the world, if they would upgrade there live acts. There was so much potential on the stage.</p><p>So when I worked with those bands together we spent most of the time into their live shows, The other thing I learned over the years, the bands really should work on there songwriting focus. Because this one song have to breakthrough a millions of songs and should be remembered by the fans and people out there.</p><p>Strong lines is what the people love.</p>',
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
            ],
            [
                'locale' => AppFixtures::LOCALE_EN,
                'title' => 'A week on the road with Civil Literature',
                'excerpt' => [
                    'title' => 'A week on the road with Civil Literature',
                    'description' => '<p>Two month ago Civil Literature launched their new album "Civil War". Now they are on tour for one week and they have already played half a dozend concerts.</p>',
                    'images' => [
                        'ids' => [$this->getMediaId('roadtrip.jpg')],
                    ],
                ],
                'headerImage' => [
                    'id' => $this->getMediaId('roadtrip.jpg'),
                ],
                'structureType' => 'blog',
                'blocks' => [
                    [
                        'type' => 'text',
                        'text' => '<h3>A week on the road with Civil Literature</h3><p>It took them three years but now it\'s finally here. Two month ago Civil Literature launched their new album "Civil War". Now they are on tour for one week and they have already played half a dozend concerts.</p><p>You\'re watching and thinking, "the show\'s true magic and their performace is amazing", that\'s how famous music critic Joe Zipotta descriped the show in Amsterdam. "It seems like in the last three year the band has improved a lot and wrote some awesome songs. Especially the frontman Liam was fascinating, he managed it to take the visitors in a other world. It was truely terrific."</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'The first week is over and is\'s so great! Germany and the Netherlands, Berlin and Amsterdam, tour live is awesome!',
                        'quoteReference' => 'Liam Mercx',
                    ],
                    [
                        'type' => 'text',
                        'text' => '<p>Civil Literatures concerts in Germany and the Netherlands are already over, but there will be a lot of other opportunities to see them and hear their extra ordanary new songs. The next stops are Paris and London. After Europe Civil Literature will then play in America and Asia.</p>',
                    ],
                    [
                        'type' => 'similar-articles',
                    ],
                ],
            ],
            [
                'locale' => AppFixtures::LOCALE_EN,
                'title' => 'Behind the scenes of our creative directors',
                'excerpt' => [
                    'title' => 'Behind the scenes of our creative directors',
                    'description' => '<p>As the people working at International Talents it is our job to help our costumers to create something you will love.</p>',
                    'images' => [
                        'ids' => [$this->getMediaId('meeting.jpg')],
                    ],
                ],
                'headerImage' => [
                    'id' => $this->getMediaId('meeting.jpg'),
                ],
                'structureType' => 'blog',
                'blocks' => [
                    [
                        'type' => 'text',
                        'text' => '<h3>Behind the scenes of our creative directors</h3><p>As a creative director every new das is a challange, but it\'s one that I like to take up. I know what our costumers expect from us and so I can create something that they will love. For me that\'s the real deal.</p><p>Some times I find myself sitting in an armchair and enjoying the silence, that\'s when I get my greatest ideas. Working on new songs with our artists is challenging, especially since every one of them need something else from us. Some want us to help them find new ideas for songs and others only want us to help them give their songs the final touch. So every day is a new callange and I am ony able to manage this with the help of our excelent team.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'Music lies deep within our soul, only because of this we can create something that is pure magic.',
                        'quoteReference' => 'Joe Armson',
                    ],
                    [
                        'type' => 'text',
                        'text' => '<p>As the people working at International Talents it is our job to help our costumers to create something you will love. In order to achieve this we need to live not just our pilosophy but also our customers. Thats a huge challange but we use all our energy to achive this.</p><p>One of the hardest things about this job is that although we have to support our artists we also need to make sure they have enougth freedom to try out something knew, even if we might not think a idea is not so great at the beginning. If we would have strict standard the artists would never be able to create these magic songs that they do.</p>',
                    ],
                    [
                        'type' => 'similar-articles',
                    ],
                ],
            ],
            [
                'locale' => AppFixtures::LOCALE_EN,
                'title' => 'Drop Big Beats',
                'excerpt' => [
                    'title' => 'Drop Big Beats',
                    'description' => '<p>Charlotte Merana shares her advice for ambitious DJs and electronic musicans.</p>',
                    'images' => [
                        'ids' => [$this->getMediaId('tj-fury.jpg')],
                    ],
                ],
                'headerImage' => [
                    'id' => $this->getMediaId('tj-fury.jpg'),
                ],
                'structureType' => 'blog',
                'blocks' => [
                    [
                        'type' => 'text',
                        'text' => '<h3>Drop big Beats</h3><p>Her finger on the pulse of dance and electronic music. Usually she is not listening to the music filled up with crazy beats. But today she shared her advice for ambitious DJs and electronic musicans. Her name is Charlotte Merana and she is the general Manager of the big beats of International Talents.</p>',
                    ],
                    [
                        'type' => 'quote',
                        'quote' => 'I\'m so excited about what will come next - where will the trend blaze the trail.',
                        'quoteReference' => 'Charlotte Merena',
                    ],
                    [
                        'type' => 'text',
                        'text' => '<p>Charlotte explained, she never predicted it would get this big. In her thoughts and visions she hoped for it. But if she shared her dreams 30 years earlier, the people would laugh about her. Today you can\'t believe it. Kids loving this music. Not all of them are quite kids but for example a booking agency from Berlin in Germany signed a 14-year-old  DJ. It\'s really exciting for them, but also for me. She always believed in this subculture, but never predicted that it would get this big.</p><h3>Charlotte also shared her advice for younger artist who wants to get the attention of the people.</h3><p>She told us to beginn with your friends first. Do what you know, what you already learned. Show the people what you can do and win them. Bring the people to support you. Start throwing your party and grow up a network of people who like what you do and are excited about what you do. Don\'t make a fanbpage, and put your sounds on Soundcloud or do a crazy Photoshooing.</p>',
                    ],
                    [
                        'type' => 'similar-articles',
                    ],
                ],
            ],
            [
                'locale' => AppFixtures::LOCALE_EN,
                'title' => 'Legend behind the Mix',
                'excerpt' => [
                    'title' => 'Legend behind the Mix',
                    'description' => '<p>We got the oppurtunity to sit down with our legendary record producer and mix master James McMorrison.</p>',
                    'images' => [
                        'ids' => [$this->getMediaId('sound.jpg')],
                    ],
                ],
                'headerImage' => [
                    'id' => $this->getMediaId('sound.jpg'),
                ],
                'structureType' => 'blog',
                'blocks' => [
                    [
                        'type' => 'text',
                        'text' => '<h3>Legend behind the Mix</h3><p>Ever thought how your most loved songs are made? Ever thought what process a song have to pass before the radio plays or you can buy the vinyl? This trial of producing music is real art, and in the background there are a lot of talented technicans working behind the scenes to achieve the tracks we love and enjoy and dance.</p><p>We are enthusiastic about those technican engineers and got the oppurtunity to sit down with our legendary record producer and mix master James McMorrison. Together we talked about his growing experience over the years. The fast changing audio industry, his work with the band Civil Literature and what each sound board operator needs, to become a legend in recording.</p>',
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

        $articles = [];

        foreach ($articleDataList as $articleData) {
            $article = $this->createArticle($documentManager, $articleData);

            $articles[$article->getRoutePath()] = array_merge($articleData, [
                'id' => $article->getId(),
            ]);
        }

        return $articles;
    }

    /**
     * @param mixed[] $articles
     *
     * @throws MetadataNotFoundException
     */
    private function loadArticlesGerman(DocumentManager $documentManager, array $articles): void
    {
        $articleDataList = [];

        /**
         * @var string
         * @var array $articleDocument
         */
        foreach ($articles as $url => $articleDocument) {
            switch ($url) {
                case '/blog/a-great-song-will-win':
                    $articleDataList[] = array_merge($articleDocument, [
                        'locale' => AppFixtures::LOCALE_DE,
                        'title' => 'Ein Guter Song wird immer gewinnen',
                        'excerpt' => array_merge($articleDocument['excerpt'], [
                            'title' => 'Ein Guter Song wird immer gewinnen',
                            'description' => '<p>Wir haben die Möglichkeit ergriffen, mit dem Mann, der ganz oben bei International Talents steht ein Gespräch zu führen.</p>',
                        ]),
                        'blocks' => [
                            [
                                'type' => 'text',
                                'text' => '<h3>Ein Guter Song wird immer gewinnen</h3><p>Er arbeitet schon seit über 50 Jahren mit den besten Künstlern der Welt. Seine Projekte haben weltweit schon über 50 Millionen Platten verkauft. Normalerweise hat er nicht viel Zeit, doch wir haben die Chance erhalten, ein Gespräch mit dem Mann, der ganz oben bei International Talents steht zu führen, Jonathan Bennet. Wir haben über die Highlights seiner Karriere und über Ratschläge für Künstler geredet.</p><p>In den Anfängen hat Jonathan für viele verschiedene Plattenfirmen gearbeitet. Eines Tages (vor 18 Jahren) hat mich Clavin Merrit angerufen und gefragt ob daran interessiert wäre, bei International Talents als Berater der jungen Band Marshall Plan aus Großbritannien zu arbeiten. Ich habe mich dafür entschieden und hatte eine unglaubliche Zeit zusammen mit der Band. Die Künstler wurden in nur wenigen Jahren berühmt, deswegen fragte mich Calvin 1999 ob ich International Talents als Kopf der Agentur beitreten möchte.</p><h3>Ratschläge für Künstler, die entdeckt werden wollen</h3><p>Meiner Meinung nach ist das wichtigste, hart daran zu arbeiten, eine starke Live-show zu bieten. Wenn ich an Marshall Plan zurückdenke, war der Grund für die lange Vorbereitungsphase ihre Live-show. Wir haben reflektiert, geprobt und neue dinge ausprobiert. Mich kümmerte das erste Album weniger als ihre Live-show. Sie waren gut darin, ihre Konzerte live zu spielen.</p><p>Mit Civil Literature war es sehr ähnlich. Als ich sie zum ersten mal spielen sah, bekam ich das Gefühl, dass sie eines Tages eine der berühmtesten Bands weltweit sein werden, wenn sie ihre Live- Auftritte verbessern. Es gab so viel Potential auf der Bühne.</p><p>Als ich mit diesen Bands zusammenarbeitete, haben wir die meiste Zeit damit verbracht, ihre Live- Auftritte zu verbessern. Die andere Sache, die ich über die Jahre mitgenomme habe, ist, dass die Bands wirklich an ihren Songtexten arbeiten sollten. Dieser eine Song, der mit seinem Text an millionen von anderen Songs vorbeizieht und von den Fans niemals vergessen wird.</p><p>Starke Texte sind das, was die Leute lieben.</p>',
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
                    ]);

                    break;
                case '/blog/a-week-on-the-road-with-civil-literature':
                    $articleDataList[] = array_merge($articleDocument, [
                        'locale' => AppFixtures::LOCALE_DE,
                        'title' => 'Eine Woche unterwegs mit Civil Literature',
                        'excerpt' => array_merge($articleDocument['excerpt'], [
                            'title' => 'Eine Woche unterwegs mit Civil Literature',
                            'description' => '<p>Vor zwei Monaten hat Civil Literature ihr neues Album "Civil War" veröffentlicht. Nun sind sie für eine Woche auf Tour und bis jetzt haben sie schon ein halbes Duzent Konzerte gegeben.</p>',
                        ]),
                        'blocks' => [
                            [
                                'type' => 'text',
                                'text' => '<h3>Eine Woche unterwegs mit Civil Literature</h3><p>Es hat sie drei Jahre Arbeit gekostet, doch nun ist es endlich da. Vor zwei Monaten hat Civil Literature ihr neues Album "Civil War" veröffentlicht. Nun sind sie für eine Woche auf Tour und bis jetzt haben sie schon ein halbes Duzent Konzerte gegeben.</p><p>Du siehst einfach nur zu und denkst dir, "Die Show ist echte Magie und ihre Performance ist atemberaubend", So hat der bekannte Musikkritiker Joe Zipotta die show in Amsterdam beschrieben. "Es scheint als hätte sich die Band in den letzten drei Jahren sehr verbessert und haben einige geniale Songs geschrieben. Vorallem der Frontman Liam war faszinierend, er hat es geschafft, die besucher in eine andere Welt zu versetzen. Es war wirklich großartig."</p>',
                            ],
                            [
                                'type' => 'quote',
                                'quote' => 'Wir haben die erste Woche hinter uns und es ist so toll! Deutschland und die Niederlande, Berlin und Amsterdam, das Tourleben ist genial!',
                                'quoteReference' => 'Liam Mercx',
                            ],
                            [
                                'type' => 'text',
                                'text' => '<p>Die Konzerte in Deutschland und den Niederlanden sind schon vorbei, doch es wird in Zukunft noch viele andere möglichkeiten geben, sie zu sehen und ihre neuen extraordinären Songs zu erleben. Der nächsten Stops sind Paris und London. Nach der Europatour werden Civil Literatur in Amerika und Asien spielen.</p>',
                            ],
                            [
                                'type' => 'similar-articles',
                            ],
                        ],
                    ]);

                    break;
                case '/blog/behind-the-scenes-of-our-creative-directors':
                    $articleDataList[] = array_merge($articleDocument, [
                        'locale' => AppFixtures::LOCALE_DE,
                        'title' => 'Hinter den Kulissen unserer creative Directors',
                        'excerpt' => array_merge($articleDocument['excerpt'], [
                            'title' => 'Hinter den Kulissen unserer creative Directors',
                            'description' => '<p>Als Mitarbeiter bei International Talents ist es unsere Aufgabe unseren Kunden zu helfen, damit sie etwas erstellen können, dass ihre Fans lieben werden.</p>',
                        ]),
                        'blocks' => [
                            [
                                'type' => 'text',
                                'text' => '<h3>Hinter den Kulissen unserer creative Directors</h3><p>Als creative Director ist jeder neuer Auftrag eine Herausforderung, doch ist es eine, die ich gerne auf mich nehme. Ich weiß was unsere Kunden von uns erwarten, somit kann ich etwas kreieren, dass sie lieben werden, was für mich sehr wichtig ist.</p><p>Manchmal erwische ich mich selbst dabei, wie ich in meinem Sessel sitze und die Stille genieße, denn dort fallen mir die besten Ideen ein. Das Arbeiten an neuen Songs mit unseren Künstlern ist eine Herausforderung, da jeder einzelne von Ihnen etwas anderes von uns benötigt. Einigen helfen wir dabei, neue Ideen für ihre Musik zu finden, andere benötigen nur noch den letzten Feinschliff für ihre Songs. Somit ist jeder Tag eine neue Herausforderung. Doch ich würde das Ganze niemals ohne die Hilfe unseres hervorragenden Teams schaffen.</p>',
                            ],
                            [
                                'type' => 'quote',
                                'quote' => 'Die Musik liegt tief in unseren Seelen, nur deshalb können wir etwas kreieren, dass purer Magie gleicht.',
                                'quoteReference' => 'Joe Armson',
                            ],
                            [
                                'type' => 'text',
                                'text' => '<p>Als Mitarbeiter bei International Talents ist es unsere Aufgabe unseren Kunden zu helfen, damit sie etwas erstellen können, dass ihre Fans lieben werden. Um dass zu ermöglichen müssen wir nicht nur unsere Philosophie leben, sondern auch unsere Kunden. Das ist eine rießige Herausforderung und wir verwenden all unsere Energie um genau das zu erreichen.</p><p>Einer der härtesten Aspekte des Jobs ist, obwohl wir unsere Künstler unterstützen müssen, sollten wir trotzdem darauf achten, dass sie genug Freiraum haben, neue Dinge auszuprobieren, auch wenn wir am Anfang vielleicht die Idee kritisch betrachten. Hätten wir strikte Standards und Vorgaben für unsere Künstler, würden sie nie in die Lage kommen, ihre magischen Werke zu kreieren.</p>',
                            ],
                            [
                                'type' => 'similar-articles',
                            ],
                        ],
                    ]);

                    break;
                case '/blog/drop-big-beats':
                    $articleDataList[] = array_merge($articleDocument, [
                        'locale' => AppFixtures::LOCALE_DE,
                        'title' => 'Fette Beats',
                        'excerpt' => array_merge($articleDocument['excerpt'], [
                            'title' => 'Fette Beats',
                            'description' => '<p>Charlotte Merena teilt ihren Ratschlag für ehrgeizige DJs und Künstler, die in der elektronischen Musikbranche tätig sind.</p>',
                        ]),
                        'blocks' => [
                            [
                                'type' => 'text',
                                'text' => '<h3>Fette Beats</h3><p>Mit ihrem finger am Puls von Dance und elektonischer Musik. Normalerweise hört sie keine Musik, die gefüllt mit verrückten und ausgefallenen Beats ist. Doch heute teilt sie ihren Ratschlag für ehrgeizige DJs und Künstler, die in der elektronischen Musikbranche tätig sind. Ihr Name ist Charlotte Merena, general Manager der fettesten beats bei international Talents.</p>',
                            ],
                            [
                                'type' => 'quote',
                                'quote' => 'Ich bin so aufgeregt, was als nächstes kommt und welchen Pfad der Trend nehmen wird.',
                                'quoteReference' => 'Charlotte Merena',
                            ],
                            [
                                'type' => 'text',
                                'text' => '<p>Charlotte erklärt, dass sie niemals erwartet hätte, dass die Sache so explodieren würde. In ihren Gedanken und Visionen hoffte sie darauf, hätte sie ihre Träume jedoch 30 Jahre früher geteilt, wäre sie höchstwahrscheinlich ausgelacht worden. Heute kann man es kaum glauben: vorallem die jüngere Generation liebt diese Art von Musik. Gerade erst hat eine Buchungsagentur aus Berlin einen 14 Jahre alten DJ unter die fittiche genommen. "Es ist nicht nur aufregend für sie, sondern auch für mich", erklärt Charlotte. Sie hatte immer Hoffnung in diese Subkultur, jedoch ist das Ausmaß, welche sie angenommen hat, unhervorsehbar gewesen.</p><h3>Charlotte teilte mit uns auch ihren Ratschlag, insbesondere für jüngere Künstler, die sich einen Namen in der Szene machen wollen.</h3><p>Tu dass, was du schon gelernt hast, und zeige den Leuten was du kannst um sie zu überzeugen. Sie erklärte, dass es eine gute Idee ist, zuerst mit den eigenen Freunden anzufangen. Versuche ein Netzwerk von Leuten zu bilden, die mögen was du machst und die selber Gefallen in dem was du machst finden. Erstelle keine Fanpage, stelle deine Werke nicht auf Soundcloud und mach keine verrückten Photoshootings.</p>',
                            ],
                            [
                                'type' => 'similar-articles',
                            ],
                        ],
                    ]);

                    break;
                case '/blog/legend-behind-the-mix':
                    $articleDataList[] = array_merge($articleDocument, [
                        'locale' => AppFixtures::LOCALE_DE,
                        'title' => 'Eine Legende hinter dem Mischpult',
                        'excerpt' => array_merge($articleDocument['excerpt'], [
                            'title' => 'Eine Legende hinter dem Mischpult',
                            'description' => '<p>Wir haben die Chance bekommen, ein Gespräch mit dem legendären Musikproduzenten und Mix-Master James McMorrison zu führen.</p>',
                        ]),
                        'blocks' => [
                            [
                                'type' => 'text',
                                'text' => '<h3>Eine Legende hinter dem Mischpult</h3><p>Hast du jemals darüber nachgedacht, wie deine lieblingssongs erstellt werden? Hast du dir schon einmal Gedanken darüber gemacht, welcher Prozess ein Song durchmachen muss, bevor er Radiotauglich ist oder auf Schallplatte erhältlich ist? Musik zu Produzieren ist echte Kunst und im Hintergrund arbeiten viele talentierte Techniker hinter den Kulissen um die Tracks zu erschaffen, zu denen wir alle tanzen können.</p><p>Wir teilen unsere Beigeisterung mit diesen Ingenieuren und haben die Chance bekommen, ein Gespräch mit dem legendären Musikproduzenten und Mix-Master James McMorrison zu führen. Wir haben über seine Jahrelange und ständig wachsende Erfahrung in der Szene, die rapiden Änderungen des Sounds in der Audioindustrie, seine Zusammenarbeit mit der Band Civil Literatur und was jeder Sound-board Bediener braucht, um eine Legende in der Audio-Aufnahme zu werden geredet.</p>',
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
                    ]);

                    break;
            }
        }

        foreach ($articleDataList as $articleData) {
            $this->createArticle($documentManager, $articleData);
        }
    }

    /**
     * @throws DocumentManagerException
     */
    private function loadHomepageGerman(DocumentManager $documentManager): void
    {
        $documentManager->clear();

        /** @var HomeDocument $homeDocument */
        $homeDocument = $documentManager->find('/cmf/demo/contents', AppFixtures::LOCALE_DE);

        /** @var BasePageDocument $aboutDocument */
        $aboutDocument = $documentManager->find('/cmf/demo/contents/about-us', AppFixtures::LOCALE_DE);

        /** @var BasePageDocument $headerTeaserDocument */
        $headerTeaserDocument = $documentManager->find('/cmf/demo/contents/artists/coyoos', AppFixtures::LOCALE_DE);

        $homeDocument->getStructure()->bind(
            [
                'locale' => AppFixtures::LOCALE_DE,
                'title' => $homeDocument->getTitle(),
                'url' => '/',
                'teaser' => $headerTeaserDocument->getUuid(),
                'blocks' => [
                    [
                        'type' => 'heading',
                        'heading' => 'Unser Label',
                    ],
                    [
                        'type' => 'text',
                        'text' => '<h3>International Talents wurde 1998 gegründet</h3><p>Von Großbritanien aus wuchs International Talents über die ganze Welt zu einer der weltweit führenden Musik Marken.Wie lieben es junge Talente mit all unserem Wissen und Erfahrungen zu begleiten und inspirieren. Mit über 20 Jahren an Musik Aufnahmen, unserer Leidenschaft für die Musik Künstler geht heute weiter. Der Wunsch den Höreren und Fans ins Herz zusprechen ist die Motivation für immer neue kreative Ideen und Strategien des Labels.</p>',
                    ],
                    [
                        'type' => 'link',
                        'page' => $aboutDocument->getUuid(),
                        'text' => 'MEHR LESEN',
                    ],
                ],
            ]
        );

        $documentManager->persist($homeDocument, AppFixtures::LOCALE_DE);
        $documentManager->publish($homeDocument, AppFixtures::LOCALE_DE);
    }

    /**
     * @throws Exception
     */
    private function loadContactSnippet(DocumentManager $documentManager): SnippetDocument
    {
        $data = [
            'locale' => AppFixtures::LOCALE_EN,
            'title' => 'Z',
            'contact' => [
                'id' => 1,
            ],
        ];

        $snippetDocument = $this->createSnippet($documentManager, 'contact', $data);

        $this->getDefaultSnippetManager()->save('demo', 'contact', $snippetDocument->getUuid(), AppFixtures::LOCALE_EN);

        return $snippetDocument;
    }

    /**
     * @throws Exception
     */
    private function loadContactSnippetGerman(DocumentManager $documentManager, SnippetDocument $snippetDocument): void
    {
        $data = [
            'id' => $snippetDocument->getUuid(),
            'locale' => AppFixtures::LOCALE_DE,
            'title' => 'Z',
            'contact' => [
                'id' => 1,
            ],
        ];

        $snippetDocument = $this->createSnippet($documentManager, 'contact', $data);

        $this->getDefaultSnippetManager()->save('demo', 'contact', $snippetDocument->getUuid(), AppFixtures::LOCALE_DE);
    }

    /**
     * @param mixed[] $data
     *
     * @throws MetadataNotFoundException
     */
    private function createSnippet(DocumentManager $documentManager, string $structureType, array $data): SnippetDocument
    {
        $locale = isset($data['locale']) && $data['locale'] ? $data['locale'] : AppFixtures::LOCALE_EN;

        /** @var SnippetDocument $snippetDocument */
        $snippetDocument = null;

        try {
            if (!isset($data['id']) || !$data['id']) {
                throw new OutOfBoundsException();
            }

            /** @var SnippetDocument $snippetDocument */
            $snippetDocument = $documentManager->find($data['id'], $locale);
        } catch (DocumentManagerException | OutOfBoundsException $e) {
            /** @var SnippetDocument $snippetDocument */
            $snippetDocument = $documentManager->create('snippet');
        }

        $snippetDocument->getUuid();
        $snippetDocument->setLocale($locale);
        $snippetDocument->setTitle($data['title']);
        $snippetDocument->setStructureType($structureType);
        $snippetDocument->setWorkflowStage(WorkflowStage::PUBLISHED);
        $snippetDocument->getStructure()->bind($data);

        $documentManager->persist($snippetDocument, $locale, ['parent_path' => '/cmf/snippets']);
        $documentManager->publish($snippetDocument, $locale);

        return $snippetDocument;
    }

    /**
     * @throws DocumentManagerException
     */
    private function updatePages(DocumentManager $documentManager, string $locale): void
    {
        /** @var BasePageDocument $artistsDocument */
        $artistsDocument = $documentManager->find('/cmf/demo/contents/artists', $locale);

        $data = $artistsDocument->getStructure()->toArray();

        if (!isset($data['element'])) {
            return;
        }

        $data['element'] = array_map(function (array $element) use ($artistsDocument): array {
            if ('pages' === $element['type']) {
                $element['pages']['dataSource'] = $artistsDocument->getUuid();
            }

            return $element;
        }, $data['element']);

        $artistsDocument->getStructure()->bind($data);

        $documentManager->persist($artistsDocument, $locale);
        $documentManager->publish($artistsDocument, $locale);
    }

    /**
     * @param mixed[] $data
     *
     * @throws MetadataNotFoundException
     */
    private function createPage(DocumentManager $documentManager, array $data): PageDocument
    {
        $locale = $data['locale'] ?? AppFixtures::LOCALE_EN;

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
        $pageDocument = null;

        try {
            if (!isset($data['id']) || !$data['id']) {
                throw new OutOfBoundsException();
            }

            /** @var PageDocument $pageDocument */
            $pageDocument = $documentManager->find($data['id'], $locale);
        } catch (DocumentManagerException | OutOfBoundsException $e) {
            /** @var PageDocument $pageDocument */
            $pageDocument = $documentManager->create('page');
        }

        $pageDocument->setNavigationContexts($data['navigationContexts'] ?? []);
        $pageDocument->setLocale($locale);
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
            $locale,
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
                $locale,
                ['parent_path' => $data['parent_path'] ?? '/cmf/demo/contents']
            );
        }

        $documentManager->publish($pageDocument, $locale);

        return $pageDocument;
    }

    /**
     * @param mixed[] $data
     *
     * @throws MetadataNotFoundException
     */
    private function createArticle(DocumentManager $documentManager, array $data): ArticleDocument
    {
        $locale = $data['locale'] ?? AppFixtures::LOCALE_EN;

        $extensionData = [
            'seo' => $data['seo'] ?? [],
            'excerpt' => $data['excerpt'] ?? [],
        ];

        unset($data['excerpt']);
        unset($data['seo']);

        $articleDocument = null;

        try {
            if (!isset($data['id']) || !$data['id']) {
                throw new OutOfBoundsException();
            }

            /** @var ArticleDocument $articleDocument */
            $articleDocument = $documentManager->find($data['id'], $locale, ['load_ghost_content' => false]);
        } catch (DocumentManagerException | OutOfBoundsException $e) {
            /** @var ArticleDocument $articleDocument */
            $articleDocument = $documentManager->create('article');
        }

        $articleDocument->setLocale($locale);
        $articleDocument->setTitle($data['title']);
        $articleDocument->setStructureType($data['structureType'] ?? 'blog');
        $articleDocument->setWorkflowStage(WorkflowStage::PUBLISHED);
        $articleDocument->getStructure()->bind($data);
        $articleDocument->setAuthor(1);
        $articleDocument->setExtensionsData($extensionData);

        $documentManager->persist($articleDocument, $locale);
        $documentManager->publish($articleDocument, $locale);

        return $articleDocument;
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
