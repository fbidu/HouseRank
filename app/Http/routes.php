<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/google/{x}/{y}/{r}', function($x, $y, $r)
    {
        $key = env('MAPS_KEY');
        $client = new GuzzleHttp\Client();
        $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$x,$y&radius=$r&key=$key";
        $res = $client->request('GET', $url);
        return $res->getBody();
});
