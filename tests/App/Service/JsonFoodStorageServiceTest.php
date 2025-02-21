<?php

namespace App\Tests\App\Service;

use App\Collection\FoodCollectionManagerInterface;
use App\Exception\CustomHttpException;
use App\Service\JsonFoodStorageService;
use PHPUnit\Framework\TestCase;

class JsonFoodStorageServiceTest extends TestCase
{

    private JsonFoodStorageService $jsonFoodStorageService;

    protected function setUp(): void
    {
        $manager = $this->createMock(FoodCollectionManagerInterface::class);
        $this->jsonFoodStorageService = new JsonFoodStorageService($manager);
    }

    public function testLoadDataException(){
        $data = '';
        $this->expectException(CustomHttpException::class);
        $this->expectExceptionMessage("Invalid JSON data");
        $this->jsonFoodStorageService->loadData($data);

        $data = '[{"id": 1,"name": "Carrot","type": "vegetable","quantity": 10922,"unit": "g"}';
        $this->expectException(CustomHttpException::class);
        $this->expectExceptionMessage("Invalid JSON data");
        $this->jsonFoodStorageService->loadData($data);
    }

}