<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Road extends Model
{
    //
    protected $fillable = ['parentid', 'leveltype', 'name', 'path', 'province', 'city', 'district', 'abbr'];
    # 通过城市id 获取街道列表

    static public function getRoadsByParentId($id = 0)
    {
        if(0 >= $id) {
            return false;
        }
        # 获取区id[]
        $district_ids = self::select('id')->where(function ($query) {
                            $query->where('parentid', 2);
                         })->get()->toArray();
        # 获取街道id[]
        $result = self::whereIn('parentid',$district_ids)
                ->where('leveltype', 4)
                ->get(['id'])
                ->toArray();
        return $result;
    }

}
