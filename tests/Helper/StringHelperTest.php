<?php

namespace JakubGucen\EntityConstantsGenerator\Tests\Helper;

use JakubGucen\EntityConstantsGenerator\Helper\StringHelper;
use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    public function testCheckStringStartsWith(): void
    {
        $result = StringHelper::checkStringStartsWith('test', '');
        $this->assertTrue($result);

        $result = StringHelper::checkStringStartsWith('test', 't');
        $this->assertTrue($result);

        $result = StringHelper::checkStringStartsWith('test', 'te');
        $this->assertTrue($result);

        $result = StringHelper::checkStringStartsWith('test', 'tes');
        $this->assertTrue($result);

        $result = StringHelper::checkStringStartsWith('test', 'test');
        $this->assertTrue($result);

        $result = StringHelper::checkStringStartsWith('test', 'e');
        $this->assertFalse($result);

        $result = StringHelper::checkStringStartsWith('test', 'st');
        $this->assertFalse($result);
    }

    public function testCheckStringEndsWith(): void
    {
        $result = StringHelper::checkStringEndsWith('test', '');
        $this->assertTrue($result);

        $result = StringHelper::checkStringEndsWith('test', 't');
        $this->assertTrue($result);

        $result = StringHelper::checkStringEndsWith('test', 'st');
        $this->assertTrue($result);

        $result = StringHelper::checkStringEndsWith('test', 'est');
        $this->assertTrue($result);

        $result = StringHelper::checkStringEndsWith('test', 'test');
        $this->assertTrue($result);

        $result = StringHelper::checkStringEndsWith('test', 'e');
        $this->assertFalse($result);

        $result = StringHelper::checkStringEndsWith('test', 'te');
        $this->assertFalse($result);
    }

    public function testGenerateConstantName(): void
    {
        $result = StringHelper::generateConstantName('id');
        $this->assertSame('ID', $result);

        $result = StringHelper::generateConstantName('oneHanded');
        $this->assertSame('ONE_HANDED', $result);

        $result = StringHelper::generateConstantName('OneHanded');
        $this->assertSame('ONE_HANDED', $result);

        $result = StringHelper::generateConstantName('one_handed');
        $this->assertSame('ONE_HANDED', $result);

        $result = StringHelper::generateConstantName('ONE_HANDED');
        $this->assertSame('ONE_HANDED', $result);
    }

    public function testGenerateConstantNamePolish(): void
    {
        $result = StringHelper::generateConstantName('abc??b??');
        $this->assertSame('ABC_??B??', $result);

        $result = StringHelper::generateConstantName('Abc??b??');
        $this->assertSame('ABC_??B??', $result);

        $result = StringHelper::generateConstantName('abc_??b??');
        $this->assertSame('ABC_??B??', $result);

        $result = StringHelper::generateConstantName('ABC_??B??');
        $this->assertSame('ABC_??B??', $result);
    }
}
