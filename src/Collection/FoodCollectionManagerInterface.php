<?php

namespace App\Collection;

use App\Entity\FoodItem;

interface FoodCollectionManagerInterface
{
    public function listFood(string $itemType, ?string $query): array;
    public function addFood(FoodItem $foodItem): void;
    public function removeFood(string $itemType, int $id): bool;
}