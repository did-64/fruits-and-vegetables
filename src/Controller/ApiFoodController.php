<?php

namespace App\Controller;

use App\Collection\FruitCollection;
use App\Collection\VegetableCollection;
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
        private StorageService $storageService,
        private FruitCollection $fruitCollection,
        private VegetableCollection $vegetableCollection
    ) {}

    #[Route('/api/process-json', name: 'api_process_json', methods: ['GET'])]
    public function processJson(KernelInterface $kernel): JsonResponse
    {
        try {
            $jsonPath = $kernel->getProjectDir() . '/request.json';
            $jsonRequest = file_get_contents($jsonPath);
            $this->storageService->setRequest($jsonRequest);
            $this->storageService->submitRequest();
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
    public function getItems(string $type, Request $request, SerializerInterface $serializer): JsonResponse
    {
        try {
            $filter = $request->query->get('filter') ?: null;
            $list =  match ($type) {
                'fruit' => $this->fruitCollection->list($filter),
                'vegetable' => $this->vegetableCollection->list($filter),
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

            $this->storageService->setRequest($content);
            $this->storageService->submitRequest();
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

    #[Route('/api/remove/{type}/{id}', name: 'api_remove_item', methods: ['DELETE'])]
    public function removeItem(int $id, string $type): JsonResponse
    {
        try {
            if (!in_array($type, ['fruit', 'vegetable'])) {
                throw new \InvalidArgumentException("Invalid Type of item");
            }

            match ($type) {
                'fruit' => $this->fruitCollection->remove($id),
                'vegetable' => $this->vegetableCollection->remove($id),
                default => null,
            };

        }catch (\Exception $exception){
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage()
            ], RESPONSE::HTTP_BAD_REQUEST);
        }
        return new JsonResponse([
            'success' => true
        ], Response::HTTP_NO_CONTENT);
    }
}
