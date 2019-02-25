<?php

namespace App\Models\Customer;

use App\Models\BaseModel;

class Road extends BaseModel
{
    //
    protected $fillable = ['parentid', 'leveltype', 'name', 'path', 'province', 'city', 'district', 'abbr'];

    public function community()
    {
        return $this->hasMany('App\Models\Community','road_id','id');
    }

    # 通过城市id 获取街道列表
    static  function getRoadsByParentId($id = 0)
    {
        if(0 >= $id) {
            return false;
        }
        $result = self::whereIn('parentid',function($query) use($id) {
                    $query->select('id')
                        ->from(with(new Road)->getTable())
                        ->where('parentid', $id);
                })
                ->where('leveltype', 4)
                ->get(['id']);
        return $result;
    }

    # 通过road_id 获取城市信息
    static function getCityByRoadId($id)
    {
        $item = self::getParentItem($id);
        return self::getParentItem($item['id']);
    }

    # 获取上层城市信息
    static function getParentItem($id)
    {
        if(0 >= $id) {
            return [];
        }
        $item = self::find($id);
        $parent_item = self::find($item['parentid']);
        return $parent_item;
    }




}
