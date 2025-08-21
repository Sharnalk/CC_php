<?php

namespace App\Service;

use App\Domain\ReservationNumberGeneratorInterface;

final class ReservationNumberGenerator implements ReservationNumberGeneratorInterface
{
    public function generate(): string
    {

        $date = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('Ymd');
        $rand = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        return $date . '-' . $rand;
    }
}
