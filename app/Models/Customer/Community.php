<?php

namespace App\Models\Customer;

use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;

class Community extends BaseModel
{
    protected $fillable = ['name', 'logo', 'road_id', 'address', 'longitude', 'latitude'];

    # 获取小区所属的街道
    public function road()
    {
        return $this->belongsTo('App\Models\Customer\Road', 'road_id', 'id');
    }

    # 通过城市id 获取小区列表
    static function getCommunityListByCityId($road_ids = [], $limit = 10)
    {
        $list = Community::whereIn('road_id', $road_ids)
            ->orderBy('road_id', 'asc')
            ->paginate($limit);
        return $list;
    }
    # 通过城市id 获取小区列表 启用经纬度协助查询
    static function getCommunityListByCityIdWithCoordinate($road_ids = [], $longitude, $latitude, $limit = 10)
    {
        $list = Community::whereIn('road_id', $road_ids)
            ->select(DB::raw("*, st_distance(point(longitude, latitude), 
                point($longitude, $latitude))/0.0111 as distance"))
            ->orderBy('distance', 'asc')
            ->paginate($limit);
        return $list;
    }
    # 通过经纬度获取周边小区列表
    static function getCommunityListByCoordinate($longitude, $latitude, $limit = 10)
    {
        $list = Community::select(DB::raw("*, st_distance(point(longitude, latitude), 
                point($longitude, $latitude))/0.0111 as distance"))
            ->orderBy('distance', 'asc')->paginate($limit);
        return $list;
    }
    # 模糊搜索小區 启用经纬度协助查询
    static function getCommunityByNameWithCoordinate($road_ids = [], $name, $longitude, $latitude, $limit = 10)
    {
        $list = Community::whereIn('road_id', $road_ids)
            ->where('name', 'like', "%$name%")
            ->select(DB::raw("*, st_distance(point(longitude, latitude), 
                point($longitude, $latitude))/0.0111 as distance"))
            ->orderBy('distance', 'asc')
            ->paginate($limit);
        return $list;
    }
    # 模糊搜索小區 启用经纬度协助查询
    static function getCommunityByName($road_ids = [], $name, $limit = 10)
    {
        $list = Community::whereIn('road_id', $road_ids)
            ->where('name', 'like', "%$name%")
            ->orderBy('road_id', 'asc')
            ->paginate($limit);
        return $list;
    }
    
}
