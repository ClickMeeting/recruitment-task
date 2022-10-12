<?php

namespace App\Repository;

use App\Entity\Meeting;
use Doctrine\ORM\EntityManagerInterface;

class MeetingRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(Meeting $newMeeting): void
    {
        $this->entityManager->persist($newMeeting);
        $this->entityManager->flush();
    }

    public function get(string $meetingId): Meeting
    {
        return $this->entityManager->getRepository(Meeting::class)->find($meetingId);
    }

    public function findAll()
    {
        return $this->entityManager->getRepository(Meeting::class)->findAll();
    }
}