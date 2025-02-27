<?php

namespace App\Service;

use App\Collection\FoodCollectionManagerInterface;
use App\Exception\CustomHttpException;

class JsonFoodStorageService implements JsonStorageInterface
{
    public function __construct(private FoodCollectionManagerInterface $foodCollectionManager)
    {
    }

    public function loadData(string $jsonData): void
    {
        $data = json_decode($jsonData);
        if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
            throw new CustomHttpException("Invalid JSON data");
        }

        if(!is_array($data)) { $data = [$data]; }// it could be one entity to insert or many, if it's one, put it in an array to iterate on

        foreach ($data as $item) {
            $this->foodCollectionManager->addFood($item);
        }
    }
}