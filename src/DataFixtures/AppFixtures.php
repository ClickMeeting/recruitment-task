<?php

namespace App\DataFixtures;

use App\Entity\Meeting;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $someUser = new User('Some User');
        $manager->persist($someUser);
        $anotherUser = new User('Another User');
        $manager->persist($anotherUser);
        $someMeeting = new Meeting('Meeting 1', new \DateTimeImmutable('2020-01-01'));
        $someMeeting->addAParticipant($someUser);
        $someMeeting->addAParticipant($anotherUser);
        $manager->persist($someMeeting);
        $anotherMeeting = new Meeting('Meeting 2', new \DateTimeImmutable('2020-01-02'));
        $manager->persist($anotherMeeting);
        $manager->flush();
    }
}
