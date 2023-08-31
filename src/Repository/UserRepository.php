<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(User $newUser): void
    {
        $this->entityManager->persist($newUser);
        $this->entityManager->flush();
    }

    public function get(string $UserId): User
    {
        return $this->entityManager->getRepository(User::class)->find($UserId);
    }

    public function findAll()
    {
        return $this->entityManager->getRepository(User::class)->findAll();
    }
}