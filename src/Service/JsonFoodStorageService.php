<?php

namespace App\Service;

use App\Collection\FruitCollection;
use App\Collection\VegetableCollection;
use App\Entity\FoodItem;
use App\Entity\Fruit;
use App\Entity\Vegetable;
use App\Service\JsonStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

class JsonFoodStorageService implements JsonStorageInterface
{


    public function __construct(private FruitCollection $fruitCollection, private VegetableCollection $vegetableCollection)
    {
    }

    public function loadData(string $jsonData): void
    {
        $data = json_decode($jsonData);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException("Invalid JSON data");
        }

        foreach ($data as $item) {
            $entity = null;
            if($item->type === "fruit"){
                $entity = new Fruit();
            }elseif ($item->type === "vegetable"){
                $entity = new Vegetable();
            }
            if($entity instanceof FoodItem){
                $quantity = $this->convertToGrams($item->quantity, $item->unit);
                $entity->setQuantity($quantity);
                $entity->setName($item->name);
                if ($item instanceof Fruit) {
                    $this->fruitCollection->add($item);
                } else {
                    $this->vegetableCollection->add($item);
                }
            }else{
                throw new \InvalidArgumentException("Invalid Type of item");
            }
        }

    }

    private function convertToGrams(float $weight, string $unit): float
    {
        return match ($unit) {
            'g' => $weight,
            'kg' => ($weight * 1000),
            default => throw new \InvalidArgumentException('Unsupported unit: ' . $unit),
        };
    }
}