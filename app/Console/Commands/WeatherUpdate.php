<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use App\Helpers\Weather;

use Cache;

#[Signature('weather:update')]
#[Description('Update weather cache')]
class WeatherUpdate extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(Weather $weather)
    {
        $api = $weather->current('Perth');
            
        if (!$api['status']) {
            $this->error('Something went wrong!');
            return self::FAILURE;
        }

        Cache::put('weather', $api['data'], now()->plus(minutes: 15));

        $this->info('Successfully update cache weather!');
        return self::SUCCESS;
    }
}
