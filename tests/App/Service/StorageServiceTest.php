<?php

namespace App\Tests\App\Service;

use App\Service\JsonFoodStorageService;
use App\Service\JsonStorageInterface;
use App\Service\StorageService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StorageServiceTest extends TestCase
{

    private StorageService $storageService;

    protected function setUp(): void
    {
        $jsonStorageMock = $this->createMock(JsonStorageInterface::class);

        $this->storageService = new StorageService($jsonStorageMock);
    }
    public function testReceivingRequest(): void
    {
        $request = file_get_contents('request.json');
        $this->storageService->setRequest($request);

        $this->assertNotEmpty($this->storageService->getRequest());
        $this->assertIsString($this->storageService->getRequest());
    }
    
}
