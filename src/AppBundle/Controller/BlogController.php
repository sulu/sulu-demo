<?php

namespace AppBundle\Controller;

use Sulu\Bundle\ContentBundle\Document\PageDocument;
use Sulu\Bundle\WebsiteBundle\Controller\WebsiteController;
use Sulu\Component\Content\Compat\StructureInterface;
use Sulu\Component\Content\Document\LocalizationState;
use Sulu\Component\Content\Document\WorkflowStage;
use Sulu\Component\Util\SortUtils;
use Symfony\Component\HttpFoundation\Response;

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

        foreach ($this->getChildren($parent) as $item) {
            if ($item->getStructureType() == "blog_detail") {
                array_push($articles, $item);
            }
        }

        /** @var PageDocument[] $articles */
        $articles = SortUtils::multisort($articles, 'created');

        $response = $this->renderStructure(
            $structure,
            [
                'latestArticles' => $this->createDetailArticles($this->getLatestArticle($articles, 4, $uuid)),
                'link' => $document->getParent()->getResourceSegment(),
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

        if ($children = $this->getChildren($structure->getDocument())) {
            $link = $structure->getPropertiesByTagName("sulu.rlp")[0]->getValue();
            $latestArticles = $this->createOverviewArticles(
                $this->getLatestArticle($children, 4, $structure->getUuid())
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
     * Takes all the articles and returns the latest ones.
     *
     * @param array $articles
     * @param int $numKind
     * @param string $uuid
     *
     * @return array
     */
    public function getLatestArticle($articles, $numKind, $uuid)
    {
        $latestArticles = [];
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
            $latestArticles[$i] = $articles[$i];
        }

        return array_reverse($latestArticles);
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

    /**
     * @param PageDocument $parentDocument
     *
     * @return array
     */
    protected function getChildren($parentDocument)
    {
        $childDocuments = $parentDocument->getChildren();

        if (!count($childDocuments)) {
            return [];
        }

        $children = [];

        $documentInspector = $this->get('sulu_document_manager.document_inspector');

        /** @var PageDocument $childDocument */
        foreach ($childDocuments as $childDocument) {
            if ($childDocument instanceof PageDocument
                && $documentInspector->getLocalizationState($childDocument) !== LocalizationState::GHOST
                && $childDocument->getWorkflowStage() === WorkflowStage::PUBLISHED
            ) {
                $children[] = $childDocument;
            }
        }

        return $children;
    }
}
