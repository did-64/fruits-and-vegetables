<?php

namespace App\Collection;

use App\Entity\Fruit;
use App\Entity\Vegetable;

enum EnumFoodItem: string
{
    case FRUIT = 'fruit';
    case VEGETABLE = 'vegetable';

    public function getInstance(): object
    {
        return match ($this) {
            self::FRUIT => new Fruit(),
            self::VEGETABLE => new Vegetable(),
        };
    }

    public static function getInstanceIfExists(string $value): ?object
    {
        $case = self::tryFrom($value);
        return $case?->getInstance();
    }
}