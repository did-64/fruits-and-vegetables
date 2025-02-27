<?php

namespace App\Collection;

use stdClass;

interface FoodCollectionManagerInterface
{
    public function listFood(string $itemType, ?string $query): array;
    public function addFood(stdClass $foodItem): void;
    public function removeFood(string $itemType, int $id): void;
}