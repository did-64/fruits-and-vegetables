<?php

namespace App\Collection;

use App\Entity\FoodItem;
use App\Exception\CustomHttpException;
use ReflectionClass;

class FoodCollectionManager implements FoodCollectionManagerInterface
{

    /** @var FoodItem[] $collection */
    private array $foodItemsCollection = [];

    public function __construct(
        private FruitCollection $fruitCollection,
        private VegetableCollection $vegetableCollection
    ) {}

    protected function getEntityCollection(string $itemType): FoodCollectionInterface
    {
        return match (strtolower($itemType)) {
            'fruit' => $this->fruitCollection,
            'vegetable' => $this->vegetableCollection,
            default => throw new CustomHttpException("Invalid Type of item")
        };
    }

    public function hydrateCollection(array $data): void
    {
        foreach ($data as $foodItem) {
            $entity = EnumFoodItem::getInstanceFoodItem($foodItem->type);

            if(!is_float($foodItem->quantity) && !is_int($foodItem->quantity))
                throw new CustomHttpException("The value must be a number");

            if(!is_string($foodItem->name) || empty($foodItem->name))
                throw new CustomHttpException("The value must be filled and type of string");

            $quantity = $this->convertToGrams($foodItem->quantity, $foodItem->unit);
            $entity->setQuantity($quantity);
            $entity->setName($foodItem->name);
            $this->foodItemsCollection[] = $entity;
        }
    }

    public function listFood(string $itemType, ?string $query): array
    {
        $collection = $this->getEntityCollection($itemType);

        return $collection->list($query);
    }

    public function addCollection(): void
    {
        foreach ($this->foodItemsCollection as $foodItem) {
            $this->addFood($foodItem);
        }
    }

    protected function addFood(FoodItem $foodItem): void
    {
        $reflect = new ReflectionClass($foodItem);
        $collection = $this->getEntityCollection($reflect->getShortName());
        $collection->add($foodItem);
    }

    public function removeFood(string $itemType, int $id): void
    {
        $collection = $this->getEntityCollection($itemType);
        $removeItem = $collection->remove($id);

        if ($removeItem === false)
            throw new CustomHttpException("No item found");
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