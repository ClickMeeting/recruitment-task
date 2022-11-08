<?php

namespace App\Tests\PhpUnit;

use App\Entity\Meeting;
use App\Entity\User;
use App\Exception\ParticipantsLimitExceededException;
use PHPUnit\Framework\TestCase;

class MeetingTest extends TestCase
{
    public function testParticipantsLimit()
    {
        $meeting = new Meeting('Test meeting', new \DateTimeImmutable('now'));
        for($i = 1; $i <= 5; $i++){
            $user = new User($i);
            $meeting->addAParticipant($user);
        }

        $this->assertSame(
            true,
            $meeting->isMeetingFull(),
            "actual value is not same as expected value"
        );
    }

    public function testParticipantsLimitExceed()
    {
        $this->expectException(ParticipantsLimitExceededException::class);

        $meeting = new Meeting('Test meeting', new \DateTimeImmutable('now'));
        for($i = 1; $i <= 10; $i++){
            $user = new User($i);
            $meeting->addAParticipant($user);
        }
    }
}
