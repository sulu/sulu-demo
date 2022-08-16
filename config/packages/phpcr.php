<?php

declare(strict_types=1);

if ('doctrinedbal' === $_ENV['PHPCR_BACKEND']) {
    $container->setParameter(
        'app.phpcr_backend',
        [
            'type' => 'doctrinedbal',
        ],
    );
} elseif ('jackrabbit' === $_ENV['PHPCR_BACKEND']) {
    $container->setParameter(
        'app.phpcr_backend',
        [
            'type' => 'jackrabbit',
            'url' => '%env(PHPCR_BACKEND_URL)%',
            'parameters' => [
                'jackalope.jackrabbit_version' => '%env(JACKRABBIT_VERSION)%',
            ],
        ],
    );

    $container->loadFromExtension(
        'sulu_document_manager',
        [
            'versioning' => [
                'enabled' => true,
            ],
        ],
    );
}
