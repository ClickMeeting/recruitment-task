<?php

namespace App\Entity;

use DateInterval;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity]
#[ORM\Table(name: '`meetings`')]
class Meeting
{

    private const DEFAULT_USER_LIMIT = 5;
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

    private int $userLimit;

    public function __construct(string $name, \DateTimeImmutable $startTime)
    {
        $this->id = uniqid();
        $this->name = $name;
        $this->startTime = $startTime;
        $this->userLimit = self::DEFAULT_USER_LIMIT;
        $this->endTime = $startTime->add(DateInterval::createFromDateString('1 hour'));
        $this->participants = new ArrayCollection();
    }

    /**
     * @throws Exception
     */
    public function addAParticipant(User $participant): void
    {
        $this->participants->count() >= $this->userLimit && throw new Exception('Too many participants');
        $this->participants->add($participant);
    }
}