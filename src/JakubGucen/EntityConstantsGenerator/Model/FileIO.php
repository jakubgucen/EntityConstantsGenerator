<?php

namespace JakubGucen\EntityConstantsGenerator\Model;

use JakubGucen\EntityConstantsGenerator\Exception\FileIOException;
use JakubGucen\EntityConstantsGenerator\Interfaces\IFileIO;

class FileIO implements IFileIO
{
    private string $path;
    private ?string $content = null;
    private ?string $onDiskContent = null;
    private ?string $originalContent = null;
    private bool $isDirty = false;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @throws FileIOException
     */
    public function getContent(): string
    {
        if ($this->content === null) {
            $this->content = $this->getOnDiskContent();
        }

        return $this->content;
    }

    /**
     * @throws FileIOException
     */
    public function setContent(string $content): self
    {
        $this->isDirty = $content !== $this->getOnDiskContent();
        $this->content = $content;

        return $this;
    }

    /**
     * @throws FileIOException
     */
    public function save(): self
    {
        // no changes
        if (!$this->isDirty) {
            return $this;
        }

        $bytes = file_put_contents($this->path, $this->content);
        if ($bytes === false) {
            throw new FileIOException("Could not save the file content: {$this->path}");
        }

        $this->onDiskContent = $this->content;

        return $this;
    }

    public function restore(): IFileIO
    {
        if ($this->originalContent === null) {
            return $this;
        }

        $this->setContent($this->originalContent);

        return $this;
    }

    /**
     * @throws FileIOException
     */
    private function getOnDiskContent(): string
    {
        if ($this->onDiskContent !== null) {
            return $this->onDiskContent;
        }

        $content = file_get_contents($this->path);
        if ($content === false) {
            throw new FileIOException("Could not get the file content: {$this->path}");
        }

        $this->onDiskContent = $content;
        $this->originalContent ??= $content;

        return $this->onDiskContent;
    }
}
