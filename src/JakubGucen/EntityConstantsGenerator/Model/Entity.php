<?php

namespace JakubGucen\EntityConstantsGenerator\Model;

use JakubGucen\EntityConstantsGenerator\Exception\InvalidEntity;
use JakubGucen\EntityConstantsGenerator\Helper\StringHelper;
use ReflectionClass;
use ReflectionProperty;

class Entity
{
    const REGION_START = '#region JakubGucen-EntityConstantsGenerator';
    const REGION_END = '#endregion';

    protected string $class;
    protected string $path;
    protected ReflectionClass $reflectionClass;
    protected string $eol = "\n";
    protected string $tab = '    ';
    protected string $startAfterExpr;

    public function __construct(string $class, string $path)
    {
        $this->class = $class;
        $this->path = $path;

        $this->reflectionClass = new ReflectionClass($this->class);
        $this->startAfterExpr = '/class ' . $this->reflectionClass->getShortName() . '.*' . $this->eol . '{' . $this->eol . '/';
    }

    public function generate(): void
    {
        $fileContent = file_get_contents($this->path);
        $fileContent = $this->removeRegion($fileContent);

        $constantLines = $this->generateConstantLines();
        $lines = [
            self::REGION_START,
            'use \JakubGucen\EntityConstantsGenerator\Traits\MetaEntityTrait;',
            ...$constantLines,
            self::REGION_END,
        ];

        $region = $this->generateRegion($lines);
        $fileContent = $this->addRegion($fileContent, $region);

        file_put_contents($this->path, $fileContent);
    }

    public function rollback(): void
    {
        $fileContent = file_get_contents($this->path);
        $fileContent = $this->removeRegion($fileContent);

        file_put_contents($this->path, $fileContent);
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
        preg_match($this->startAfterExpr, $fileContent, $matches, PREG_OFFSET_CAPTURE);
        if (!is_array($matches) || !count($matches)) {
            throw new InvalidEntity("Could not find expression: {$this->startAfterExpr} in: {$this->class}");
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
            throw new InvalidEntity("Could not find end of region in: {$this->class}");
        }

        $length = $lastLinePos - $firstLinePos + mb_strlen($lastLine);
        $oldLines = mb_substr($fileContent, $firstLinePos, $length);

        return str_replace($oldLines, '', $fileContent);
    }

    protected function generatePropertyNames(): array
    {
        $properties = $this->reflectionClass->getProperties();
        $propertyNames = array_map(
            fn (ReflectionProperty $property) => $property->getName(),
            $properties
        );

        // get property names by add methods
        $methods = $this->reflectionClass->getMethods();
        foreach ($methods as $method) {
            $methodName = $method->getName();

            if (
                mb_strlen($methodName) <= 3
                || !StringHelper::checkStringStartsWith($methodName, 'add')
            ) {
                continue;
            }

            $propertyName = mb_substr($methodName, 3);
            $propertyName = lcfirst($propertyName);
            $propertyNames[] = $propertyName;
        }

        $propertyNames = array_unique($propertyNames);
        asort($propertyNames);

        return $propertyNames;
    }

    protected function generateConstantLines(): array
    {
        $constantLines = [];
        $propertyNames = $this->generatePropertyNames();

        // get interfaces constants
        $interfacesConsts = [];
        $interfaces = $this->reflectionClass->getInterfaces();
        foreach ($interfaces as $interface) {
            foreach ($interface->getConstants() as $constantName => $constValue) {
                $interfacesConsts[] = $constantName;
            }
        }

        // generate constant lines
        foreach ($propertyNames as $propertyName) {
            $constantName = $this->generateConstantName($propertyName);
            if (array_search($constantName, $interfacesConsts, true) !== false) {
                continue;
            }

            $constantLines[] = "const {$constantName} = '{$propertyName}';";
        }

        return $constantLines;
    }

    protected function generateConstantName(string $propertyName): string
    {
        $constantName = '';

        for ($i = 0; $i < mb_strlen($propertyName); $i++) {
            $char = $propertyName[$i];
            $charUpper = mb_strtoupper($char);

            if ($i > 0 && $char === $charUpper) {
                $constantName .= '_';
            }

            $constantName .= $charUpper;
        }

        return $constantName;
    }
}
