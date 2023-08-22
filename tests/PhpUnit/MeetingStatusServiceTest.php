<?php

namespace App\Tests\PhpUnit;

use App\Entity\Meeting;
use App\Entity\User;
use App\Service\Meetings\StatusService;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;

class MeetingStatusServiceTest extends TestCase
{
    /**
     * @throws Exception
     * @dataProvider meetingData
     */
    public function testMeetingStatusWithDataProvider(Meeting $meeting, string $expected): void
    {
        $statusService = new StatusService();
        $this->assertEquals($expected, $statusService->getStatus($meeting));
    }
    /**
     * @throws Exception
     */
    private function meetingData(): array
    {
        $data[] = [
            'meeting' => new Meeting('Some Meeting1', new DateTimeImmutable('now')),
            'expected' => 'in session',
        ];

        $data[] = [
            'meeting' => new Meeting('Some Meeting2', new DateTimeImmutable('1990-01-01')),
            'expected' => 'done',
        ];

        $meeting = new Meeting('Some Meeting2', new DateTimeImmutable('2990-01-01'));
        $data[] = [
            'meeting' => $this->getFullMeeting($meeting),
            'expected' => 'full',
        ];
        return $data;
    }

    /**
     * @throws Exception
     */
    public function getFullMeeting(Meeting $meeting): Meeting
    {
        for ($i = 0; $i < $meeting->getUserLimit(); $i++) {
            $meeting->addAParticipant(new User('User' . $i));
        }
        return $meeting;
    }
}