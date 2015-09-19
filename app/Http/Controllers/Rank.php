<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;

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
    public function getDistance($x1, $y1, $x2, $y2)
    {
        $key = env('MAPS_KEY');
        $client = new \GuzzleHttp\Client();
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$x1,$y1&destinations=$x2,$y2&key=$key";
        $response = $client->request('GET', $url);
        $response = json_decode($response->getBody());
        return $response->rows[0]->elements[0]->duration->value;
    }
    /**
     * undocumented function
     *
     * @return void
     */
    public function searchViva(Request $request)
    {
        $x = $request->input('x');
        $y = $request->input('y');
        $r = $request->input('r');

        $key = env('VIVA_KEY');
        $client = new \GuzzleHttp\Client();
        $url = "http://api.vivareal.com/api/1.0/listings?lat=$x&long=$y&r=$r&maxResults=-1&portal=VR_BR&exactLocation=false&language=pt&apiKey=$key";
        $res = $client->request('GET', $url);
        $response = json_decode($res->getBody());

        foreach ($response->listings as $item) {
            $item->code = 42;
        }
        return response()->json($response);
    }
}


