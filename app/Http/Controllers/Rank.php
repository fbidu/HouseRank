<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class Rank extends Controller
{
    /**
     * undocumented function
     *
     * @return void
     */
    public function searchMaps(Request $request)
    {
        $types = $request->input('types');
        $types = (implode(explode(",", $types), '|'));

        $x = $request->input('x');
        $y = $request->input('y');
        $r = $request->input('r');

        $key = env('MAPS_KEY');
        $client = new \GuzzleHttp\Client();
        $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$x,$y&radius=$r&types=$types&key=$key";
        $res = $client->request('GET', $url);
        return $res->getBody();
    }

    /**
     * undocumented function
     *
     * @return void
     */
    public function searchViva($x, $y, $r)
    {
        $key = env('VIVA_KEY');
        $client = new \GuzzleHttp\Client();
        $url = "http://api.vivareal.com/api/1.0/listings?lat=$x&long=$y&r=$r&maxResults=-1&portal=VR_BR&exactLocation=false&language=pt&apiKey=$key";
        $res = $client->request('GET', $url);
        return $res->getBody();
    }
}


