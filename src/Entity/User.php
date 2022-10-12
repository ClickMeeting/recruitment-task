<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`users`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\Column(length: 13)]
    public readonly string $id;

    #[ORM\Column(length: 128, nullable: true)]
    public readonly string $name;

    public function __construct(string $name)
    {
        $this->id = uniqid();
        $this->name = $name;
    }
}
