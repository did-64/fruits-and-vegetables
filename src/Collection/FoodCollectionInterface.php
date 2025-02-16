<?php

namespace App\Collection;

use App\Entity\FoodItem;

interface FoodCollectionInterface
{
    public function add(FoodItem $item): void;
    public function remove(int $id): bool;
    public function list(?string $query): array;

}