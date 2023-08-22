<?php

namespace App\Service\Meetings;

use App\Entity\Meeting;
use DateTimeImmutable;
use RuntimeException;

class StatusService
{
    private const STATUS_OPEN_TO_REGISTRATION = 'open to registration';
    private const STATUS_FULL = 'full';
    private const STATUS_IN_SESSION = 'in session';
    private const STATUS_DONE = 'done';

    public function getStatus(Meeting $meeting): string
    {
        $now = new DateTimeImmutable();

        if ($now < $meeting->startTime && !$meeting->checkIfMeetingIsFull()) {
            return self::STATUS_OPEN_TO_REGISTRATION;
        }
        if ($meeting->checkIfMeetingIsFull()) {
            return self::STATUS_FULL;
        }
        if ($now > $meeting->endTime) {
            return self::STATUS_DONE;
        }
        if ($now > $meeting->startTime && $now < $meeting->endTime) {
            return self::STATUS_IN_SESSION;
        }
        throw new RuntimeException('Unknown status');
    }
}