<?php
namespace App\Command;

use App\Document\{Hotel, Chambre, Client};
use App\Domain\{DateRange, ReservationServiceInterface};
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:demo:reservation')]
final class DemoReservationCommand extends Command
{
    protected static $defaultName = 'app:demo:reservation';

    public function __construct(
        private readonly DocumentManager $dm,
        private readonly ReservationServiceInterface $reservations
    ) { parent::__construct(); }

    protected function execute(InputInterface $in, OutputInterface $out): int
    {
        // 1) seed minimal
        $hotel = new Hotel('QuickHome Paris', '***', 'Paris');
        $email = 'alice+'.time().'@example.com';
        $client = new Client($email, 'hash');
        $ch101 = new Chambre($hotel, 101, 1, 'Standard', 1);
        $ch102 = new Chambre($hotel, 102, 1, 'Standard', 1);

        $this->dm->persist($hotel);
        $this->dm->persist($client);
        $this->dm->persist($ch101);
        $this->dm->persist($ch102);
        $this->dm->flush();

        // 2) créer une résa
        $range = new DateRange(new \DateTimeImmutable('+1 day'), new \DateTimeImmutable('+3 days'));
        $resa = $this->reservations->createReservation($client, $hotel, [$ch101], $range, 'Late check-in');

        $out->writeln('Reservation OK: '.$resa->getNumReservation());

        // 3) tenter une double réservation sur la même chambre/période -> doit échouer
        try {
            $this->reservations->createReservation($client, $hotel, [$ch101], $range);
            $out->writeln('<error>BUG: double réservation acceptée</error>');
        } catch (\Throwable $e) {
            $out->writeln('<info>Double réservation bien refusée: '.$e->getMessage().'</info>');
        }

        return Command::SUCCESS;
    }
}
