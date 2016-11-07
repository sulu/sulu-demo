<?php

namespace AppBundle\Controller;

use Sulu\Bundle\ContentBundle\Document\PageDocument;
use Sulu\Bundle\WebsiteBundle\Controller\WebsiteController;
use Sulu\Component\Content\Compat\StructureInterface;
use Sulu\Component\Content\Repository\Mapping\Mapping;
use Sulu\Component\Content\Repository\Mapping\MappingBuilder;
use Sulu\Component\Util\SortUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Uuid;

class BlogController extends WebsiteController
{
    /**
     * Loads the content from the request (filled by the route provider) and creates a response with this content and
     * the appropriate cache headers.
     *
     * @param \Sulu\Component\Content\Compat\StructureInterface $structure
     * @param bool $preview
     * @param bool $partial
     *
     * @return Response
     */
    public function detailAction(StructureInterface $structure, $preview = false, $partial = false)
    {
        /** @var PageDocument $document */
        $uuid = $structure->getUuid();
        $document = $this->get('sulu_document_manager.document_manager')
            ->find($uuid, $structure->getLanguageCode());

        $parent = $document->getParent();
        $articles = [];

        foreach ($parent->getChildren() as $item) {
            if ($item->getStructureType() == "blog_detail") {
                array_push($articles, $item);
            }
        }

        /** @var PageDocument[] $articles */
        $articles = SortUtils::multisort($articles, 'created');

        $response = $this->renderStructure(
            $structure,
            [
                'lastAndPrevArticle' => $this->createDetailArticles(
                    $this->getPrevAndNextArticle($articles, $uuid)
                ),
                'latestArticles' => $this->createDetailArticles(
                    $this->getLatestArticle($articles, 4, $uuid)
                ),
                'link' => $document->getParent()->getResourceSegment()

            ],
            $preview,
            $partial
        );

        return $response;
    }

    /**
     * Loads the content from the request (filled by the route provider) and creates a response with this content and
     * the appropriate cache headers.
     *
     * @param \Sulu\Component\Content\Compat\StructureInterface $structure
     * @param bool $preview
     * @param bool $partial
     *
     * @return Response
     */
    public function overviewAction(StructureInterface $structure, $preview = false, $partial = false)
    {
        $latestArticles = [];
        $link = '';

        if (!empty($structure->getDocument()->getChildren())) {
            $children = $structure->getChildren();
            $articlelist = SortUtils::multisort($children, 'created');
            $articles = [];
            $link = $structure->getPropertiesByTagName("sulu.rlp")[0]->getValue();

            for ($i = 0; $i <= count($articlelist) -1; $i++) {
                $articles[$i] = $articlelist[$i]->getDocument();
            }

            $latestArticles = $this->createOverviewArticles(
                $this->getLatestArticle($articles, 4, $structure->getUuid())
            );
        }

        $response = $this->renderStructure(
            $structure,
            [
                'latestArticles' => $latestArticles,
                'link' => $link
            ],
            $preview,
            $partial
        );

        return $response;
    }

    /**
     * Takes all the articles and returns the lastest ones.
     *
     * @param array $articles
     * @param int $numKind
     *
     * @return array
     */
    public function getLatestArticle($articles, $numKind, $uuid)
    {
        $lastarticles = [];
        $index = -1;

        for ($i = 0; $i <= count($articles) - 1; $i++) {
            if ($articles[$i]->getUuid() == $uuid) {
                $index = $i;
            }
        }

        if ($index != -1) {
            array_splice($articles, $index, 1);
        }

        for ($i = 0; $i <= ((count($articles) >= $numKind) ? $numKind -1 : count($articles) -1); $i++) {
            $lastarticles[$i] = $articles[$i];
        }

        return array_reverse($lastarticles);
    }

    /**
     * Looks for the articles before and after the one with the uuid.
     *
     * @param array $articles
     * @param string $uuid
     *
     * @return array
     */
    public function getPrevAndNextArticle($articles, $uuid)
    {
        $result = [];

        for ($i = 0; $i <= count($articles) - 1; $i++) {
            if ($articles[$i]->getUuid() == $uuid) {
                if (array_key_exists($i - 1, $articles) && array_key_exists($i + 1, $articles)) {
                    $result = ['prev' => $articles[$i - 1], 'next' => $articles[$i + 1]];
                } elseif (array_key_exists($i - 1, $articles)) {
                    $result = ['prev' => $articles[$i - 1]];
                } elseif (array_key_exists($i + 1, $articles)) {
                    $result = ['next' => $articles[$i + 1]];
                }
            }
        }

        return $result;
    }

    /**
     * Formats the array for the Response.
     *
     * @var PageDocument[] $article
     *
     * @return array
     */
    public function createDetailArticles($article)
    {
        $result = [];

        foreach ($article as $key => $item) {
            $structure = $item->getStructure();
            $image = null;
            $url = null;
            $heading = null;
            $creation = $creation = $item->getChanged();

            if ($structure->hasProperty('headerImage')) {
                $image = $structure->getProperty('headerImage');
            }

            if ($structure->hasProperty('url')) {
                $url = $structure->getProperty('url');
            }

            if ($structure->hasProperty('title')) {
                $heading = $structure->getProperty('title');
            }

            $result[$key] = [
                'image' => $image,
                'url' => $url,
                'heading' => $heading,
                'creation' => $creation
            ];
        }

        return $result;
    }

    /**
     * Formats the array for the Response.
     *
     * @var array $article
     *
     * @return array
     */
    public function createOverviewArticles($article)
    {
        $result = [];

        foreach ($article as $key => $item) {
            $structure = $item->getStructure();

            $result[$key] = [
                'image' => $structure->getProperty('headerImage'),
                'url' => $structure->getProperty('url'),
                'heading' => $structure->getProperty('title'),
                'creation' => $item->getChanged()
            ];
        }

        return $result;
    }
}
