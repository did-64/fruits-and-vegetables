<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
}
