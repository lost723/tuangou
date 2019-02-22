<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Auth\CustomerController;
use App\Http\Controllers\Common\WXLocationController;
use App\Http\Resources\CommunityResource;
use App\Models\Customer\Community;
use App\Models\Customer\Road;


class CommunityController extends CustomerController
{
    # 小程序端 小区管理
    public function __construct()
    {
        $this->middleware('auth',  ['except' => ['CommunityList', 'searchCommunity' ]]);
    }

    # 我的小区信息
    public function myCommunity()
    {
        $customer = auth()->user();
        if(0 < $customer['community_id']) {
            $community = Community::find($customer['community_id']);
            if(!empty($community)) {
                return new CommunityResource($community);
            }
            return $this->ok(['data' => []]);
        }
        return $this->warning('请检查参数是否正确！');

    }

    # 通过腾讯地图api搜索附近小区
    public function searchCommunity()
    {
        $item = WXLocationController::Search('小区');
        if(!empty($item))
        {
            return $this->ok(['data' => $item]);
        }
        return $this->ok(['data' => []]);
    }

    /**
     * 用户关联小区
     * @param community_id 关联的小区id
     * @return \Illuminate\Http\JsonResponse
     */
    public function relateCommunity()
    {
        $community_id  = request()->post('community_id')?:0;
        if(0 < $community_id) {
            $customer = auth()->user();
            $customer->community_id = $community_id;
            if($customer->save()) {
                return $this->note('成功关联小区');
            }
            return $this->warning('关联小区失败');
        }
        return $this->warning('请检查传入参数是否正确');
    }

    # 通过地理位置定位附近的小区
    public function CommunityList()
    {
        $type = request()->post('type')?:1;
        switch ($type) {
            # 通过城市id获取小区列表
            case 1:
                $city_id = request()->post('city_id');
                $enable_coordinate = request()->post('enable_coordinate');
                $longitude = request()->post('longitude');
                $latitude  = request()->post('latitude');
                $city_id = request()->post('city_id');
                $limit = request()->post('limit')?: 10;
                $result = $this->getCommunityByCityId($city_id, $enable_coordinate, $longitude, $latitude, $limit);
                break;
            # 通过名称获取小区列表
            case 2:
                $city_id = request()->post('city_id');
                $name = request()->post('name');
                $enable_coordinate = request()->post('enable_coordinate');
                $longitude = request()->post('longitude');
                $latitude  = request()->post('latitude');
                $limit = request()->post('limit')?: 10;
                $result = $this->getCommunityByName($city_id, $name, $enable_coordinate, $longitude, $latitude, $limit);
                break;
            case 3:
                $longitude = request()->post('longitude');
                $latitude  = request()->post('latitude');
                $limit = request()->post('limit')?: 10;
                $result = $this->getCommunityByCoordinate($longitude, $latitude, $limit);
                break;
            default:
                return $this->warning('参数请求错误的');
        }

        if(empty($result)) {
            return $this->ok(['data' => []]);
        }
        else {
            return  CommunityResource::collection($result);
        }
    }

    /**
     * @param $id   城市id
     * @param $name 小区名称
     * @param $page 页码
     * @param $limit请求数量
     * @return mixed
     */
    # 模糊搜索小區
    public function getCommunityByName($id, $name, $enable_coordinate = false, $longitude, $latitude, $limit = 10)
    {
        # 获取 city 下的所有 街道id
        $road = new Road();
        $ids = $road->getRoadsByParentId($id);

        if(!$ids || empty($ids)) {
            return [];
        }


        if($enable_coordinate) {
            if(empty($longitude) || empty($latitude)) {
                return [];
            }
            $list = Community::getCommunityByNameWithCoordinate($ids, $name, $longitude, $latitude, $limit);
        }
        else {
            $list = Community::getCommunityByName($ids, $name, $limit);
        }

        return $list;
    }

    /**
     * 通过城市id 获取小区列表
     * @param $id 城市id
     * @param bool $enable_coordinate 是否启用经纬度查询
     * @param $longitude 经度
     * @param $latitude  纬度
     * @param int $limit 每页条数
     * @return bool
     */
    public function getCommunityByCityId($id, $enable_coordinate = false, $longitude , $latitude, $limit = 10)
    {
        # 获取 city 下的所有 街道id
        $ids = Road::getRoadsByParentId($id);

        if(!$ids || empty($ids)) {
            return [];
        }

        if($enable_coordinate) {
            if(empty($longitude) || empty($latitude)) {
                return [];
            }
            $list = Community::getCommunityListByCityIdWithCoordinate($ids, $longitude, $latitude, $limit);
        }
        else {
            $list = Community::getCommunityListByCityId($ids, $limit);
        }
        return $list;
    }

    /**
     * 通过经纬度获取周边小区列表
     * @param $longitude 经度
     * @param $latitude  纬度
     * @param int $limit 每页条数
     * @return mixed
     */
    public function getCommunityByCoordinate($longitude, $latitude, $limit = 10)
    {
        if(empty($longitude) || empty($latitude)) {
            return [];
        }
        $list = Community::getCommunityListByCoordinate($longitude, $latitude, $limit);
        return $list;
    }

}
