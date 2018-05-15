<?php

namespace Dkron\Tests\Models;

use PHPUnit\Framework\TestCase;

class Job extends TestCase
{
    public function testCanBeUsedAsString()
    {
        $this->assertEquals(
            'user@example.com',
            Email::fromString('user@example.com')
        );
    }
}