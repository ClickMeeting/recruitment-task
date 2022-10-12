<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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
        $this->participants->add($participant);
    }
}
