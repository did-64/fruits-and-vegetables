<?php

namespace App\Tests\App\Controller;

use App\Repository\VegetableRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApiFoodControllerTest extends WebTestCase
{
    public function testProcessJson(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/process-json');
        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $responseData = json_decode($responseContent, true);
        $this->assertTrue($responseData['success']);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testGetItems(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/list/fruit');
        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $data = json_decode($responseContent, true);
        $this->assertNotEmpty($data);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testAddItems()
    {
        $client = static::createClient();
        $json = json_encode([
            'name' => 'Grapefuit',
            'type' => 'fruit',
            'unit'=> 'kg',
            'quantity' => 10
        ]);
        $client->request('POST', '/api/create', [], [], ['CONTENT_TYPE' => 'application/json'], $json);
        $responseContent = $client->getResponse()->getContent();
        $data = json_decode($responseContent, true);
        $this->assertTrue($data['success']);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testRemoveItem()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $vegetableRepository = $container->get(VegetableRepository::class);
        $vegetable  = $vegetableRepository->findOneBy([], ['id' => 'DESC']);
        $idToRemove = $vegetable->getId();
        $client->request('DELETE', '/api/remove/vegetable/'.$idToRemove);
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
