<?php

namespace JakubGucen\EntityConstantsGenerator\Model;

use JakubGucen\EntityConstantsGenerator\Exception\FileIOException;

class FileIO
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @throws FileIOException
     */
    public function getFileContent(): string
    {
        $fileContent = file_get_contents($this->path);
        if ($fileContent === false) {
            throw new FileIOException("Could not get the file content: {$this->path}");
        }

        return $fileContent;
    }

    /**
     * @throws FileIOException
     */
    public function saveFileContent(string $newFileContent): void
    {
        $bytes = file_put_contents($this->path, $newFileContent);
        if ($bytes === false) {
            throw new FileIOException("Could not save the file content: {$this->path}");
        }
    }
}
