<?php

namespace App\Enums;

enum UniteClassique: string
{
    case KILOGRAMME = 'kg';
    case LITRE = 'litre';
    case UNITE = 'unite';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getBaseUnit(string $uniteClassique): string
    {
        $mapping = [
            'kg' => 'g',
            'litre' => 'ml',
            'unite' => 'unite'
        ];

        return $mapping[$uniteClassique] ?? 'unite';
    }
}
