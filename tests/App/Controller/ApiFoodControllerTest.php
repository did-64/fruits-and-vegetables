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
        $responseData = json_decode($responseContent, true);
        $this->assertNotEmpty($responseData);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testGetNonExistentEntity(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/list/cereal');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testAddItems(): void
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
        $this->assertJson($responseContent);
        $responseData = json_decode($responseContent, true);
        $this->assertTrue($responseData['success']);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testAddNonExistentEntity(): void
    {
        $client = static::createClient();
        $json = json_encode([
            'name' => 'Grapefuit',
            'type' => 'beverage',
            'unit'=> 'kg',
            'quantity' => 10
        ]);
        $client->request('POST', '/api/create', [], [], ['CONTENT_TYPE' => 'application/json'], $json);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testAddNonExistentUnit(): void
    {
        $client = static::createClient();
        $json = json_encode([
            'name' => 'Grapefuit',
            'type' => 'fruit',
            'unit'=> 'cm',
            'quantity' => 10
        ]);
        $client->request('POST', '/api/create', [], [], ['CONTENT_TYPE' => 'application/json'], $json);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testRemoveItem(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $vegetableRepository = $container->get(VegetableRepository::class);
        $vegetable  = $vegetableRepository->findOneBy([], ['id' => 'DESC']);
        $idToRemove = $vegetable->getId();
        $client->request('DELETE', '/api/remove/vegetable/'.$idToRemove);
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testRemoveNonExistentItem(): void
    {
        $client = static::createClient();
        $nonExistentId = 99999;
        $client->request('DELETE', '/api/remove/vegetable/'.$nonExistentId);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testRemoveNonExistentEntity(): void
    {
        $client = static::createClient();
        $id = 1;
        $client->request('DELETE', '/api/remove/cereal/'.$id);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
