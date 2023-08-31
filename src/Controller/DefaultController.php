<?php

namespace App\Controller;

use App\Entity\MeetingRate;
use App\Repository\MeetingRepository;
use App\Repository\MeetingRateRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Exception\ValidationException;

final class DefaultController
{
    private MeetingRepository $meetingRepository;

    private UserRepository $userRepository;

    private MeetingRateRepository $meetingRateRepository;

    public function __construct(MeetingRepository $meetingRepository, UserRepository $userRepository, MeetingRateRepository $meetingRateRepository)
    {
        $this->meetingRepository = $meetingRepository;
        $this->userRepository = $userRepository;
        $this->meetingRateRepository = $meetingRateRepository;
    }

    #[Route('/meetings/{id}', name: 'meeting')]
    public function meeting(string $id): Response
    {
        $meeting = $this->meetingRepository->get($id);
        return new JsonResponse($meeting);
    }

    #[Route('/meetings/{meetingId}/rate', name: 'rate_meeting', methods: ['POST'])]
    public function rateMeeting(string $meetingId, Request $request): Response
    {
        /**
         * Notes:
         * - we may use some model and parse request body to the object
         * - we may use form component
         * - we should get user from session, etc.
         * - we should use JSON as request body
         * - we should use some Factory to create new entities
         * - we may use Request models with validation constraints it's useful for API with multiple versions in use
         */

        $rateValue = $request->get('value');
        $userId = $request->get('userId');

        if(empty($rateValue) || empty($userId)){
            return new Response('missing userId or value in request', Response::HTTP_BAD_REQUEST);
            // Note: should use command error response
        }

        if(!is_numeric($rateValue)){
            return new Response('Rate value must ba a numeric', Response::HTTP_BAD_REQUEST);
            // Note: should use command error response
        }

        $meeting = $this->meetingRepository->get($meetingId);
        $user = $this->userRepository->get($userId);
        
        $existingMeeting = $this->meetingRateRepository->getByMeetingAndParticipant($meeting, $user);
        if($existingMeeting !== null){
            return new Response('This user has already rated the meeting', Response::HTTP_CONFLICT);
            // Note: should use command error response
            // This validation may be done with UniqueEntity() constraint from validator component
        }

        $newRate = new MeetingRate($user, $meeting, $rateValue);

        try {
            $newRate->validate();
        } catch(ValidationException $e){
            return new Response($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
            // Note: should use command error response
        }

        $this->meetingRateRepository->add($newRate);

        return new Response('', Response::HTTP_CREATED);
    }

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return new Response('<h1>Hello</h1>');
    }
}
