<?php

namespace App\Tests\PhpUnit\Entity;

use App\Entity\Meeting;
use App\Entity\User;
use App\Exception\ParticipantsLimitReachedException;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class MeetingTest extends TestCase
{
    public function testAddAParticipant()
    {
        // Create test participant
        $participant = new User("Test_user");

        // Test if add method on participants collection is triggered  when canAddAParticipant returns true
        $meetingEntityMock = $this->getMockBuilder(Meeting::class)->setConstructorArgs(["Test_meeting", new \DateTimeImmutable()])->onlyMethods(['canAddAParticipant'])->getMock();
        $meetingEntityMock->participants = $this->createMock(ArrayCollection::class);
        $meetingEntityMock->expects($this->once())->method('canAddAParticipant')->willReturn(true);
        $meetingEntityMock->participants->expects($this->once())->method('add')->with($participant);
        $meetingEntityMock->addAParticipant($participant);

        // Test if exception is thrown when canAddAParticipant returns false
        $meetingEntityMock = $this->getMockBuilder(Meeting::class)->setConstructorArgs(["Test_meeting", new \DateTimeImmutable()])->onlyMethods(['canAddAParticipant'])->getMock();
        $meetingEntityMock->expects($this->once())->method('canAddAParticipant')->willReturn(false);
        $this->expectException(ParticipantsLimitReachedException::class);
        $meetingEntityMock->addAParticipant($participant);

        /**
         * Notes:
         * We don't test canAddAParticipant logic here. We test reaction to it's results.
         * 
         * I recreate new $meetingEntityMock for each test case because there
         * is no option to change willReturn value.
         */
    }

    public function testCanAddAParticipant()
    {
        // Test if the constant has correct value, because we use the constant in the test.
        $this->assertEquals(5, Meeting::PARTICIPANTS_LIMIT);

        // Create fake count values and name it to make test code code readable
        $lessThanLimit = Meeting::PARTICIPANTS_LIMIT - 1;
        $exactLimit = Meeting::PARTICIPANTS_LIMIT;
        $moreThanLimit = Meeting::PARTICIPANTS_LIMIT + 1;

        // Create Meeting with mocked participants collection
        $meetingEntityMock = new Meeting('Test_meeting', new \DateTimeImmutable());

        // Test if canAddAParticipant returns true when participants count is lower than the limit
        $participantCollectionMock = $this->createMock(ArrayCollection::class);
        $meetingEntityMock->participants = $participantCollectionMock;
        $participantCollectionMock->method('count')->willReturn($lessThanLimit);
        $this->assertTrue($meetingEntityMock->canAddAParticipant());

        // // Test if canAddAParticipant returns false when participants is equal to the limit
        $participantCollectionMock = $this->createMock(ArrayCollection::class);
        $meetingEntityMock->participants = $participantCollectionMock;
        $participantCollectionMock->method('count')->willReturn($exactLimit);
        $this->assertFalse($meetingEntityMock->canAddAParticipant());

        // Test if canAddAParticipant returns false when participants count is greater than the limit
        $participantCollectionMock = $this->createMock(ArrayCollection::class);
        $meetingEntityMock->participants = $participantCollectionMock;
        $participantCollectionMock->method('count')->willReturn($moreThanLimit);
        $this->assertFalse($meetingEntityMock->canAddAParticipant());

        /**
         * Notes:
         * I recreate new $participantCollectionMock for each test case because there
         * is no option to change willReturn value.
         * 
         * Using willReturnOnConsecutiveCalls or callback will make test code less readable
         */
    }

    
    public function testGetStatus()
    {
        // Create helper anonymous function which creates Meeting mocks
        $createMeeting = function(string $startTimeStr, bool $canAddAParticipant): Meeting{
            $startTime = new DateTimeImmutable($startTimeStr);
            $meetingEntityMock = $this->getMockBuilder(Meeting::class)->setConstructorArgs(["Test_meeting", $startTime])->onlyMethods(['canAddAParticipant'])->getMock();
            $meetingEntityMock->method('canAddAParticipant')->willReturn($canAddAParticipant);
            return $meetingEntityMock;
        };

        // Test meeting open - starts in 7 hours and can add participants
        $meetingOpen = $createMeeting('+7 hours', true);
        $this->assertEquals('open to registration', $meetingOpen->getStatus());

        // Test meeting open - starts in 7 hours and can not add participants
        $meetingFull = $createMeeting('+7 hours', false);
        $this->assertEquals('full', $meetingFull->getStatus());

        // Test meeting open - started 5 minutes ago and both can and can not add participants
        $meetingInSession = $createMeeting('-5 minutes', true);
        $this->assertEquals('in session', $meetingInSession->getStatus());
        $meetingInSession = $createMeeting('-5 minutes', false);
        $this->assertEquals('in session', $meetingInSession->getStatus());

        // Test meeting open - started 2 hours ago and both can and can not add participants
        $meetingDone = $createMeeting('-2 hours', true);
        $this->assertEquals('done', $meetingDone->getStatus());
        $meetingDone = $createMeeting('-2 hours', false);
        $this->assertEquals('done', $meetingDone->getStatus());
    }
}
