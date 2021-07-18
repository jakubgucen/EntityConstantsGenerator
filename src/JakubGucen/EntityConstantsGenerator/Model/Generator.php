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
        $entityFiles = $this->loadEntities();
        $generated = [];

        foreach ($entityFiles as $entityFile) {
            try {
                $entityFile->generate();
                $generated[] = $entityFile;
            } catch (Throwable $e) {
                array_map(
                    fn (EntityFile $entityFile) => $entityFile->restore(),
                    $generated
                );
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
        $entityFiles = $this->loadEntities();

        foreach ($entityFiles as $entityFile) {
            $entityFile->rollback();
        }
    }

    /**
     * @return EntityFile[]
     */
    private function loadEntities(): array
    {
        $entityFiles = [];
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

            $entityFiles[] = $entityFile;
        }

        usort(
            $entityFiles,
            fn (EntityFile $a, EntityFile $b) => strcmp($a->getClass(), $b->getClass())
        );

        return $entityFiles;
    }
}
