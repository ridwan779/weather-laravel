## Setup Weather API
- Go to https://www.weatherapi.com/
- Register
- Take the API key
- Put the API key in the .env WEATHER_API_KEY

## Setup Queue Worker on Local
run `` php artisan queue:work ``

## Manualy dispatch welcome email
run `` php artisan email:send hello@example.com``