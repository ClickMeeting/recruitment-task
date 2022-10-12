<?php

namespace App\Tests\PhpUnit;

use App\Entity\Meeting;
use PHPUnit\Framework\TestCase;

class DemoTest extends TestCase
{
    public function testFramework()
    {
        $meeting = new Meeting('Some Meeting', new \DateTimeImmutable('now'));
        $this->assertInstanceOf(Meeting::class, $meeting);
    }
}
