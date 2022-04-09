<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Http;

class AddressController extends ApiController
{

    /** 
     * Get lat/long from address string request
     * @param Request $request The request containing the string of the address to find
     * @return  json returns lat/long information about location entered
     */
    function sendAddressRequest(Request $request)
    {
        if ($this->overLimit()) {
            $limiter = $this->getLimiter();
            $timeUntilAvailable = $limiter->availableIn($this->getKey()) + 1;
            sleep($timeUntilAvailable);
            return $this->sendAddressRequest($request);
        }
        $query = $request->address;
        $requestUrl = "https://nominatim.openstreetmap.org/search.php?q=" . $query . "&countrycodes=ca&limit=1&format=jsonv2";

        $response = Http::get($requestUrl);
        $lat = $response->json()[0]["lat"];
        $long = $response->json()[0]["lon"];


        $this->getLimiter()->hit(
            $this->getKey(),
            1 //1 request per second
        );

        if ($response->successful()) {
            return $this->respondWithData(['lat' => $lat, 'long' => $long], $response->status());
        } else {
            return $this->respondWithError($response->body(), $response->status());
        }
    }


    function getLimiter()
    {
        return app(RateLimiter::class);
    }

    function overLimit()
    {
        $limiter = $this->getLimiter();
        return $limiter->tooManyAttempts(
            $this->getKey(),
            25
        );
    }

    function getKey()
    {
        return "key";
    }
}
