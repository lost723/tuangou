<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Auth\CustomerController;
use App\Http\Resources\CommunityListResource;
use App\Http\Resources\CommunityResource;
use App\Models\Community;
use App\Models\Road;
use Illuminate\Support\Facades\DB;


class CommunityController extends CustomerController
{
    # 小程序端 小区管理
    public function __construct()
    {
        $this->middleware('auth',  ['except' => ['' ]]);
    }


    # 通过地理位置定位附近的小区
    public function CommunityList()
    {
        $type = request()->query('type')?:1;
        switch ($type) {
            # 通过城市id获取小区列表
            case 1:
                $city_id = request()->query('city_id');
                $enable_coordinate = request()->query('enable_coordinate');
                $longitude = request()->query('longitude');
                $latitude  = request()->query('latitude');
                $result = $this->getCommunityByCityId($city_id, $enable_coordinate, $longitude, $latitude );
                break;
            # 通过名称获取小区列表
            case 2:
                $city_id = request()->query('city_id');
                $name = request()->query('name');
                $enable_coordinate = request()->query('enable_coordinate');
                $longitude = request()->query('longitude');
                $latitude  = request()->query('latitude');
                $result = $this->getCommunityByName($city_id, $name, $enable_coordinate, $longitude, $latitude);
                break;
            case 3:
                $longitude = request()->query('longitude');
                $latitude  = request()->query('latitude');
                $result = $this->getCommunityByCoordinate($longitude, $latitude);
                break;
            default:
                return $this->warning('参数请求错误的');
        }
        if(!$result) {
            return $this->nocontent('该城市还未有小区开通！');
        }
        return new CommunityListResource($result);
    }

    /**
     * @param $id   城市id
     * @param $name 小区名称
     * @param $page 页码
     * @param $limit请求数量
     * @return bool
     */
    # 模糊搜索小區
    public function getCommunityByName($id, $name, $enable_coordinate = true, $longitude, $latitude, $limit = 10)
    {
        # 获取 city 下的所有 街道id
        $road = new Road();
        $result = $road->getRoadsByParentId($id);

        if(!$result || empty($result)) {
            return false;
        }

        if($enable_coordinate) {
            $list = Community::whereIn('road_id', $result)
                ->where('name', 'like', "%$name%")
                ->select(DB::raw("*, st_distance(point(longitude, latitude), 
                point($longitude, $latitude))/0.0111 as distance"))
                ->with('road')
                ->orderBy('distance', 'asc')
                ->paginate($limit);
        }
        else {
            $list = Community::whereIn('road_id', $result)
                ->where('name', 'like', "%$name%")
                ->with('road')
                ->orderBy('road_id', 'asc')
                ->paginate($limit);
        }

        return $list;
    }

    # 通过城市id 获取小区列表
    public function getCommunityByCityId($id, $enable_coordinate = true, $longitude , $latitude, $limit = 10)
    {
        # 获取 city 下的所有 街道id
        $result = Road::getRoadsByParentId($id);

        if(!$result || empty($result)) {
            return false;
        }

        if($enable_coordinate) {
            $list = Community::whereIn('road_id', $result)
                ->select(DB::raw("*, st_distance(point(longitude, latitude), 
                point($longitude, $latitude))/0.0111 as distance"))
                ->with('road')
                ->orderBy('distance', 'asc')
                ->paginate($limit);
        }
        else {
            $list = Community::whereIn('road_id', $result)
                ->with('road')
                ->orderBy('road_id', 'asc')
                ->paginate($limit);
        }
        return $list;
    }

    # 通过坐标获取附近小区列表
    public function getCommunityByCoordinate($longitude, $latitude, $page = 1 , $limit = 10)
    {

        $list = Community::select(DB::raw("*, st_distance(point(longitude, latitude), 
                point($longitude, $latitude))/0.0111 as distance"))
            ->with('road')
            ->orderBy('distance', 'asc')->paginate($limit);
        return $list;
    }

}
