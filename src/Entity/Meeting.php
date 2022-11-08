<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Exception\ParticipantsLimitExceededException;

#[ORM\Entity]
#[ORM\Table(name: '`meetings`')]
class Meeting
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\Column]
    public readonly string $id;

    #[ORM\Column(length: 255)]
    public readonly string $name;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public readonly \DateTimeImmutable $startTime;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public readonly \DateTimeImmutable $endTime;

    #[ORM\ManyToMany(targetEntity: User::class)]
    public Collection $participants;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    public readonly ?int $participantsLimit;

    public function __construct(string $name, \DateTimeImmutable $startTime, int $participantsLimit = 5)
    {
        $this->id = uniqid();
        $this->name = $name;
        $this->startTime = $startTime;
        $this->endTime = $startTime->add(\DateInterval::createFromDateString('1 hour'));
        $this->participants = new ArrayCollection();
        $this->participantsLimit = $participantsLimit;
    }

    /**
     *
     * @throws ParticipantsLimitExceededException
     */
    public function addAParticipant(User $participant): void
    {
        if ($this->isMeetingFull()) {
            throw new ParticipantsLimitExceededException('Participants limit exceeded');
        }

        $this->participants->add($participant);
    }

    /**
     * Checks if meeting is full.
     *
     * @return bool
     */
    public function isMeetingFull(): bool
    {
        $meetingIsFull = false;

        if (is_null($this->participantsLimit)) {
            return $meetingIsFull;
        }

        if ($this->participants->count() >= $this->participantsLimit) {
            $meetingIsFull = true;

            return $meetingIsFull;
        }

        return $meetingIsFull;
    }
}
