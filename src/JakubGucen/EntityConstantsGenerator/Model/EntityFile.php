<?php

namespace JakubGucen\EntityConstantsGenerator\Model;

use JakubGucen\EntityConstantsGenerator\Exception\EntityFileException;
use ReflectionClass;

class EntityFile
{
    const REGION_START = '#region JakubGucen-EntityConstantsGenerator';
    const REGION_END = '#endregion';

    protected string $class;
    protected string $path;
    protected ReflectionClass $reflectionClass;
    protected string $eol;
    protected string $tab;

    public function __construct(string $class, string $path)
    {
        $this->class = $class;
        $this->path = $path;
        $this->reflectionClass = new ReflectionClass($this->class);
        $this->eol = "\n";
        $this->tab = '    ';
    }

    public function generate(): void
    {
        $fileContent = $this->getFileContent();
        $fileContent = $this->removeRegion($fileContent);

        $classConstantsGenerator = new ClassConstantsGenerator(
            $this->reflectionClass
        );
        $constantLines = $classConstantsGenerator->generateConstantLines();

        $lines = [
            self::REGION_START,
            'use \JakubGucen\EntityConstantsGenerator\Traits\MetaEntityTrait;',
            ...$constantLines,
            self::REGION_END,
        ];

        $region = $this->generateRegion($lines);
        $fileContent = $this->addRegion($fileContent, $region);

        $this->saveFileContent($fileContent);
    }

    public function rollback(): void
    {
        $fileContent = $this->getFileContent();
        $fileContent = $this->removeRegion($fileContent);

        $this->saveFileContent($fileContent);
    }

    protected function getFileContent(): string
    {
        $fileContent = file_get_contents($this->path);
        if ($fileContent === false) {
            throw new EntityFileException('Could not get the file content: ' . $this->path);
        }

        return $fileContent;
    }

    protected function saveFileContent(string $newFileContent): void
    {
        $bytes = file_put_contents($this->path, $newFileContent);
        if ($bytes === false) {
            throw new EntityFileException('Could not save the file content: ' . $this->path);
        }
    }

    protected function generateRegion(array $lines): string
    {
        $lines = array_map(
            fn (string $line) => $this->prepareLine($line),
            $lines
        );
        $region = $this->prepareRegionEnd(implode($this->eol, $lines));

        return $region;
    }

    protected function addRegion(string $fileContent, string $region): string
    {
        $startAfterExpr = '/class ' . $this->reflectionClass->getShortName() . '.*' . $this->eol . '{' . $this->eol . '/';
        preg_match($startAfterExpr, $fileContent, $matches, PREG_OFFSET_CAPTURE);
        if (!is_array($matches) || !count($matches)) {
            throw new EntityFileException("Could not find expression: {$startAfterExpr} in: {$this->class}");
        }

        $startFrom = $matches[0][1] + strlen($matches[0][0]);
        $fileContent = substr_replace($fileContent, $region, $startFrom, 0);

        return $fileContent;
    }

    protected function prepareLine(string $line): string
    {
        return "{$this->tab}{$line}";
    }

    protected function prepareRegionEnd(string $lines): string
    {
        return $lines . "{$this->eol}{$this->eol}";
    }

    protected function removeRegion(string $fileContent): string
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
