## Setup Weather API
- Go to https://www.weatherapi.com/
- Register
- Get your API key
- Add the API key to `.env` as `WEATHER_API_KEY`

## Setup Queue Worker Locally
Run: `php artisan queue:work`

## Manually Dispatch Welcome Email
Run: `php artisan email:send hello@example.com`