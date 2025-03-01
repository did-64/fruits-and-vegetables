<?php

namespace App\Tests\App\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApiFoodControllerTest extends WebTestCase
{
    public function testProcessJson(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/process-json');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('success', $responseData);
        $this->assertTrue($responseData['success']);
    }

    public function testGetItemsWithValidType()
    {

        $client = static::createClient();


        $client->request('GET', '/api/list/fruit');
        $response = $client->getResponse();


        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());


        $this->assertJson($response->getContent());


        $data = json_decode($response->getContent(), true);
        $this->assertNotEmpty($data);
    }

    public function testGetItemsWithInvalidType()
    {
        $client = static::createClient();


        $client->request('GET', '/api/list/invalid');
        $response = $client->getResponse();


        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());


        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals("Invalid Type of item", $data['message']);
    }

    public function testGetItemsWithFilter(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/list/fruit?filter=red');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
    }


    public function testAddItemsWithValidJson()
    {
        $client = static::createClient();


        $validJson = json_encode([
            'name' => 'Apple',
            'type' => 'fruit',
            'unit'=> 'kg',
            'quantity' => 10
        ]);


        $client->request('POST', '/api/create', [], [], ['CONTENT_TYPE' => 'application/json'], $validJson);
        $response = $client->getResponse();


        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());


        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['success']);
    }

    public function testAddItemsWithInvalidJson()
    {
        $client = static::createClient();


        $invalidJson = '{"name": "Apple", "type": "fruit", "quantity": 10';


        $client->request('POST', '/api/create', [], [], ['CONTENT_TYPE' => 'application/json'], $invalidJson);
        $response = $client->getResponse();


        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());


        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Invalid JSON format', $data['error']);
    }

    public function testAddItemsWithEmptyContent()
    {
        $client = static::createClient();


        $client->request('POST', '/api/create');
        $response = $client->getResponse();


        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());


        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals('Invalid JSON format', $data['error']);
    }


    public function testRemoveItemSuccess(): void
    {
        $client = static::createClient();


        $client->request('DELETE', '/api/remove/fruit/10');

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

    }

    public function testRemoveItemInvalidType(): void
    {
        $client = static::createClient();


        $client->request('DELETE', '/api/remove/meat/1');

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

    }
}
