<?php

namespace App\Tests\PhpUnit;

use App\Entity\Meeting;
use App\Entity\User;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;

class UserLimitTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testUserLimit(): void
    {
        $this->expectExceptionMessage('Too many participants');
        $meeting = new Meeting('Some Meeting', new DateTimeImmutable('now'));
        for ($i = 0; $i <= $meeting->getUserLimit(); $i++) {
            $meeting->addAParticipant(new User('User' . $i));
        }
    }
}
