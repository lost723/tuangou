<?php
namespace App\Utils;

trait TencentLBS{
    # 地理坐标逆解析
    static function getLocation()
    {
        $geocodeUrl = 'https://apis.map.qq.com/ws/geocoder/v1/?location=';
        $longitude = request()->post('longitude');
        $latitude = request()->post('latitude');
        $url = $geocodeUrl."$latitude,$longitude&key=".config('wx.location.key');
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
        $searchUrl = 'https://apis.map.qq.com/ws/place/v1/search?';
        $url = self::SEARCH_URL."keyword=$name&boundary=nearby($latitude,$longitude,1000)&key=".config('wx.location.key')."&page_size=10";

        $result = self::http_get($url);
        $result = json_decode($result, true);

        if($result['status'] <> 0) {
            return [];
        }
        return $result['data'];

    }
}