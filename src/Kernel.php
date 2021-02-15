<?php

namespace App;

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use App\Controller\Admin\CategoryController;
use App\Manager\CategoryManager;
use FOS\HttpCache\SymfonyCache\HttpCacheProvider;
use Sulu\Bundle\HttpCacheBundle\Cache\SuluHttpCache;
use Sulu\Component\HttpKernel\SuluKernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Kernel extends SuluKernel implements HttpCacheProvider, CompilerPassInterface
{
    /**
     * @var HttpKernelInterface|null
     */
    private $httpCache;

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->setParameter('container.dumper.inline_class_loader', true);

        parent::configureContainer($container, $loader);
    }

    public function getHttpCache()
    {
        if (!$this->httpCache) {
            $this->httpCache = new SuluHttpCache($this);
            // Activate the following for user based caching see also:
            // https://foshttpcachebundle.readthedocs.io/en/latest/features/user-context.html
            //
            //$this->httpCache->addSubscriber(
            //    new \FOS\HttpCache\SymfonyCache\UserContextListener([
            //        'session_name_prefix' => 'SULUSESSID',
            //    ])
            //);
        }

        return $this->httpCache;
    }

    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('sulu_category.category_controller')) {
            $categoryControllerDefinition = $container->getDefinition('sulu_category.category_controller');
            $categoryControllerDefinition->setClass(CategoryController::class);
            $categoryControllerDefinition->addArgument(new Reference('doctrine.orm.entity_manager'));
        }
    }
}
