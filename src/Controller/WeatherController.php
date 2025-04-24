<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\WeatherService;

class WeatherController extends AbstractController
{
    #[Route('/weather/{city}', name: 'app_weather')]
    public function index(WeatherService $weatherService, string $city = 'London'): Response
    {
        $weatherData = $weatherService->getWeather($city);

        if (isset($weatherData['error'])) {
            return $this->render('weather/error.html.twig', [
                'error' => $weatherData['error']
            ]);
        }

        return $this->render('weather/index.html.twig', [
            'weather' => $weatherData,
        ]);
    }
}