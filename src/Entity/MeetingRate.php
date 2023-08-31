<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Exception\ValidationException;

#[ORM\Entity]
#[ORM\Table(name: '`meeting_rates`')]
#[ORM\Index(name: 'participant_meeting_idx', columns: ['participant', 'meeting'], flags: ['unique'])]
class MeetingRate
{
    const MIN_RATE = 1;
    const MAX_RATE = 5;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\Column]
    public readonly string $id;

    #[ORM\Column(type: Types::INTEGER)]
    public readonly int $rate;

    #[ORM\ManyToOne(targetEntity: User::class)]
    public User $participant;

    #[ORM\ManyToOne(targetEntity: Meeting::class)]
    public Meeting $meeting;

    public function __construct(User $participant, Meeting $meeting, int $rate)
    {
        $this->id = uniqid(); // Possible conflicts
        $this->participant = $participant;
        $this->meeting = $meeting;
        $this->rate = $rate;
    }
    
    public function validate(): void
    {
        if($this->rate < self::MIN_RATE)
        {
            throw new ValidationException(sprintf("Rate value can not be lower than %d", self::MIN_RATE));
        }

        if($this->rate > self::MAX_RATE)
        {
            throw new ValidationException(sprintf("Rate value can not be greater than %d", self::MAX_RATE));
        }

        // Note: this validation should be moved outside entity. It may affect DB performance, so it should use some cache
        if(!$this->meeting->participants->contains($this->participant))
        {
            throw new ValidationException("The user must participate in the meeting");
        }
    }
}
