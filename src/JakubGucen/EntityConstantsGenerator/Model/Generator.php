<?php

namespace JakubGucen\EntityConstantsGenerator\Model;

use FilesystemIterator;
use InvalidArgumentException;
use JakubGucen\EntityConstantsGenerator\Helper\StringHelper;

class Generator
{
    /**
     * @var EntityData[]
     */
    protected array $entitiesData;

    /**
     * @var Entity[]
     */
    protected array $entities = [];

    /**
     * @param EntityData[] $entitiesData
     * @throws InvalidArgumentException
     */
    public function __construct(array $entitiesData)
    {
        $this->checkEntitiesData($entitiesData);

        $this->entitiesData = $entitiesData;
    }

    public function run(): void
    {
        $this->loadEntities();

        foreach ($this->entities as $entity) {
            $entity->generate();
        }
    }

    public function rollback(): void
    {
        $this->loadEntities();

        foreach ($this->entities as $entity) {
            $entity->rollback();
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function checkEntitiesData(array $entitiesData): void
    {
        foreach ($entitiesData as $entityData) {
            if (is_object($entityData) && $entityData instanceof EntityData) {
                $entityData->check();
                continue;
            }

            throw new InvalidArgumentException('Invalid item in: entitiesData, expected instance of EntityData');
        }
    }

    protected function loadEntities(): void
    {
        $this->entities = [];

        foreach ($this->entitiesData as $entityData) {
            $entityDir = $entityData->getDir();
            $entityNamespace = $entityData->getNamespace();

            $iterator = new FilesystemIterator($entityDir, FilesystemIterator::SKIP_DOTS);

            foreach ($iterator as $item) {
                $fileName = $item->getFilename();
                if (!StringHelper::checkStringEndsWith($fileName, '.php')) {
                    continue;
                }

                $baseName = basename($fileName, '.php');
                $entityClass = "{$entityNamespace}\\{$baseName}";
                $entityPath = $item->getPathname();

                $this->entities[] = new Entity(
                    $entityClass,
                    $entityPath
                );
            }
        }
    }
}
