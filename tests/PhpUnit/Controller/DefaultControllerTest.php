<?php

namespace Tests\App\Controller;

use App\Entity\Meeting;
use App\Repository\MeetingRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testMeetingStatus()
    {
        $client = static::createClient();
        $container = static::getContainer();

        // Mock Meeting Repository
        $repositoryMock = $this->createMock(MeetingRepository::class);
        $meeting = new Meeting('Test_meeting', new DateTimeImmutable('-5 minutes'));
        $repositoryMock->expects($this->once())->method('get')->with('test-meeting-id')->willReturn($meeting);
        $container->set(MeetingRepository::class, $repositoryMock);

        // Do request
        $client->request('GET', '/meetings/test-meeting-id/status');
        $response = $client->getResponse();

        // Test response status code and decode JSON from response body
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent(), true);

        // test response body
        $this->assertEquals('in session', $data['status']);
    }
}