<?php

if ('doctrinedbal' === $_ENV['PHPCR_BACKEND']) {
    $container->setParameter('app.phpcr_backend', ['type' => 'doctrinedbal']);
} elseif ('jackrabbit' === $_ENV['PHPCR_BACKEND']) {
    $container->setParameter('app.phpcr_backend', ['type' => 'jackrabbit', 'url' => '%env(PHPCR_BACKEND_URL)%']);

    $container->loadFromExtension(
        'sulu_document_manager',
        [
            'versioning' => [
                'enabled' => true,
            ],
        ]
    );
}
