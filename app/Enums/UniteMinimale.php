<?php

namespace App\Enums;

enum UniteMinimale: string
{
    case GRAMME = 'g';
    case KILOGRAMME = 'kg';
    case MILLILITRE = 'ml';
    case CENTILITRE = 'cl';
    case DECILITRE = 'dl';
    case LITRE = 'l';
    case CUILLERE_CAFE = 'cc';
    case CUILLERE_SOUPE = 'cs';
    case PINCEE = 'pincee';
    case UNITE = 'unite';

    public function toString(): string
    {
        return match($this) {
            self::GRAMME => 'gramme',
            self::KILOGRAMME => 'kilogramme',
            self::MILLILITRE => 'millilitre',
            self::CENTILITRE => 'centilitre',
            self::DECILITRE => 'décilitre',
            self::LITRE => 'litre',
            self::CUILLERE_CAFE => 'cuillère à café',
            self::CUILLERE_SOUPE => 'cuillère à soupe',
            self::PINCEE => 'pincée',
            self::UNITE => 'unité'
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getConversionRate(string $from, string $to): float
    {
        $conversions = [
            'cc' => ['ml' => 5],
            'cs' => ['ml' => 15],
            'pincee' => ['g' => 1.5],
            'cl' => ['ml' => 10],
            'dl' => ['ml' => 100],
            'l' => ['ml' => 1000],
            'litre' => ['ml' => 1000],
            'kg' => ['g' => 1000],
            'unite' => ['unite' => 1]
        ];

        return $conversions[$from][$to] ?? 1;
    }

    public static function getUniteClassiquePermise(string $uniteMinimale): array
    {
        $mapping = [
            'g' => ['kg'],
            'kg' => ['kg'],
            'ml' => ['litre'],
            'cl' => ['litre'],
            'dl' => ['litre'],
            'l' => ['litre'],
            'cc' => ['kg', 'litre'],
            'cs' => ['kg', 'litre'],
            'pincee' => ['kg'],
            'unite' => ['unite']
        ];

        return $mapping[$uniteMinimale] ?? ['unite'];
    }
}
