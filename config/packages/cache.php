<?php

declare(strict_types=1);

$container->loadFromExtension(
    'framework',
    [
        'cache' => [
            'app' => $_ENV['APP_CACHE_ADAPTER'],
        ],
    ],
);
