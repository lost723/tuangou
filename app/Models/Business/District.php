<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    //


    /**
     * 获取某个区域模版附带所有小区详情
     * @param $id
     * @return mixed
     */
    static function  findWithItmes($id)
    {
        $obj = self::find($id);
        # todo 需要关联小区表
        $obj->items = DistrictItem::where('distid', $id)->get();
        return $obj;
    }
}
