<?php
namespace App\Helpers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class Weather {

    private $key;
    private $url;

    public function __construct()
    {
        $this->url = config('services.weather.url');
        $this->key = config('services.weather.key');
    }

    public function current($location)
    {
        try {
            $response = Http::acceptJson()->get($this->url, [
                'key' => $this->key,
                'q' => $location
            ]);
        } catch (ConnectionException) {
            return ['status' => false, 'data' => []];
        }
        
        
        if (!$response->successful()) {
            return ['status' => false, 'data' => []];
        }
        
        return ['status' => true, 'data' => $response->json()];
    }

}