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
use Sulu\Component\Localization\Localization;
use Sulu\Component\Webspace\Manager\WebspaceManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Install project.
 */
class InstallCommand extends ContainerAwareCommand
{
    const SQL_FILE_PATH = 'data/sulu_demo.sql';
    const MEDIA_FILE_PATH = 'data/media.tar.gz';

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
     *
     * @return int
     */
    protected function importMedia()
    {
        // be sure that the decompressed file doesn't exists
        $this->filesystem->remove(str_replace('.gz', '', self::MEDIA_FILE_PATH));

        // decompress file
        $pharData = new \PharData(self::MEDIA_FILE_PATH);
        $pharDataDecompressed = $pharData->decompress();

        // extract file to media path
        return $pharDataDecompressed->extractTo($this->mediaPath);
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
        $cacheRootDir = dirname($this->getContainer()->getParameter('kernel.cache_dir'), 2);
        $this->filesystem->remove($cacheRootDir . '/admin');
        $this->filesystem->remove($cacheRootDir . '/preview');
        $this->filesystem->remove($cacheRootDir . '/website');
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
}
