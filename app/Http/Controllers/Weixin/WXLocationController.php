<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WXLocationController extends Controller
{

    # 地理坐标逆解析
    const GEOCODE_URL = 'https://apis.map.qq.com/ws/geocoder/v1/?location=';
    # 腾讯搜索
    const SEARCH_URL = 'https://apis.map.qq.com/ws/place/v1/search?';
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

    static function Search($name)
    {
        $longitude = request()->post('longitude');
        $latitude = request()->post('latitude');
        $url = self::SEARCH_URL."keyword=$name&boundary=nearby($latitude,$longitude,1000)&key=".config('wx.location.key')."&page_size=10";

        $result = self::http_get($url);
        $result = json_decode($result, true);

        if($result['status'] <> 0) {
            return [];
        }
        return $result['data'];

    }

}
