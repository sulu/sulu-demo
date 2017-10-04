<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tests\Functional\Traits;

use Sulu\Bundle\ContentBundle\Document\PageDocument;
use Sulu\Component\Content\Document\WorkflowStage;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Page trait to create new pages.
 */
trait PageTrait
{
    /**
     * @param string $template
     * @param array $data
     * @param string $locale
     *
     * @return PageDocument
     */
    protected function createPage($template, array $data)
    {
        $documentManager = $this->getDocumentManager();
        /** @var \Sulu\Bundle\ContentBundle\Document\PageDocument $document */
        $document = $documentManager->create('page');
        $document->setLocale($data['locale']);
        $document->setTitle($data['title']);
        $document->setStructureType($template);
        $document->setResourceSegment($data['url']);

        if ($data['published']) {
            $document->setWorkflowStage(WorkflowStage::PUBLISHED);
        }

        $document->getStructure()->bind($data);

        $documentManager->persist($document, $data['locale'], ['parent_path' => '/cmf/sulu_demo/contents']);

        if ($data['published']) {
            $documentManager->publish($document, $data['locale']);
        }

        $documentManager->flush();

        return $document;
    }

    /**
     * Assert crawler property.
     *
     * @param Crawler $crawler
     * @param string $selector
     * @param string $needle
     */
    private function assertCrawlerProperty(Crawler $crawler, $selector, $needle)
    {
        $this->assertContains($needle, $crawler->filter($selector)->html());
    }
}
