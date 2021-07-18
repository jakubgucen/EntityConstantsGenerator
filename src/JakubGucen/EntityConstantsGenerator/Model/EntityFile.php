<?php

namespace JakubGucen\EntityConstantsGenerator\Model;

use JakubGucen\EntityConstantsGenerator\Exception\EntityFileException;
use JakubGucen\EntityConstantsGenerator\Exception\FileIOException;
use JakubGucen\EntityConstantsGenerator\Interfaces\IFileIO;
use ReflectionClass;

class EntityFile
{
    const REGION_START = '#region JakubGucen-EntityConstantsGenerator';
    const REGION_END = '#endregion';

    private IFileIO $fileIO;
    private string $class;
    private string $eol;
    private string $tab;
    private ?ReflectionClass $reflectionClass = null;
    private ?ClassConstantsGenerator $classConstantsGenerator = null;

    public function __construct(IFileIO $fileIO, string $class)
    {
        $this->fileIO = $fileIO;
        $this->class = $class;
        $this->eol = "\n";
        $this->tab = '    ';
    }

    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @throws FileIOException
     * @throws EntityFileException
     */
    public function generate(): void
    {
        $fileContent = $this->fileIO->getContent();
        $fileContent = $this->removeRegion($fileContent);

        $classConstantsGenerator = $this->getClassConstantsGenerator();
        $constantLines = $classConstantsGenerator->generateConstantLines();

        $lines = [
            self::REGION_START,
            'use \JakubGucen\EntityConstantsGenerator\Traits\MetaEntityTrait;',
            ...$constantLines,
            self::REGION_END,
        ];

        $region = $this->generateRegion($lines);
        $fileContent = $this->addRegion($fileContent, $region);

        $this->fileIO
            ->setContent($fileContent)
            ->save();
    }

    /**
     * @throws FileIOException
     * @throws EntityFileException
     */
    public function rollback(): void
    {
        $fileContent = $this->fileIO->getContent();
        $fileContent = $this->removeRegion($fileContent);

        $this->fileIO
            ->setContent($fileContent)
            ->save();
    }

    public function restore(): void
    {
        $this->fileIO
            ->restore()
            ->save();
    }

    private function getReflectionClass(): ReflectionClass
    {
        if ($this->reflectionClass === null) {
            $this->reflectionClass = new ReflectionClass($this->class);
        }

        return $this->reflectionClass;
    }

    private function getClassConstantsGenerator(): ClassConstantsGenerator
    {
        if ($this->classConstantsGenerator === null) {
            $this->classConstantsGenerator = new ClassConstantsGenerator(
                $this->getReflectionClass()
            );
        }

        return $this->classConstantsGenerator;
    }

    private function generateRegion(array $lines): string
    {
        $lines = array_map(
            fn (string $line) => $this->prepareLine($line),
            $lines
        );
        $region = $this->prepareRegionEnd(implode($this->eol, $lines));

        return $region;
    }

    private function prepareLine(string $line): string
    {
        return "{$this->tab}{$line}";
    }

    private function prepareRegionEnd(string $lines): string
    {
        return $lines . "{$this->eol}{$this->eol}";
    }

    /**
     * @throws EntityFileException
     */
    private function addRegion(string $fileContent, string $region): string
    {
        $reflectionClass = $this->getReflectionClass();
        $startAfterExpr = '/class ' . $reflectionClass->getShortName() . '.*' . $this->eol . '{' . $this->eol . '/';
        preg_match($startAfterExpr, $fileContent, $matches, PREG_OFFSET_CAPTURE);
        if (!is_array($matches) || !count($matches)) {
            throw new EntityFileException("Could not find expression: {$startAfterExpr} in: {$this->class}");
        }

        $startFrom = $matches[0][1] + strlen($matches[0][0]);
        $fileContent = substr_replace($fileContent, $region, $startFrom, 0);

        return $fileContent;
    }

    /**
     * @throws EntityFileException
     */
    private function removeRegion(string $fileContent): string
    {
        $firstLine = $this->prepareLine(self::REGION_START);
        $lastLine = $this->prepareLine(self::REGION_END);
        $lastLine = $this->prepareRegionEnd($lastLine);

        $firstLinePos = mb_strpos($fileContent, $firstLine);
        if ($firstLinePos === false) {
            return $fileContent;
        }

        $lastLinePos = mb_strpos($fileContent, $lastLine, $firstLinePos);
        if ($lastLinePos === false) {
            throw new EntityFileException("Could not find end of region in: {$this->class}");
        }

        $length = $lastLinePos - $firstLinePos + mb_strlen($lastLine);
        $oldLines = mb_substr($fileContent, $firstLinePos, $length);

        return str_replace($oldLines, '', $fileContent);
    }
}
