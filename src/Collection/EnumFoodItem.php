<?php

namespace App\Collection;

use App\Entity\FoodItem;
use App\Entity\Fruit;
use App\Entity\Vegetable;
use App\Exception\CustomHttpException;

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

    public static function getInstanceIfExists(string $value): FoodItem
    {
        $case = self::tryFrom($value);
        if ($case === null)
            throw new CustomHttpException("Invalid Type of Item");
        return $case->getInstance();
    }
}