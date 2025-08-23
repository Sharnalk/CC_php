<?php

namespace App\Domain;

interface ReservationNumberGeneratorInterface
{
    public function generate(): string;
}
