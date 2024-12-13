<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/api/reservations', name: 'api_reservations', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $reservations = $entityManager->getRepository(Reservation::class)->findAll();
        return $this->json($reservations);
    }

    #[Route('/api/reservations', name: 'api_reservations_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $reservation = new Reservation();
        $reservation->setDate(new \DateTime($data['date']));
        $reservation->setTimeSlot($data['timeSlot']);
        $reservation->setEventName($data['eventName']);
        $reservation->setUser($entityManager->getRepository(User::class)->find($data['user_id']));

        $entityManager->persist($reservation);
        $entityManager->flush();

        return $this->json($reservation);
    }
}
