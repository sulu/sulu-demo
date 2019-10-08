<?php

declare(strict_types=1);

require __DIR__ . '/../config/bootstrap.php';

$databaseCreatedFile = __DIR__ . '/../var/cache/admin/test/adminApp_KernelTestDebugContainer.php';

// For dev performance create database only in case of not exist cache directory.
if (!file_exists($databaseCreatedFile)) {
    // Create test database
    $cmd = sprintf(
        'php "%s/../bin/adminconsole" doctrine:database:create --if-not-exists',
        __DIR__
    );

    passthru($cmd, $exitCode);

    if ($exitCode) {
        exit($exitCode);
    }

    // Create or update test database schema
    $cmd = sprintf(
        'php "%s/../bin/adminconsole" doctrine:schema:update --dump-sql --force --complete',
        __DIR__
    );

    passthru($cmd, $exitCode);

    if ($exitCode) {
        exit($exitCode);
    }
}
