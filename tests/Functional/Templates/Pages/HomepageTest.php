<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tests\Functional\Templates\Pages;

use Tests\Functional\WebsiteTestCase;
use Tests\Functional\Traits\PageTrait;

/**
 * Test homepage template.
 */
class HomepageTest extends WebsiteTestCase
{
    use PageTrait;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->initPhpcr();
    }

    public function testHomepage()
    {
        $this->createPage(
            'homepage',
            [
                'locale' => 'en',
                'title' => 'Homepage',
                'url' => '/homepage',
                'published' => true,
            ]
        );

        $client = $this->createWebsiteClient();
        $crawler = $client->request('GET', '/en/homepage');

        $this->assertHttpStatusCode(200, $client->getResponse());
        $this->assertCrawlerProperty($crawler, 'title', 'Homepage');
    }
}
