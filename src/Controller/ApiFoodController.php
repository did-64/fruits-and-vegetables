<?php

namespace App\Controller;

use App\Service\JsonFoodStorageService;
use App\Service\StorageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiFoodController extends AbstractController
{

    public function __construct(
        private JsonFoodStorageService $foodStorage,
    ) {}

    #[Route('/api/process-json', name: 'api_process_json', methods: ['GET'])]
    public function processJson(KernelInterface $kernel): JsonResponse
    {
        try {
            $jsonPath = $kernel->getProjectDir() . '/request.json';
            $jsonRequest = file_get_contents($jsonPath);
            $storageService = new StorageService($this->foodStorage);
            $storageService->setRequest($jsonRequest);
            $storageService->submitRequest();
        }catch (\Exception $exception){
            return $this->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], RESPONSE::HTTP_BAD_REQUEST);
        }
        return $this->json([
            'success' => true
        ], Response::HTTP_OK);
    }
}
