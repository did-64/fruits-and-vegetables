<?php

namespace App\Collection;

use App\Entity\FoodItem;

interface FoodCollectionInterface
{
    public function add(FoodItem $item): void;
    public function remove(string $name): void;
    public function list(): array;

}