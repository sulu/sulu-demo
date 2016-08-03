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
        $document = $this->get('sulu_document_manager.document_manager')
            ->find($structure->getUuid(), $structure->getLanguageCode());

        $parent = $document->getParent();

        $articles = array();

        foreach($parent->getChildren() as $item) {
            array_push($articles, $item);
        }

        /** @var PageDocument[] $articles */
        $articles = SortUtils::multisort($articles,'created');

        $document->getStructure()->getProperty('title');

        $response = $this->renderStructure(
            $structure,
            [
                'lastAndPrevArticle' => $this->createDetailArticles($this->getPrevAndNextArticle ($articles, $structure->getUuid())),
                'latestArticles' => $this->createDetailArticles($this->getLatestArticel($articles, 4)),
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
        $articles = SortUtils::multisort($structure->getChildren(),'created');

        $response = $this->renderStructure(
            $structure,
            [
                'latestArticles' => $this->createOverviewArticles($this->getLatestArticel($articles, 4)),
                'link' => $structure->getPropertiesByTagName("sulu.rlp")[0]->getValue()
                
            ],
            $preview,
            $partial
        );

        return $response;
    }

    function getLatestArticel($articles, $anzArt){
        $lastarticles = [];

        for ($i = 1; $i <= ((count($articles) >= $anzArt)? $anzArt : count($articles)); $i++)
        {
            $lastarticles[$i] = $articles[count($articles)-$i];
        }

        return $lastarticles;
    }

    function getPrevAndNextArticle($articles, $uuid){
        $result = [];

        for ($i = 0; $i <= count($articles) - 1; $i++)
        {
            if ($articles[$i]->getUuid() == $uuid) {
                if(array_key_exists ( $i - 1 , $articles) && array_key_exists ( $i + 1 , $articles)) {
                    $result = array('prev' => $articles[$i - 1],'next' => $articles[$i+1]);
                }
                elseif (array_key_exists ( $i - 1 , $articles )) {
                    $result = array('prev' => $articles[$i - 1]);
                }
                elseif (array_key_exists ( $i + 1 , $articles )) {
                    $result = array('next' => $articles[$i + 1]);
                }
            }
        }

        return $result;
    }

    function createDetailArticles($article){
        /** @var PageDocument[] $article */
        $result = [];

        foreach($article as $key => $item) {
            $structure = $item->getStructure();

            $result[$key] = [
                'image' => $structure->getProperty('contentTitleimage'),
                'url' => $structure->getProperty('url'),
                'heading' => $structure->getProperty('contentHeading'),
                'creation' => $item->getChanged()
            ];
        }

        return $result;
    }

    function createOverviewArticles($article){
        $result = [];

        foreach($article as $key => $item) {
            $structure = $item->getDocument()->getStructure();

            $result[$key] = [
                'image' => $structure->getProperty('contentTitleimage'),
                'url' => $structure->getProperty('url'),
                'heading' => $structure->getProperty('contentHeading'),
                'creation' => $item->getChanged() 
            ];
        }

        return $result;
    }
}