<?php
// tests/Controller/CountControllerTest.php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CountControllerTest extends WebTestCase
{
    public function testIndexWithOutDataCount(): void
    {
        $client = static::createClient();

        // Mock request with data
        $client->request('GET', '/count', [
            'serviceNames' => 'USER-SERVICE',
            'statusCode' => 200,
            'startDate' => '2024-07-18',
            'endDate' => '2024-07-19',
        ]);

        // Assert HTTP status code
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // Assert JSON response structure and content
        $content = json_decode($client->getResponse()->getContent(), true);
 
        $this->assertArrayHasKey('count', $content);
        $this->assertIsInt($content['count']); // Assuming count is returned as an integer
        $this->assertEquals(0, $content['count']);  
    }   
    
    public function testIndexWithDataCount(): void
    {
        $client = static::createClient();

        // Mock request with data
        $client->request('GET', '/count', [
            'serviceNames' => 'USER-SERVICE',
            'startDate' => '2024-07-18',
            'endDate' => '2024-07-19',
        ]);

        // Assert HTTP status code
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // Assert JSON response structure and content
        $content = json_decode($client->getResponse()->getContent(), true);
  
        $this->assertArrayHasKey('count', $content);
        $this->assertIsInt($content['count']); // Assuming count is returned as an integer
        $this->assertEquals(56, $content['count']);  
    }

    public function testIndexWithNoData(): void
    {
        $client = static::createClient();

        // Mock request with no query parameters
        $client->request('GET', '/count');

        // Assert HTTP status code
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // Assert JSON response for error handling
        $content = json_decode($client->getResponse()->getContent(), true);
 
         $this->assertEquals(80, $content['count']);  
    }

    protected static function getKernelClass(): string
    {
        return \App\Kernel::class; // Adjust as per your Symfony Kernel class location
    }
}
