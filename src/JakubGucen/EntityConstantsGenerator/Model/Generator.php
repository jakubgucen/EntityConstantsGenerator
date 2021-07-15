<?php

namespace JakubGucen\EntityConstantsGenerator\Model;

use FilesystemIterator;
use InvalidArgumentException;
use Throwable;
use JakubGucen\EntityConstantsGenerator\Exception\EntityFileException;
use JakubGucen\EntityConstantsGenerator\Exception\FileIOException;
use JakubGucen\EntityConstantsGenerator\Helper\StringHelper;

class Generator
{
    private EntitiesData $entitiesData;

    /**
     * @var EntityFile[]
     */
    private array $entities = [];
    private bool $entietiesPrepared = false;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(EntitiesData $entitiesData)
    {
        $entitiesData->check();
        $this->entitiesData = $entitiesData;
    }

    /**
     * Generates regions JakubGucen-EntityConstantsGenerator.  
     * Note: it overrides files.
     * 
     * @throws FileIOException
     * @throws EntityFileException
     */
    public function run(): void
    {
        $this->prepareEntities();

        foreach ($this->entities as $entity) {
            try {
                $entity->generate();
            } catch (Throwable $e) {
                $this->rollback();
                throw $e;
            }
        }
    }

    /**
     * Removes regions JakubGucen-EntityConstantsGenerator.  
     * Note: it overrides files.
     * 
     * @throws FileIOException
     * @throws EntityFileException
     */
    public function rollback(): void
    {
        $this->prepareEntities();

        foreach ($this->entities as $entity) {
            $entity->rollback();
        }
    }

    private function prepareEntities(): void
    {
        if ($this->entietiesPrepared) {
            return;
        }

        $this->loadEntities();

        $this->entietiesPrepared = true;
    }

    private function loadEntities(): void
    {
        $this->entities = [];

        $entityDir = $this->entitiesData->getDir();
        $entityNamespace = $this->entitiesData->getNamespace();

        $iterator = new FilesystemIterator($entityDir, FilesystemIterator::SKIP_DOTS);

        foreach ($iterator as $item) {
            $fileName = $item->getFilename();
            if (!StringHelper::checkStringEndsWith($fileName, '.php')) {
                continue;
            }

            $baseName = basename($fileName, '.php');
            $entityClass = "{$entityNamespace}\\{$baseName}";

            $fileIO = new FileIO($item->getPathname());
            $entityFile = new EntityFile($fileIO, $entityClass);

            $this->entities[] = $entityFile;
        }
    }
}
