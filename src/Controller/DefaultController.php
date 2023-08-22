<?php

namespace App\Controller;

use App\Repository\MeetingRepository;
use App\Service\Meetings\StatusService;
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

    #[Route('/meetings/{meetingId}', name: 'meeting')]
    public function meeting(string $meetingId): Response
    {
        $meeting = $this->meetingRepository->get($meetingId);
        return new JsonResponse($meeting);
    }

    #[Route('/meetings/{meetingId}/status', name: 'status')]
    public function status(string $meetingId): Response
    {
        $meeting = $this->meetingRepository->get($meetingId);

        $meetingStatus = new StatusService();

        return new JsonResponse($meetingStatus->getStatus($meeting));
    }

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return new Response('<h1>Hello</h1>');
    }
}
