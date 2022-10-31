<?php

declare(strict_types=1);

$phpcrBackend = $_SERVER['PHPCR_BACKEND'] ?? 'doctrinedbal';

if ('doctrinedbal' === $phpcrBackend) {
    $container->setParameter(
        'app.phpcr_backend',
        [
            'type' => 'doctrinedbal',
        ],
    );
} elseif ('jackrabbit' === $phpcrBackend) {
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
