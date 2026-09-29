<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\Weather;

use Cache;

class WeatherController extends Controller
{
    public function getWeather(Weather $weather)
    {
        $data = [];
        if (!Cache::has('weather')) {
            $api = $weather->current('Perth');
            
            if (!$api['status']) {
                return response()->json(['message' => 'Failed to retrieve weather data'], 502);
            }

            $data = $api['data'];

            Cache::put('weather', $data, now()->plus(minutes: 15));
        } else {
            $data = Cache::get('weather');
        }

        return response()->json($data);
    }
}
