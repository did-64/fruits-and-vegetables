<?php

namespace App\Collection;

interface FoodCollectionManagerInterface
{
    public function listFood(string $itemType, ?string $query): array;
    public function addCollection(): void;
    public function hydrateCollection(array $data): void;
    public function removeFood(string $itemType, int $id): void;
}