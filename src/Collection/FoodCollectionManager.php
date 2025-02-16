<?php

namespace App\Collection;

use App\Entity\FoodItem;
use App\Exception\CustomHttpException;

class FoodCollectionManager implements FoodCollectionManagerInterface
{

    public function __construct(
        private FruitCollection $fruitCollection,
        private VegetableCollection $vegetableCollection
    ) {}

    public function listFood(string $itemType, ?string $query): array
    {
        $list =  match ($itemType) {
            'fruit' => $this->fruitCollection->list($query),
            'vegetable' => $this->vegetableCollection->list($query),
            default => null,
        };
        if($list === null) {
            throw new CustomHttpException("Invalid Type of item");
        }
        return $list;
    }

    public function addFood(FoodItem $foodItem): void
    {
        // TODO: Implement addFood() method.
    }

    public function removeFood(string $itemType, int $id): bool
    {
        $removeItem = match ($itemType) {
            'fruit' => $this->fruitCollection->remove($id),
            'vegetable' => $this->vegetableCollection->remove($id),
            default => throw new CustomHttpException("Invalid Type of item")
        };
        if ($removeItem === false)
            throw new CustomHttpException("No item found");
        return $removeItem;
    }
}