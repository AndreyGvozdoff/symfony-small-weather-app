<?php
namespace App\Tests\Service;

use App\Service\WeatherService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Psr\Log\NullLogger;

class WeatherServiceTest extends TestCase
{
    public function testGetWeatherReturnsExpectedData()
    {
        $mockResponseData = [
            'location' => [
                'name' => 'London',
                'country' => 'GreatBritain'
            ],
            'current' => [
                'temp_c' => 5,
                'condition' => ['text' => 'Cloudy'],
                'humidity' => 80,
                'wind_kph' => 15,
                'last_updated' => '2025-04-23 12:00'
            ]
        ];

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('toArray')->willReturn($mockResponseData);

        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')->willReturn($mockResponse);

        $weatherService = new WeatherService($mockHttpClient, new NullLogger(), 'fake_api_key');

        $result = $weatherService->getWeather('London');

        $this->assertIsArray($result);
        $this->assertEquals('London', $result['city']);
        $this->assertEquals('GreatBritain', $result['country']);
        $this->assertEquals(5, $result['temperature']);
        $this->assertEquals('Cloudy', $result['condition']);
        $this->assertEquals(80, $result['humidity']);
        $this->assertEquals(15, $result['wind_speed']);
        $this->assertEquals('2025-04-23 12:00', $result['last_updated']);
    }

    public function testGetWeatherHandlesException()
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')->willThrowException(new \Exception('API Error'));

        $weatherService = new WeatherService($mockHttpClient, new NullLogger(), 'fake_api_key');
        $result = $weatherService->getWeather('Nowhere');

        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('API Error', $result['error']);
    }
}