<?php

declare(strict_types=1);

use App\Kernel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Dotenv\Dotenv;

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
(new Dotenv())->bootEnv(dirname(dirname(__DIR__)) . '/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG'], Kernel::CONTEXT_ADMIN);
$kernel->boot();

/** @var ContainerInterface $container */
$container = $kernel->getContainer();

return $container->get('doctrine')->getManager();
