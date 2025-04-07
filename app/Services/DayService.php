<?php

namespace App\Services;

class DayService
{
    private array $dayMap = [
        'dimanche' => 0,
        'lundi' => 1,
        'mardi' => 2,
        'mercredi' => 3,
        'jeudi' => 4,
        'vendredi' => 5,
        'samedi' => 6,
    ];

    public function getDayNumber(string $day): int
    {
        return $this->dayMap[strtolower($day)] ?? 0;
    }

    public function getAllDays(): array
    {
        return array_keys($this->dayMap);
    }
}
