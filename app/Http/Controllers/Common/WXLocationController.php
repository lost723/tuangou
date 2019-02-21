<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WXLocationController extends Controller
{


    const GEOCODE_URL = 'https://apis.map.qq.com/ws/geocoder/v1/?location=';

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['getLocation']]);
    }
    static function getLocation()
    {
        $longitude = request()->post('longitude');
        $latitude = request()->post('latitude');
        $url = self::GEOCODE_URL."$latitude,$longitude&key=".config('wx.location.key');
        $result = self::http_get($url);
        $result = json_decode($result, true);
        if($result['status'] <> 0) {
            return [];
        }
        return $result['result']['ad_info'];

    }
}
