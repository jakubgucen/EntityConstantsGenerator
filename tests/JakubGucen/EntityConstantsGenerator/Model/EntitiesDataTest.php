<?php

namespace Tests\JakubGucen\EntityConstantsGenerator\Helper;

use InvalidArgumentException;
use JakubGucen\EntityConstantsGenerator\Model\EntitiesData;
use PHPUnit\Framework\TestCase;

class EntitiesDataTest extends TestCase
{
    public function testCheck(): void
    {
        $entitiesData = new EntitiesData();
        $entitiesData
            ->setDir('test_dir')
            ->setNamespace('App\Entity');

        $result = $entitiesData->check();
        $this->assertNull($result);

        $this->assertSame('test_dir', $entitiesData->getDir());
        $this->assertSame('App\Entity', $entitiesData->getNamespace());
    }

    public function testCheckNoDir(): void
    {
        $entitiesData = new EntitiesData();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Entity dir cannot be empty.');
        $entitiesData->check();
    }

    public function testCheckNoNamespace(): void
    {
        $entitiesData = new EntitiesData();
        $entitiesData->setDir('test_dir');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Entity namespace cannot be empty.');
        $entitiesData->check();
    }

    public function testSetDirEndsWithBackslash(): void
    {
        $entitiesData = new EntitiesData();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Entity dir cannot ends with \.');
        $entitiesData->setDir('test_dir\\');
    }

    public function testSetDirEndsWithSlash(): void
    {
        $entitiesData = new EntitiesData();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Entity dir cannot ends with /.');
        $entitiesData->setDir('test_dir/');
    }

    public function testSetNamespaceEndsWithBackslash(): void
    {
        $entitiesData = new EntitiesData();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Entity namespace cannot ends with \.');
        $entitiesData->setNamespace('test_dir\\');
    }

    public function testSetNamespaceEndsWithSlash(): void
    {
        $entitiesData = new EntitiesData();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Entity namespace cannot ends with /.');
        $entitiesData->setNamespace('test_dir/');
    }
}
