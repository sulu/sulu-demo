<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tests\Functional;

use Doctrine\ORM\EntityManager;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Sulu\Component\DocumentManager\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Base test case.
 */
abstract class AbstractTestCase extends SuluTestCase
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Kernel[]
     */
    protected $kernelStack;

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        while ($kernel = array_shift($this->kernelStack)) {
            $kernel->shutdown();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->purgeDatabase();
        $this->entityManager = $this->getEntityManager();
        $this->client = $this->createAuthenticatedClient('127.0.0.1');
    }

    /**
     * {@inheritdoc}
     */
    protected function getKernel(array $options = [])
    {
        $kernel = new \WebsiteKernel('test', true);
        $kernel->boot();
        $this->kernelStack[] = $kernel;

        return $kernel;
    }

    /**
     * {@inheritdoc}
     */
    protected function createAuthenticatedClient($httpHost = null)
    {
        $client = $this->createClient(
            [
                'environment' => 'test',
            ],
            [
                'PHP_AUTH_USER' => 'test',
                'PHP_AUTH_PW' => 'test',
            ]
        );

        if (null !== $httpHost) {
            $client->setServerParameter('HTTP_HOST', $httpHost);
        }

        return $client;
    }

    /**
     * @return DocumentManager
     */
    protected function getDocumentManager()
    {
        return $this->getContainer()->get('sulu_document_manager.document_manager');
    }

    /**
     * Gets a service.
     *
     * @param $id
     *
     * @return object
     */
    protected function get($id)
    {
        return $this->getContainer()->get($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTestUser()
    {
        return $this->getContainer()->get('test_user_provider')->getUser();
    }
}
