<?php

namespace App\Controller;

use App\Collection\FoodCollectionManagerInterface;
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
        private FoodCollectionManagerInterface $foodCollectionManager
    ) {}

    #[Route('/api/process-json', name: 'api_process_json', methods: ['GET'])]
    public function processJson(KernelInterface $kernel): JsonResponse
    {
        $jsonPath = $kernel->getProjectDir() . '/request.json';
        $jsonRequest = file_get_contents($jsonPath);
        $this->storageService->setRequest($jsonRequest);
        $this->storageService->submitRequest();
        return new JsonResponse([
            'success' => true
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/list/{type}', name: 'api_get_items', methods: ['GET'])]
    public function getItems(string $type, Request $request, SerializerInterface $serializer): JsonResponse
    {
        $query = $request->query->get('filter') ?: null;
        $list = $this->foodCollectionManager->listFood($type, $query);
        $jsonlist = $serializer->serialize($list, 'json');
        return new JsonResponse(
            $jsonlist
        , Response::HTTP_OK, [],true);
    }


    #[Route('/api/create', name: 'api_add_items', methods: ['POST'])]
    public function addItems(Request $request): JsonResponse
    {
        $content = $request->getContent();
        $this->storageService->setRequest($content);
        $this->storageService->submitRequest();
        return new JsonResponse([
            'success' => true
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/remove/{type}/{id}', name: 'api_remove_item', methods: ['DELETE'])]
    public function removeItem(int $id, string $type): Response
    {
        $this->foodCollectionManager->removeFood($type, $id);
        return new Response(
            null,
            Response::HTTP_NO_CONTENT);
    }
}
