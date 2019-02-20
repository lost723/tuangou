<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Road extends Model
{
    //
    protected $fillable = ['parentid', 'leveltype', 'name', 'path', 'province', 'city', 'district', 'abbr'];
    # 通过城市id 获取街道列表

    public function getRoadsByParentId($id = 0)
    {
        if(0 >= $id) {
            return false;
        }
//        $result = $this->where('path','like',"%$id%")
//                ->where('leveltype', 4)
//                ->get(['id'])->implode('id',',');
//        if(!empty($result)) {
//            $result = explode(',',$result);
//        }



        $district_ids = $this->select('id')->where(function ($query) {
                    $query->where('parentid', 2);
                })->get()->toArray();
        $result = $this->whereIn('parentid',$district_ids)
            ->where('leveltype', 4)->get()->toArray();

        dump($result);die;
//        return $result;
    }

}
