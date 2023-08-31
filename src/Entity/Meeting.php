<?php

namespace App\Entity;

use App\Exception\ParticipantsLimitReachedException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity]
#[ORM\Table(name: '`meetings`')]
class Meeting
{
    const PARTICIPANTS_LIMIT = 5;
    const STATUS_OPEN = 'open to registration'; // Note: I created the string as in Readme, but I prefer status codes like 'open-to-registration'
    const STATUS_FULL = 'full';
    const STATUS_IN_SESSION = 'in session';
    const STATUS_DONE = 'done';

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

    public function __construct(string $name, \DateTimeImmutable $startTime)
    {
        $this->id = uniqid();
        $this->name = $name;
        $this->startTime = $startTime;
        $this->endTime = $startTime->add(\DateInterval::createFromDateString('1 hour'));
        $this->participants = new ArrayCollection();
    }

    public function addAParticipant(User $participant): void
    {
        if(!$this->canAddAParticipant()){
            throw new ParticipantsLimitReachedException();
        }

        $this->participants->add($participant);
    }

    public function canAddAParticipant(): bool
    {
        /**
         * Notes and how to do it better:
         * 1. There is no removeParticipants so I assume we count the participant as present event if it leaves the meeting.
         * 2. Too frequent query to DB. Should be cached and the limit should be stored in some key-value DB like Redis.
         * To be discussed, because it depends on business needs and other features.
         * 3. The logic should be moved to a service, especially when using some cache storage as dependency.
         */

        if($this->participants->count() < self::PARTICIPANTS_LIMIT){
            return true;
        }

        return false;
    }

    public function getStatus(): string
    {
        /**
         * Notes and how to do it better:
         * 1. The logic should be moved to a service
         */

        $now = new \DateTimeImmutable();

        if($this->endTime < $now){
            return self::STATUS_DONE;
        }
        
        if($this->startTime < $now){
            return self::STATUS_IN_SESSION;
        }
        
        if(!$this->canAddAParticipant()){
            // I use canAddAParticipant beacuse is well tested
            return self::STATUS_FULL;
        }

        return self::STATUS_OPEN;
    }
}
