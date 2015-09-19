<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;

class Rank extends Controller
{

    private $GOOGLE_TYPES = [
        'accounting',
        'airport',
        'amusement_park',
        'aquarium',
        'art_gallery',
        'atm',
        'bakery',
        'bank',
        'bar',
        'beauty_salon',
        'bicycle_store',
        'book_store',
        'bowling_alley',
        'bus_station',
        'cafe',
        'campground',
        'car_dealer',
        'car_rental',
        'car_repair',
        'car_wash',
        'casino',
        'cemetery',
        'church',
        'city_hall',
        'clothing_store',
        'convenience_store',
        'courthouse',
        'dentist',
        'department_store',
        'doctor',
        'electrician',
        'electronics_store',
        'embassy',
        'establishment',
        'finance',
        'fire_station',
        'florist',
        'food',
        'funeral_home',
        'furniture_store',
        'gas_station',
        'general_contractor',
        'grocery_or_supermarket',
        'gym',
        'hair_care',
        'hardware_store',
        'health',
        'hindu_temple',
        'home_goods_store',
        'hospital',
        'insurance_agency',
        'jewelry_store',
        'laundry',
        'lawyer',
        'library',
        'liquor_store',
        'local_government_office',
        'locksmith',
        'lodging',
        'meal_delivery',
        'meal_takeaway',
        'mosque',
        'movie_rental',
        'movie_theater',
        'moving_company',
        'museum',
        'night_club',
        'painter',
        'park',
        'parking',
        'pet_store',
        'pharmacy',
        'physiotherapist',
        'place_of_worship',
        'plumber',
        'police',
        'post_office',
        'real_estate_agency',
        'restaurant',
        'roofing_contractor',
        'rv_park',
        'school',
        'shoe_store',
        'shopping_mall',
        'spa',
        'stadium',
        'storage',
        'store',
        'subway_station',
        'synagogue',
        'taxi_stand',
        'train_station',
        'travel_agency',
        'university',
        'veterinary_care',
        'zoo'
    ];

public function getDistance($lat1, $lon1, $lat2, $lon2) {

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;

 return ($miles * 1.609344);

}
    /**
     * undocumented function
     *
     * @return void
     */
    public function searchMaps(Request $request)
    {
        $types = $request->input('types');

        $x = $request->input('x');
        $y = $request->input('y');
        $r = $request->input('r');

        return $this->searchStaticMaps($x, $y, $r, $types);
    }

    public function searchStaticMaps($x, $y, $r, $types)
    {
        #$types = (implode(explode(",", $types), '|'));

        $key = env('MAPS_KEY');
        $client = new \GuzzleHttp\Client();
        $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$x,$y&radius=$r&types=$types&key=$key";
        $res = $client->request('GET', $url);
        return json_decode($res->getBody(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    }

    // public static function getDistance($x1, $y1, $x2, $y2)
    // {
    //     $key = env('MAPS_KEY');
    //     $client = new \GuzzleHttp\Client();
    //     $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$x1,$y1&destinations=$x2,$y2&key=$key";
    //     $response = $client->request('GET', $url);
    //     $response = json_decode($response->getBody(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    //     if (isset($response->rows[0]->elements[0]->duration->value))
    //     {
    //     	return $response->rows[0]->elements[0]->duration->value;
    //     }
    //     else
    //     {
    //     	return -1;
    //     }
    // }


    public function geocode(Request $request)
    {
    	$name = $request->input('name');
        $key = env('MAPS_KEY');
        $client = new \GuzzleHttp\Client();
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$name&key=$key";
        $response = $client->request('GET', $url);
        $response = $response->getBody();
        $response = json_decode($response, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        return $response['results'][0]['geometry']['location'];
    }

    public function searchViva(Request $request)
    {
        $x = $request->input('x');
        $y = $request->input('y');
        $r = $request->input('r');
        return $this->searchStaticViva($x, $y, $r);
    }

    public function searchStaticViva($x, $y, $r, $business = 'VENTA')
    {
        $key = env('VIVA_KEY');
        $client = new \GuzzleHttp\Client();
        $url = "http://api.vivareal.com/api/1.0/listings?lat=$x&long=$y&r=$r&maxResults=-1&portal=VR_BR&business=$business&exactLocation=false&language=pt&apiKey=$key";
        $res = $client->request('GET', $url);
        return json_decode($res->getBody(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    }


    public function findClosest($x, $y, $results = [])
    {
    	$closest = 99999999;
    	foreach ($results as $location)
    	{
    		$lat = (float) $location['geometry']['location']['lat'];
    		$lng = (float) $location['geometry']['location']['lng'];
    		$distance = $this->getDistance($lat, $lng, (float) $x, (float) $y);
    		if($distance < $closest)
    		{
    			$closest = $distance;
    		}
    	}

    	return $closest;
    }

public function cmp($a, $b)
{
    if ($a['score'] == $b['score']) {
        return 0;
    }
    return ($a['score'] < $b['score']) ? -1 : 1;
}
    public function search(Request $request)
    {
        $x = $request->input('x');
        $y = $request->input('y');
        $r = $request->input('r');
        $types = $request->input('types');
        $weights = $request->input('weights');
        $wx = $request->input('wx');
        $wy = $request->input('wy');
        $wp = $request->input('wp');
        $business = $request->input('business');
        $imoveis = $this->searchStaticViva($x, $y, $r, $business)['listings'];
        $types = explode(',', $types);
        $weights = explode(',', $weights);

        $surroudings = [];

		for($i = 0; $i < sizeof($imoveis); $i++)
        {
            if (isset($wx, $wy, $wp))
            {
        	    $imoveis[$i]['score'] = $wp * 1/$this->getDistance($imoveis[$i]['latitude'], $imoveis[$i]['longitude'], $wx, $wy);
            }
            else
            {
                $imoveis[$i]['score'] = 0;
            }
        }

        foreach ($types as $type) {
        	if (in_array($type, $this->GOOGLE_TYPES))
        	{
            	$surroudings[$type] = $this->searchStaticMaps($x, $y, $r+1000, $type)['results'];
            	for($i = 0; $i < sizeof($imoveis); $i++)
        		{
                    $imoveis[$i]['score']  += $weights[array_search($type, $types)] * 1/$this->findClosest($imoveis[$i]['latitude'], $imoveis[$i]['longitude'], $surroudings[$type]);
        		}
        	}
        	else
        	{
        		$surroudings[$type] = [];
        	}
        }

        $imoveis = array_values(array_sort($imoveis, function($value)
		{
    		return (float) $value['score'];
		}));
		return array_reverse($imoveis);
    }
}


