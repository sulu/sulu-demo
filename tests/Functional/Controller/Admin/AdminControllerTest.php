<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Admin;

use PHPUnit\Framework\Attributes\DataProvider;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;

class AdminControllerTest extends SuluTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createAuthenticatedClient();
    }

    #[DataProvider('loadFormKeys')]
    public function testFormMetadata(string $formKey): void
    {
        $this->client->request(Request::METHOD_GET, '/admin/metadata/form/' . $formKey);

        $this->assertHttpStatusCode(200, $this->client->getResponse());
    }

    #[DataProvider('loadListKeys')]
    public function testListMetadata(string $listKey): void
    {
        $this->client->request(Request::METHOD_GET, '/admin/metadata/list/' . $listKey);

        $this->assertHttpStatusCode(200, $this->client->getResponse());
    }

    public function testConfig(): void
    {
        $this->client->request(Request::METHOD_GET, '/admin/config');

        $this->assertHttpStatusCode(200, $this->client->getResponse());
    }

    /**
     * @return \Generator<array<string>>
     */
    public static function loadFormKeys(): \Generator
    {
        return self::getFileKeys('forms');
    }

    /**
     * @return \Generator<array<string>>
     */
    public static function loadListKeys(): \Generator
    {
        return self::getFileKeys('lists');
    }

    /**
     * @return \Generator<array<string>>
     */
    private static function getFileKeys(string $type): \Generator
    {
        $finder = new Finder();
        $path = __DIR__ . '/../../../../config/' . $type;

        $path = \realpath($path);
        if (!$path) {
            throw new \RuntimeException(\sprintf('Could not find path: "%s"', $path));
        }

        $finder->in($path);

        foreach ($finder as $file) {
            yield [
                $file->getFilenameWithoutExtension(),
            ];
        }
    }
}
