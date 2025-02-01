<?php

namespace App\Controller;

use App\Collection\FruitCollection;
use App\Collection\VegetableCollection;
use App\Service\JsonFoodStorageService;
use App\Service\StorageService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiFoodController extends AbstractController
{

    public function __construct(
        private JsonFoodStorageService $foodStorage,
        private FruitCollection $fruitCollection,
        private VegetableCollection $vegetableCollection
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
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage()
            ], RESPONSE::HTTP_BAD_REQUEST);
        }
        return new JsonResponse([
            'success' => true
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/list/{type}', name: 'api_get_items', methods: ['GET'])]
    public function getItems(string $type, Request $request , SerializerInterface $serializer): JsonResponse
    {
        try {
            $list =  match ($type) {
                'fruit' => $this->fruitCollection->list(),
                'vegetable' => $this->vegetableCollection->list(),
                default => null,
            };
            if($list === null) {
                throw new \InvalidArgumentException("Invalid Type of item");
            }
            $jsonlist = $serializer->serialize($list, 'json');

        }catch (\Exception $exception){
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage()
            ], RESPONSE::HTTP_BAD_REQUEST);
        }
        return new JsonResponse(
            $jsonlist
        , Response::HTTP_OK, [],true);
    }


    #[Route('/api/create', name: 'api_add_items', methods: ['POST'])]
    public function addItems(Request $request): JsonResponse
    {
        try {
            $content = $request->getContent();

            if (!$content || !json_decode($content)) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Invalid JSON format'
                ], RESPONSE::HTTP_BAD_REQUEST);
            }

            $storageService = new StorageService($this->foodStorage);
            $storageService->setRequest($content);
            $storageService->submitRequest();
        }catch (\Exception $exception){
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage()
            ], RESPONSE::HTTP_BAD_REQUEST);
        }
        return new JsonResponse([
            'success' => true
        ], Response::HTTP_CREATED);
    }
}
