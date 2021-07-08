<?php

namespace Tests\JakubGucen\EntityConstantsGenerator\Helper;

use JakubGucen\EntityConstantsGenerator\Helper\StringHelper;
use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    public function testCheckStringStartsWith(): void
    {
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
}
