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


        $client->request('GET', '/api/list?type=fruit');
        $response = $client->getResponse();


        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());


        $this->assertJson($response->getContent());


        $data = json_decode($response->getContent(), true);
        $this->assertNotEmpty($data);
    }

    public function testGetItemsWithInvalidType()
    {
        $client = static::createClient();


        $client->request('GET', '/api/list?type=invalid');
        $response = $client->getResponse();


        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());


        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertEquals("Invalid Type of item", $data['message']);
    }

    public function testGetItemsWithoutType()
    {
        $client = static::createClient();


        $client->request('GET', '/api/list');
        $response = $client->getResponse();


        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());


        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['success']);
        $this->assertStringContainsString('Invalid Type of item', $data['message']);
    }
}
