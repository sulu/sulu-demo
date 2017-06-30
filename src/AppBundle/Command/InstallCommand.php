<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Command;

use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use ONGR\ElasticsearchBundle\Service\Manager;
use Sulu\Bundle\WebsiteBundle\Cache\CacheClearerInterface;
use Sulu\Component\Localization\Localization;
use Sulu\Component\Webspace\Manager\WebspaceManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Install project.
 */
class InstallCommand extends ContainerAwareCommand
{
    const DATA_ROOT_PATH = 'vendor' . DIRECTORY_SEPARATOR . 'sulu' . DIRECTORY_SEPARATOR . 'demo-data' . DIRECTORY_SEPARATOR . 'data';
    const SQL_FILE_PATH = self::DATA_ROOT_PATH . DIRECTORY_SEPARATOR . 'sulu_demo.sql';
    const MEDIA_DIRECTORY_PATH = self::DATA_ROOT_PATH . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'media';

    /** @var SymfonyStyle */
    protected $io;

    /** @var FileSystem */
    protected $filesystem;

    /** @var string */
    protected $mediaPath;

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('app:install')
            ->setDescription('Installs Sulu demo')
            ->addOption('destroy', null, InputOption::VALUE_NONE, 'Destroy existing data');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->filesystem = new Filesystem();
        $this->mediaPath = $this->getContainer()->getParameter('sulu_media.media.storage.local.path');

        if (true === $input->getOption('destroy')) {
            $this->dropDatabase();
            $this->deleteMedia();
        }

        if ($this->checkIfDatabaseExists() || $this->checkIfMediaDirectoryIsEmpty()) {
            $this->io->error(
                'Database exists or media directory is not empty. You can run the command with "--destroy" to delete everything.'
            );

            return 1;
        }

        $this->createDatabase();
        $this->importDatabaseDump();
        $this->importMedia();
        $this->reindexArticles();
        $this->clearCache();
        $this->massiveSearchReindex();

        return 0;
    }

    protected function checkIfDatabaseExists()
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $connection = $databaseExists = $doctrine->getConnection();

        try {
            /** @var AbstractSchemaManager $schemaManager */
            $schemaManager = $connection->getSchemaManager();
            $databaseExists = true;
            $schemaManager->listDatabases();
        } catch (ConnectionException $e) {
            $databaseExists = false;
        }

        return $databaseExists;
    }

    protected function checkIfMediaDirectoryIsEmpty()
    {
        return $this->filesystem->exists($this->mediaPath . '/*');
    }

    /**
     * @return int
     */
    protected function dropDatabase()
    {
        return $this->execCommand('doctrine:database:drop', ['--force' => true]);
    }

    /**
     * @return int
     */
    protected function createDatabase()
    {
        return $this->execCommand('doctrine:database:create');
    }

    /**
     * @return int
     */
    protected function importDatabaseDump()
    {
        return $this->execCommand('doctrine:database:import', ['file' => self::SQL_FILE_PATH]);
    }

    /**
     * Deletes media path.
     *
     * @return int
     */
    protected function deleteMedia()
    {
        $this->filesystem->remove($this->mediaPath);
    }

    /**
     * Imports the media from the media file to the media file path.
     */
    protected function importMedia()
    {
        if (!$this->filesystem->exists($this->mediaPath)) {
            $this->filesystem->mkdir($this->mediaPath);
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(self::MEDIA_DIRECTORY_PATH, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $targetDir = $this->mediaPath . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
                $this->filesystem->mkdir($targetDir);
            } else {
                $targetFilename = $this->mediaPath . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
                $this->filesystem->copy($item, $targetFilename);
            }
        }
    }

    /**
     * Drops and recreates needed elasticsearch index and rebuild index for articles.
     */
    protected function reindexArticles()
    {
        /** @var Manager $esManagerLive */
        $esManagerLive = $this->getContainer()->get('es.manager.live');
        /** @var Manager $esManagerDefault */
        $esManagerDefault = $this->getContainer()->get('es.manager.default');

        // drop and create index
        $esManagerDefault->dropAndCreateIndex();
        $esManagerLive->dropAndCreateIndex();

        /** @var WebspaceManager $webspaceManager */
        $webspaceManager = $this->getContainer()->get('sulu_core.webspace.webspace_manager');

        // reindex with all locales
        /** @var Localization $localization */
        foreach ($webspaceManager->getAllLocalizations() as $localization) {
            $this->execCommand('sulu:article:index-rebuild',
                [
                    'locale' => $localization->getLocale(),
                ]
            );
            $this->execCommand('sulu:article:index-rebuild',
                [
                    'locale' => $localization->getLocale(),
                    '--live' => true,
                ]
            );
        }
    }

    /**
     * Clears the whole cache.
     */
    protected function clearCache()
    {
        /** @var CacheClearerInterface $httpCacheClearer */
        $httpCacheClearer = $this->getContainer()->get('sulu_website.http_cache.clearer');
        $httpCacheClearer->clear();
    }

    /**
     * Reindex massive search index.
     */
    protected function massiveSearchReindex()
    {
        // call purge all command
        $this->execCommandline('bin' . DIRECTORY_SEPARATOR . 'websiteconsole massive:search:purge --all --force');

        // call reindex commands
        $this->execCommandline('bin' . DIRECTORY_SEPARATOR . 'websiteconsole massive:search:reindex');
        $this->execCommandline('bin' . DIRECTORY_SEPARATOR . 'adminconsole massive:search:reindex');
    }

    /**
     * Execute a command.
     *
     * @param string $command
     * @param array $arguments
     *
     * @return int
     */
    protected function execCommand($command, $arguments = [])
    {
        $command = $this->getApplication()->find($command);

        return $command->run(new ArrayInput($arguments), $this->io);
    }

    /**
     * Execute commandline call.
     *
     * @param string$cmdLine
     *
     * @return Process
     */
    protected function execCommandline($cmdLine)
    {
        $rootDir = $this->getContainer()->getParameter('kernel.project_dir');

        $cmdLine .= ' --env=' . $this->getContainer()->getParameter('kernel.environment');

        $process = new Process($this->getPhp() . ' ' . $rootDir . DIRECTORY_SEPARATOR . $cmdLine);
        $process->setTimeout(null);
        $process->run(function ($type, $out) {
            $this->io->writeln($out);
        });

        if ($process->getExitCode() !== 0) {
            $this->io->error(
                sprintf(
                    'Could not execute command "%s", got exit code "%s": %s',
                    $cmdLine,
                    $process->getExitCode(),
                    $process->getErrorOutput()
                )
            );
        }

        return $process;
    }

    /**
     * Finds the PHP executable.
     *
     * @return string
     *
     * @throws FileNotFoundException
     */
    protected function getPhp()
    {
        $phpFinder = new PhpExecutableFinder();
        $phpPath = $phpFinder->find();
        if (!$phpPath) {
            throw new FileNotFoundException('The PHP executable could not be found.');
        }

        return $phpPath;
    }
}
