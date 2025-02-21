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

    private function getEntityCollection(string $itemType): FoodCollectionInterface
    {
        return match ($itemType) {
            'fruit' => $this->fruitCollection,
            'vegetable' => $this->vegetableCollection,
            default => throw new CustomHttpException("Invalid Type of item")
        };
    }

    public function listFood(string $itemType, ?string $query): array
    {
        $collection = $this->getEntityCollection($itemType);
        return $collection->list($query);
    }

    public function addFood(stdClass $foodItem): void
    {
        $entity = EnumFoodItem::getInstanceFoodItem($foodItem->type);
        if(!is_float($foodItem->quantity) && !is_int($foodItem->quantity))
            throw new CustomHttpException("The value must be a number");
        if(!is_string($foodItem->name) || empty($foodItem->name))
            throw new CustomHttpException("The value must be filled and type of string");
        $quantity = $this->convertToGrams($foodItem->quantity, $foodItem->unit);
        $entity->setQuantity($quantity);
        $entity->setName($foodItem->name);
        $collection = $this->getEntityCollection($foodItem->type);
        $collection->add($entity);
    }

    public function removeFood(string $itemType, int $id): bool
    {
        $collection = $this->getEntityCollection($itemType);
        $removeItem = $collection->remove($id);
        if ($removeItem === false)
            throw new CustomHttpException("No item found");
        return true;
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