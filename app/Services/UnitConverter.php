<?php

namespace App\Services;

class UnitConverter {
    private static $conversionRules = [
        'g' => ['unit' => 'kg', 'factor' => 0.001],
        'kg' => ['unit' => 'kg', 'factor' => 1],
        'ml' => ['unit' => 'litre', 'factor' => 0.001],
        'cl' => ['unit' => 'litre', 'factor' => 0.01],
        'dl' => ['unit' => 'litre', 'factor' => 0.1],
        'l' => ['unit' => 'litre', 'factor' => 1],
        'cc' => ['unit' => 'litre', 'factor' => 0.001],
        'cs' => ['unit' => 'litre', 'factor' => 0.015],
        'pincee' => ['unit' => 'kg', 'factor' => 0.001],
        'unite' => ['unit' => 'unité', 'factor' => 1]
    ];

    public static function convert($value, $unit) {
        if (!isset(self::$conversionRules[$unit])) {
            return [$value, $unit];
        }

        $rule = self::$conversionRules[$unit];
        $convertedValue = $value * $rule['factor'];

        // Formatage intelligent
        if ($convertedValue < 0.001) {
            return [($value), $unit];
        } elseif ($convertedValue < 1) {
            return [($convertedValue * 1000), mb_substr($rule['unit'], 0, 1) . 'g'];
        } else {
            return [($convertedValue), $rule['unit']];
        }
    }
}
