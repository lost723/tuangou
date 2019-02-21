<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Auth\CustomerController;
use App\Http\Controllers\Common\WXLocationController;
use App\Http\Resources\RoadResource;
use App\Models\Road;

class RoadController extends CustomerController
{
    # 小程序端街道管理
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['listCity', 'myCity']]);
    }

    # 坐标获取城市信息
    # 使用坐标通过腾讯地图获取城市信息
    public function myCity()
    {
        # 获取当前城市
        $location = WXLocationController::getLocation();
        if(!empty($location)) {
            $item = Road::where('name', $location['city'])->first();
        }
        if(!empty($item)) {
            return new RoadResource($item);
        }
        return $this->ok(['data' => []]);
    }

    # 获取城市列表
    public function listCity()
    {
        $city = Road::where('leveltype', 2)->orderBy('abbr', 'asc')->get()->groupby('abbr')->toArray();
        if(empty($city)) {
            return $this->warning('城市跑丢了。。');
        }

        $list = [];
        foreach ($city as $key=>$val) {
            $tmp['letter'] = $key;
            $tmp['list'] = [];
            foreach ($val as $k=>$v) {
                array_push($tmp['list'], ['id' => $v['id'], 'name' => $v['name']]);
            }
            array_push($list, $tmp);
            unset($tmp);
        }
        unset($city);
        return $this->ok($list);
    }
}
