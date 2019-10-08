<?php

declare(strict_types=1);

use App\Kernel;
use Symfony\Component\DependencyInjection\ContainerInterface;

require dirname(__DIR__) . '/../config/bootstrap.php';

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG'], Kernel::CONTEXT_ADMIN);
$kernel->boot();

/** @var ContainerInterface $container */
$container = $kernel->getContainer();

return $container->get('doctrine')->getManager();
