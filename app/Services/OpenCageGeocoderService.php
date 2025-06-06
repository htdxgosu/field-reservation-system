<?php

namespace App\Services;

use GuzzleHttp\Client;

class OpenCageGeocoderService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('OPENCAGE_API_KEY');  // Lấy API key từ .env
    }

    public function geocodeAddress($location)
    {
        $url = 'https://api.opencagedata.com/geocode/v1/json';

        $response = $this->client->get($url, [
            'query' => [
                'q' => $location,
                'key' => $this->apiKey
            ]
        ]);

        $body = json_decode($response->getBody(), true);

        if (isset($body['results'][0]) && $body['status']['code'] == 200) {
            $location = $body['results'][0]['geometry'];
            return $location; // Trả về latitude và longitude
        } else {
            return null;
        }
        
    }
}

