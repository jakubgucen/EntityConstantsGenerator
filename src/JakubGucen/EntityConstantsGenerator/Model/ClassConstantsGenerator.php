<?php

namespace JakubGucen\EntityConstantsGenerator\Model;

use JakubGucen\EntityConstantsGenerator\Helper\StringHelper;
use ReflectionProperty;
use ReflectionClass;

class ClassConstantsGenerator
{
    protected ReflectionClass $reflectionClass;

    public function __construct(ReflectionClass $reflectionClass)
    {
        $this->reflectionClass = $reflectionClass;
    }

    public function generateConstantLines(): array
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
            $constantName = StringHelper::generateConstantName($propertyName);
            if (array_search($constantName, $interfacesConsts, true) !== false) {
                continue;
            }

            $constantLines[] = "const {$constantName} = '{$propertyName}';";
        }

        return $constantLines;
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
}
