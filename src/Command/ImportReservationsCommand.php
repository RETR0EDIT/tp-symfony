<?php

namespace App\Command;

use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;

class ImportReservationsCommand extends Command
{
    protected static $defaultName = 'app:import-reservations';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import reservations from a JSON file')
            ->addArgument('filePath', InputArgument::REQUIRED, 'Path to the JSON file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('filePath');

        if (!file_exists($filePath)) {
            $io->error('File not found: ' . $filePath);
            return Command::FAILURE;
        }

        $data = json_decode(file_get_contents($filePath), true);

        foreach ($data as $item) {
            $reservation = new Reservation();
            $reservation->setDate(new \DateTime($item['date']));
            $reservation->setTimeSlot($item['timeSlot']);
            $reservation->setEventName($item['eventName']);

            $user = $this->entityManager->getRepository(User::class)->find($item['user']['id']);
            if (!$user) {
                $user = new User();
                $user->setEmail($item['user']['email']);
                $user->setUserIdentifier($item['user']['userIdentifier']);
                $user->setRoles($item['user']['roles']);
                $user->setPassword($item['user']['password']);
                $user->setName($item['user']['name']);
                $user->setPhoneNumber($item['user']['phoneNumber']);
                $this->entityManager->persist($user);
            }

            $reservation->setUser($user);
            $this->entityManager->persist($reservation);
        }

        $this->entityManager->flush();
        $io->success('Reservations imported successfully.');

        return Command::SUCCESS;
    }
}
