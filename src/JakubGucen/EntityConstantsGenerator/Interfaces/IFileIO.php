<?php

namespace JakubGucen\EntityConstantsGenerator\Interfaces;

interface IFileIO
{
    public function getContent(): string;
    public function setContent(string $content): IFileIO;
    public function save(): IFileIO;
}
