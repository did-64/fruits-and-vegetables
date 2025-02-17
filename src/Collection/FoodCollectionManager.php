<?php

namespace App\Collection;

use App\Entity\FoodItem;
use App\Entity\Fruit;
use App\Entity\Vegetable;
use App\Exception\CustomHttpException;
use stdClass;

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

    public function addFood(stdClass $foodItem): void
    {
        $entityName = ucfirst($foodItem->name);
        $entity = new $entityName();
        if($entity instanceof FoodItem){
            if(!is_float($foodItem->quantity) && !is_int($foodItem->quantity))
                throw new CustomHttpException("The value must be a number.");
            if(!is_string($foodItem->name) || empty($foodItem->name))
                throw new CustomHttpException("The value must be filled and type of string.");
            $quantity = $this->convertToGrams($foodItem->quantity, $foodItem->unit);
            $entity->setQuantity($quantity);
            $entity->setName($foodItem->name);
            if ($entity instanceof Fruit)
                $this->fruitCollection->add($entity);
            else if($entity instanceof Vegetable)
                $this->vegetableCollection->add($entity);
            else
                throw new CustomHttpException("This food item doesn't exist.");
        }else{
            throw new CustomHttpException("Invalid Type of item");
        }
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

    private function convertToGrams(float $weight, string $unit): float
    {
        return match ($unit) {
            'g' => $weight,
            'kg' => ($weight * 1000),
            default => throw new CustomHttpException('Unsupported unit: ' . $unit),
        };
    }
}