<?php

namespace AppBundle\Controller;

use Sulu\Bundle\ContentBundle\Document\PageDocument;
use Sulu\Bundle\WebsiteBundle\Controller\WebsiteController;
use Sulu\Component\Content\Compat\StructureInterface;
use Sulu\Component\Content\Repository\Mapping\Mapping;
use Sulu\Component\Content\Repository\Mapping\MappingBuilder;
use Symfony\Component\HttpFoundation\Response;

class ArtistController extends WebsiteController
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
    public function indexAction(StructureInterface $structure, $preview = false, $partial = false)
    {
        /** @var PageDocument $document */
        $document = $this->get('sulu_document_manager.document_manager')
            ->find($structure->getUuid(), $structure->getLanguageCode());

        $response = $this->renderStructure(
            $structure,
            [
                'artists' => $this->get('sulu_content.content_repository')->findByParentUuid($document->getParent()
                    ->getUuid(), $structure->getLanguageCode(), $structure->getWebspaceKey(), MappingBuilder::create
                ()->addProperties(['title'])
                    ->getMapping())
            ],
            $preview,
            $partial
        );

        return $response;
    }
}