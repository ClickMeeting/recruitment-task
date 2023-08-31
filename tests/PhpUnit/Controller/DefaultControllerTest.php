<?php

namespace Tests\App\Controller;

use App\Entity\Meeting;
use App\Entity\MeetingRate;
use App\Entity\User;
use App\Repository\MeetingRepository;
use App\Repository\MeetingRateRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testRateMeeting()
    {
        $rateObjectFromCb = null;
        $client = static::createClient();
        $container = static::getContainer();

        // Fake test entities
        $rateValue = 2;
        $userId = 'user-1';
        $meetingId = 'meeting-1';
        $user = new User('Test_user');
        $meeting = new Meeting('Test_meeting', new DateTimeImmutable());
        $meeting->addAParticipant($user);

        // Mock repositories
        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->method('get')->with($userId)->willReturn($user);
        $meetingRepositoryMock = $this->createMock(MeetingRepository::class);
        $meetingRepositoryMock->method('get')->with($meetingId)->willReturn($meeting);
        $rateRepositoryMock = $this->createMock(MeetingRateRepository::class);
        $rateRepositoryMock->expects($this->once())->method('getByMeetingAndParticipant')->willReturn(null);
        $rateRepositoryMock->expects($this->once())->method('add')->with(self::callback(function ($rateObject) use ($user, $meeting, $rateValue): bool {
            // Test new object:
            self::assertInstanceOf(MeetingRate::class, $rateObject);
            self::assertEquals($user, $rateObject->participant);
            self::assertEquals($rateValue, $rateObject->rate);
            self::assertEquals($meeting, $rateObject->meeting);
         
            return true;
        }));
        $container->set(MeetingRepository::class, $meetingRepositoryMock);
        $container->set(UserRepository::class, $userRepositoryMock);
        $container->set(MeetingRateRepository::class, $rateRepositoryMock);

        // Do request
        $reqBody = ['userId' => $userId, 'value' => $rateValue];
        $client->request('POST', '/meetings/meeting-1/rate', $reqBody);
        $response = $client->getResponse();

        // Test response status code and decode JSON from response body
        $this->assertEquals(201, $response->getStatusCode());
    }
    
    public function testRateMeetingWithInvalidRateValue()
    {
        $rateObjectFromCb = null;
        $client = static::createClient();
        $container = static::getContainer();

        // Fake test entities
        $rateValue = 6; // Limit is 5
        $userId = 'user-1';
        $meetingId = 'meeting-1';
        $user = new User('Test_user');
        $meeting = new Meeting('Test_meeting', new DateTimeImmutable());
        $meeting->addAParticipant($user);

        // Mock repositories
        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->method('get')->with($userId)->willReturn($user);
        $meetingRepositoryMock = $this->createMock(MeetingRepository::class);
        $meetingRepositoryMock->method('get')->with($meetingId)->willReturn($meeting);
        $rateRepositoryMock = $this->createMock(MeetingRateRepository::class);
        $rateRepositoryMock->expects($this->once())->method('getByMeetingAndParticipant')->willReturn(null);
        $rateRepositoryMock->expects($this->never())->method('add');
        $container->set(MeetingRepository::class, $meetingRepositoryMock);
        $container->set(UserRepository::class, $userRepositoryMock);
        $container->set(MeetingRateRepository::class, $rateRepositoryMock);

        // Do request
        $reqBody = ['userId' => $userId, 'value' => $rateValue];
        $client->request('POST', '/meetings/meeting-1/rate', $reqBody);
        $response = $client->getResponse();

        // Test response status code and decode JSON from response body
        $this->assertEquals(422, $response->getStatusCode());
    }
    
    public function testRateMeetingWithNotAParticipant()
    {
        $rateObjectFromCb = null;
        $client = static::createClient();
        $container = static::getContainer();

        // Fake test entities
        $rateValue = 4;
        $userId = 'user-1';
        $meetingId = 'meeting-1';
        $user = new User('Test_user');
        $meeting = new Meeting('Test_meeting', new DateTimeImmutable());

        // Mock repositories
        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->method('get')->with($userId)->willReturn($user);
        $meetingRepositoryMock = $this->createMock(MeetingRepository::class);
        $meetingRepositoryMock->method('get')->with($meetingId)->willReturn($meeting);
        $rateRepositoryMock = $this->createMock(MeetingRateRepository::class);
        $rateRepositoryMock->expects($this->once())->method('getByMeetingAndParticipant')->willReturn(null);
        $rateRepositoryMock->expects($this->never())->method('add');
        $container->set(MeetingRepository::class, $meetingRepositoryMock);
        $container->set(UserRepository::class, $userRepositoryMock);
        $container->set(MeetingRateRepository::class, $rateRepositoryMock);

        // Do request
        $reqBody = ['userId' => $userId, 'value' => $rateValue];
        $client->request('POST', '/meetings/meeting-1/rate', $reqBody);
        $response = $client->getResponse();

        // Test response status code and decode JSON from response body
        $this->assertEquals(422, $response->getStatusCode());
    }
    
    public function testRateMeetingWithDuplicatedRate()
    {
        $rateObjectFromCb = null;
        $client = static::createClient();
        $container = static::getContainer();

        // Fake test entities
        $rateValue = 4;
        $userId = 'user-1';
        $meetingId = 'meeting-1';
        $user = new User('Test_user');
        $meeting = new Meeting('Test_meeting', new DateTimeImmutable());
        $meeting->addAParticipant($user);
        $existingRate = new MeetingRate($user, $meeting, 3);

        // Mock repositories
        $userRepositoryMock = $this->createMock(UserRepository::class);
        $userRepositoryMock->method('get')->with($userId)->willReturn($user);
        $meetingRepositoryMock = $this->createMock(MeetingRepository::class);
        $meetingRepositoryMock->method('get')->with($meetingId)->willReturn($meeting);
        $rateRepositoryMock = $this->createMock(MeetingRateRepository::class);
        $rateRepositoryMock->expects($this->once())->method('getByMeetingAndParticipant')->willReturn($existingRate);
        $rateRepositoryMock->expects($this->never())->method('add');
        $container->set(MeetingRepository::class, $meetingRepositoryMock);
        $container->set(UserRepository::class, $userRepositoryMock);
        $container->set(MeetingRateRepository::class, $rateRepositoryMock);

        // Do request
        $reqBody = ['userId' => $userId, 'value' => $rateValue];
        $client->request('POST', '/meetings/meeting-1/rate', $reqBody);
        $response = $client->getResponse();

        // Test response status code and decode JSON from response body
        $this->assertEquals(409, $response->getStatusCode());
    }
}