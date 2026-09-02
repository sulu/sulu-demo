<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
use Sulu\Rector\Set\SuluLevelSetList;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withPhpStanConfigs([
        __DIR__ . '/phpstan.dist.neon',
        // rector does not load phpstan extension automatically so require them manually here:
        __DIR__ . '/vendor/phpstan/phpstan-doctrine/extension.neon',
        __DIR__ . '/vendor/phpstan/phpstan-symfony/extension.neon',
    ])
    ->withImportNames(importShortClasses: false)
    ->withSymfonyContainerXml(__DIR__ . '/var/cache/website/dev/App_KernelDevDebugContainer.xml')
    ->withSets([
        SetList::CODE_QUALITY,
        LevelSetList::UP_TO_PHP_81,

        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,

        DoctrineSetList::DOCTRINE_CODE_QUALITY,

        SuluLevelSetList::UP_TO_SULU_26,
    ]);
