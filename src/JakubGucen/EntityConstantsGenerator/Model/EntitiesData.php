<?php

namespace JakubGucen\EntityConstantsGenerator\Model;

use InvalidArgumentException;
use JakubGucen\EntityConstantsGenerator\Helper\StringHelper;

class EntitiesData
{
    protected ?string $dir = null;
    protected ?string $namespace = null;

    /**
     * @throws InvalidArgumentException
     */
    public function check(): void
    {
        if ($this->dir === null) {
            throw new InvalidArgumentException('Entity dir cannot be empty.');
        }

        if ($this->namespace === null) {
            throw new InvalidArgumentException('Entity namespace cannot be empty.');
        }
    }

    public function getDir(): ?string
    {
        return $this->dir;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setDir(string $dir): self
    {
        $this->checkPath($dir, 'dir');

        $this->dir = $dir;
        return $this;
    }

    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setNamespace(string $namespace): self
    {
        $this->checkPath($namespace, 'namespace');

        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function checkPath(string $path, string $paramName): void
    {
        if (StringHelper::checkStringEndsWith($path, '\\')) {
            throw new InvalidArgumentException("Entity {$paramName} cannot ends with \.");
        }

        if (StringHelper::checkStringEndsWith($path, '/')) {
            throw new InvalidArgumentException("Entity {$paramName} cannot ends with /.");
        }
    }
}
