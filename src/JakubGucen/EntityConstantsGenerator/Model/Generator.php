<?php

namespace JakubGucen\EntityConstantsGenerator\Model;

use FilesystemIterator;
use InvalidArgumentException;
use JakubGucen\EntityConstantsGenerator\Exception\EntityFileException;
use JakubGucen\EntityConstantsGenerator\Helper\StringHelper;

class Generator
{
    protected EntitiesData $entitiesData;

    /**
     * @var Entity[]
     */
    protected array $entities = [];

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(EntitiesData $entitiesData)
    {
        $entitiesData->check();

        $this->entitiesData = $entitiesData;
    }

    /**
     * @throws EntityFileException
     */
    public function run(): void
    {
        $this->loadEntities();

        foreach ($this->entities as $entity) {
            $entity->generate();
        }
    }

    /**
     * @throws EntityFileException
     */
    public function rollback(): void
    {
        $this->loadEntities();

        foreach ($this->entities as $entity) {
            $entity->rollback();
        }
    }

    protected function loadEntities(): void
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
            $entityPath = $item->getPathname();

            $this->entities[] = new EntityFile(
                $entityClass,
                $entityPath
            );
        }
    }
}
