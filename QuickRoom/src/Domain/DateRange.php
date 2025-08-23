<?php

namespace App\Domain;

final class DateRange
{
    public function __construct(
        public readonly \DateTimeImmutable $start,
        public readonly \DateTimeImmutable $end
    ) {
        if ($end <= $start) {
            throw new \InvalidArgumentException('DateRange invalide: end must be > start.');
        }
    }
}
