<?php

namespace App\Repository;

use App\Entity\Meeting;
use App\Entity\MeetingRate;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class MeetingRateRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(MeetingRate $newMeetingRate): void
    {
        $this->entityManager->persist($newMeetingRate);
        $this->entityManager->flush();
    }

    public function get(string $MeetingRateId): ?MeetingRate
    {
        return $this->entityManager->getRepository(MeetingRate::class)->find($MeetingRateId);
    }

    public function getByMeetingAndParticipant(Meeting $meeting, User $participant): ?MeetingRate
    {
        return $this->entityManager->getRepository(MeetingRate::class)->find(
            ['meeting' => $meeting, 'participant' => $participant]
        );
    }

    public function findAll()
    {
        return $this->entityManager->getRepository(MeetingRate::class)->findAll();
    }
}