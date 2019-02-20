<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Auth\CustomerController;
use App\Models\Community;
use App\Models\Road;


class CommunityController extends CustomerController
{
    # 小程序端 小区管理
    public function __construct()
    {
        $this->middleware('auth',  ['except' => ['CommunityList', ]]);
    }


    # 通过地理位置定位附近的小区

    public function CommunityList()
    {
        $type = request()->input('type')?:1;
        switch ($type) {
            # 通过城市id获取小区列表
            case 1:
                $city_id = request()->input('city_id');
                $page = request()->input('page');
                $result = $this->getCommunityByCityId($city_id, $page);
                break;
            # 通过名称获取小区列表
            case 2:
                $city_id = request()->input('city_id');
                $name = request()->input('name');
                $page = request()->input('page');
                $result = $this->getCommunityByName($city_id, $name, $page);
                break;
            case 3:
                $longitude = request()->input('longitude');
                $latitude  = request()->input('latitude');
                $page      = request()->input('page');
                $result = $this->getCommunityByCoordinate($longitude, $latitude, $page);
                break;
            default:
                return $this->warning('参数请求错误的');
        }
        if(!$result) {
            return $this->nocontent('该城市还未有小区开通！');
        }
        return $this->ok($result);
    }

    /**
     * @param $id   城市id
     * @param $name 小区名称
     * @param $page 页码
     * @param $limit请求数量
     * @return bool
     */
    # 模糊搜索小區
    public function getCommunityByName($id, $name, $page = 1, $limit = 10)
    {
        # 获取 city 下的所有 街道id
        $road = new Road();
        $result = $road->getRoadsByParentId($id);

        if(!$result || empty($result)) {
            return false;
        }
        $offset = ($page-1)*$limit;
        $list = Community::whereIn('road_id', $result)
            ->where('name', 'like', "%$name%")
            ->with(['road' => function ($query) {
                $query->select('id', 'name', 'province', 'city', 'district');
            }])
            ->select(['id', 'road_id', 'name', 'address'])
            ->offset($offset)
            ->limit($limit)
            ->get()->toArray();

        # 拼装返回数据
        foreach ($list as &$val) {
            $val['road'] = $val['road']['province'].$val['road']['city'].$val['road']['district'].$val['road']['name'];
        }

        return $list;
    }

    # 通过城市id 获取小区列表
    public function getCommunityByCityId($id, $page = 1, $limit = 10)
    {
        # 获取 city 下的所有 街道id
        $road = new Road();
        $result = $road->getRoadsByParentId($id);

        if(!$result || empty($result)) {
            return false;
        }

        $offset = $limit*($page-1);
        $list = Community::whereIn('road_id', $result)
                        ->with(['road' => function ($query) {
                            $query->select('id', 'name', 'province', 'city', 'district');
                        }])->offset($offset)
                        ->limit($limit)
                        ->select(['id', 'road_id', 'name', 'address'])
                        ->get()->toArray();

        # 拼装返回数据
        foreach ($list as &$val) {
            $val['road'] = $val['road']['province'].$val['road']['city'].$val['road']['district'].$val['road']['name'];
        }

        return $list;
    }

    # 通过坐标获取附近小区列表
    public function getCommunityByCoordinate($longitude, $latitude, $page = 1 , $limit = 10)
    {
        $community = new Community();
        $list =  $community->getListByCoordinate($longitude, $latitude, $page, $limit);

        if(!empty($list)) {
            foreach ($list as &$val) {
                $val = (array)$val;
                $val['road'] = $val['province'].$val['city'].$val['district'].$val['rname'];
                unset($val['province']);
                unset($val['city']);
                unset($val['district']);
                unset($val['rname']);
            }
            return $list;
        }
        else {
            return false;
        }
    }

}
