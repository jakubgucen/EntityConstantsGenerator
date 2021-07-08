<?php

namespace JakubGucen\EntityConstantsGenerator\Model;

use InvalidArgumentException;

class EntityData
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

    public function getDir(): string
    {
        return $this->dir;
    }

    public function setDir(string $dir): self
    {
        $this->dir = rtrim($dir, '\\/');
        return $this;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace): self
    {
        $this->namespace = rtrim($namespace, '\\/');
        return $this;
    }
}
