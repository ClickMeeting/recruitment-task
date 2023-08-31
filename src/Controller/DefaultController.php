<?php

namespace App\Controller;

use App\Repository\MeetingRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DefaultController
{
    private MeetingRepository $meetingRepository;

    public function __construct(MeetingRepository $meetingRepository)
    {
        $this->meetingRepository = $meetingRepository;
    }

    #[Route('/meetings/{id}', name: 'meeting')]
    public function meeting(string $id): Response
    {
        $meeting = $this->meetingRepository->get($id);
        return new JsonResponse($meeting);
    }

    #[Route('/meetings/{meetingId}/status', name: 'meeting_status')]
    public function meetingStatus(string $meetingId): Response
    {
        $meeting = $this->meetingRepository->get($meetingId);
        return new JsonResponse(['status' => $meeting->getStatus()]);
    }

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return new Response('<h1>Hello</h1>');
    }
}
