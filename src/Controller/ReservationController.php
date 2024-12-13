<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $reservations = $entityManager->getRepository(Reservation::class)->findBy(['user' => $user]);

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
            'user' => $user,
        ]);
    }

    #[Route('/reservation/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $logger->info('Form submitted');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $logger->info('Form is valid');
            $reservation->setUser($this->getUser());

            try {
                $entityManager->persist($reservation);
                $entityManager->flush();
                $logger->info('Reservation persisted and flushed to the database');
            } catch (\Exception $e) {
                $logger->error('Error persisting reservation: ' . $e->getMessage());
            }

            return $this->redirectToRoute('app_reservation');
        } else {
            if ($form->isSubmitted()) {
                $logger->error('Form is not valid');
                foreach ($form->getErrors(true) as $error) {
                    $logger->error($error->getMessage());
                }
            }
        }

        return $this->render('reservation/new.html.twig', [
            'reservationForm' => $form->createView(),
        ]);
    }

    #[Route('/reservations', name: 'app_all_reservations', methods: ['GET'])]
    public function allReservations(EntityManagerInterface $entityManager): Response
    {
        $reservations = $entityManager->getRepository(Reservation::class)->findAll();

        return $this->render('reservation/all.html.twig', [
            'reservations' => $reservations,
        ]);
    }
}
