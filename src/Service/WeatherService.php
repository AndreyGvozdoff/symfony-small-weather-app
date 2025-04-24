<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class WeatherService
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private string $apiKey;

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->apiKey = $apiKey;
    }

    public function getWeather(string $city): array
    {
        $url = sprintf('https://api.weatherapi.com/v1/current.json?key=%s&q=%s&lang=ru', $this->apiKey, urlencode($city));

        try {
            $response = $this->httpClient->request('GET', $url);
            $data = $response->toArray();

            $result = [
                'city' => $data['location']['name'],
                'country' => $data['location']['country'],
                'temperature' => $data['current']['temp_c'],
                'condition' => $data['current']['condition']['text'],
                'humidity' => $data['current']['humidity'],
                'wind_speed' => $data['current']['wind_kph'],
                'last_updated' => $data['current']['last_updated'],
            ];

            $this->logger->info(sprintf(
                'Погода в %s: %s°C, %s',
                $result['city'],
                $result['temperature'],
                $result['condition']
            ));

            return $result;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}